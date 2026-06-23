#!/usr/bin/env python3
"""Convertit un fichier Excel d'adresses en fichier GPX (waypoints + tracé routier).

Pipeline utilisé pour `data/adresses fleurs.xlsx` et `data/ParticpantsFACADES2026.xls` :

  1. Lecture du tableur (.xls via xlrd, .xlsx via openpyxl si disponible).
  2. Géocodage de chaque adresse via Nominatim (OpenStreetMap) avec :
       - cascade de requêtes (recherche structurée -> requêtes libres -> centre de rue) ;
       - validation par distance au centre de la commune (rejette les homonymes
         d'une autre localité, ex. "Route de Bastogne" à Harsin/Nassogne, ~15 km) ;
       - cache disque pour pouvoir relancer sans re-télécharger.
  3. Surcharges manuelles (OVERRIDES) pour les adresses introuvables/mal nommées
     dans OSM (ex. l'orthographe "Route" vs "Rue de Bastogne").
  4. Calcul d'un itinéraire routier passant par tous les arrêts dans l'ordre, via OSRM.
  5. Écriture d'un GPX (waypoints numérotés + <trk> + distance totale).

Bonnes pratiques Nominatim respectées : User-Agent identifiant, 1 requête/seconde max.

Usage :
    python xls_to_gpx.py <fichier.xls> <sortie.gpx> [--title "Mon itinéraire"]

Le parsing des colonnes (RUE / CP / Localité) est propre au format "Façades".
Adaptez parse_rows() pour un autre tableur.
"""
from __future__ import annotations

import argparse
import json
import math
import os
import re
import sys
import time
import urllib.parse
import urllib.request

# --- Configuration géographique (commune de Marche-en-Famenne) ---------------
COMMUNE_CENTER = (50.2270, 5.3430)   # lat, lon — centre approximatif
MAX_DISTANCE_KM = 15                 # rejette tout résultat plus loin que ça
DEFAULT_POSTCODE = "6900"

NOMINATIM = "https://nominatim.openstreetmap.org/search"
OSRM = "http://router.project-osrm.org/route/v1/driving/"
USER_AGENT = "visit-marche-geocode/1.0 (jfsenechal@gmail.com)"
RATE_LIMIT_S = 1.1                   # politesse Nominatim : 1 req/s

# Surcharges manuelles : clé = (rue, localité) en minuscules.
# Pour les adresses absentes ou mal nommées dans OpenStreetMap.
OVERRIDES: dict[tuple[str, str], tuple[float, float, str]] = {
    # rue/localité (lower)                  -> (lat, lon, note)
    ("route de bastogne, 18", "hollogne"): (50.2191403, 5.3483389, "OSM: 'Rue de Bastogne'"),
    ("route de bastogne, 19", "hollogne"): (50.2191874, 5.3489722, "OSM: 'Rue de Bastogne'"),
    ("rue de bande, 17", "roy"):           (50.1871000, 5.4080000, "approx: rue absente d'OSM, centre de Roy"),
    ("place de l'église, 1", "waha"):      (50.2122748, 5.3435925, "approx: Église Saint-Étienne"),
    ("aux 3 sapins, 16", "waha"):          (50.2046637, 5.3411930, "approx: centre de rue"),
}


def haversine(a: tuple[float, float], b: tuple[float, float]) -> float:
    r = 6371.0
    la1, lo1, la2, lo2 = map(math.radians, [a[0], a[1], b[0], b[1]])
    h = math.sin((la2 - la1) / 2) ** 2 + math.cos(la1) * math.cos(la2) * math.sin((lo2 - lo1) / 2) ** 2
    return 2 * r * math.asin(math.sqrt(h))


class Geocoder:
    """Géocodeur Nominatim avec cache disque et validation par distance."""

    def __init__(self, cache_path: str):
        self.cache_path = cache_path
        self.cache = json.load(open(cache_path)) if os.path.exists(cache_path) else {}

    def _query(self, params: dict) -> list:
        url = NOMINATIM + "?" + urllib.parse.urlencode(params)
        if url in self.cache:
            return self.cache[url]
        time.sleep(RATE_LIMIT_S)
        try:
            req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
            data = json.load(urllib.request.urlopen(req, timeout=30))
        except Exception as exc:  # noqa: BLE001 — on tolère les erreurs réseau ponctuelles
            print(f"  ! requête échouée: {exc}", file=sys.stderr)
            data = []
        self.cache[url] = data
        json.dump(self.cache, open(self.cache_path, "w"))
        return data

    def _first_in_range(self, results: list) -> tuple[float, float] | None:
        for x in results:
            p = (float(x["lat"]), float(x["lon"]))
            if haversine(p, COMMUNE_CENTER) <= MAX_DISTANCE_KM:
                return p
        return None

    def geocode(self, street: str, num: str, locality: str) -> tuple[tuple[float, float] | None, bool]:
        """Retourne ((lat, lon) | None, exact?). exact=True si le numéro de maison a été matché."""
        base = {"format": "json", "limit": "5", "countrycodes": "be"}
        queries = []
        if num:
            queries.append({**base, "street": f"{num} {street}", "city": locality, "postalcode": DEFAULT_POSTCODE})
            queries.append({**base, "q": f"{street} {num}, {locality}, Marche-en-Famenne, Belgique"})
            queries.append({**base, "q": f"{street} {num}, {DEFAULT_POSTCODE} {locality}, Belgique"})
        n_exact = len(queries)
        # Repli sans numéro : centre de la rue
        queries.append({**base, "street": street, "city": locality, "postalcode": DEFAULT_POSTCODE})
        queries.append({**base, "q": f"{street}, {locality}, Marche-en-Famenne, Belgique"})
        for i, q in enumerate(queries):
            p = self._first_in_range(self._query(q))
            if p:
                return p, i < n_exact
        return None, False


def parse_rows(xls_path: str) -> list[dict]:
    """Parse le format 'Façades' : colonnes Titre/Nom/Prénom/Rue/CP/Localité.

    Ignore les en-têtes de section, la ligne 'PAUSE REPAS' et les lignes vides.
    """
    import xlrd

    sheet = xlrd.open_workbook(xls_path).sheet_by_index(0)
    rows = []
    for r in range(5, sheet.nrows):
        rue = str(sheet.cell_value(r, 4)).strip()
        loc = str(sheet.cell_value(r, 6)).strip()
        if not rue or rue.lower() == "rue":
            continue
        rue = rue.split("\n")[0].strip()           # ex. ligne 47 : double adresse -> 1re
        nom = str(sheet.cell_value(r, 2)).strip()
        pre = str(sheet.cell_value(r, 3)).strip()
        if "," in rue:
            street, num = (s.strip() for s in rue.rsplit(",", 1))
        else:
            m = re.search(r"^(.*?)(\d+\w*)\s*$", rue)
            street, num = (m.group(1).strip(), m.group(2).strip()) if m else (rue, "")
        rows.append({"row": r, "street": street, "num": num, "loc": loc,
                     "nom": nom, "pre": pre, "rue": rue})
    return rows


def osrm_route(stops: list[dict]) -> tuple[list[tuple[float, float]], float]:
    """Itinéraire routier passant par tous les arrêts (ordre conservé). Retourne (geom, km)."""
    coords = ";".join(f'{s["lon"]},{s["lat"]}' for s in stops)
    url = f"{OSRM}{coords}?overview=full&geometries=geojson"
    req = urllib.request.Request(url, headers={"User-Agent": "visit-marche-gpx/1.0"})
    res = json.load(urllib.request.urlopen(req, timeout=90))
    if res.get("code") != "Ok":
        raise RuntimeError(f"OSRM error: {res.get('code')} {res.get('message')}")
    route = res["routes"][0]
    return route["geometry"]["coordinates"], route["distance"] / 1000.0


def xml_escape(s: str) -> str:
    return s.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")


def write_gpx(path: str, title: str, stops: list[dict], geom: list, km: float) -> None:
    out = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<gpx version="1.1" creator="visit-marche-gpx" xmlns="http://www.topografix.com/GPX/1/1">',
        f'  <metadata><name>{xml_escape(title)}</name><desc>{km:.1f} km - {len(stops)} arrêts</desc></metadata>',
    ]
    for i, s in enumerate(stops, 1):
        who = " ".join(x for x in [s.get("nom", ""), s.get("pre", "")] if x)
        name = xml_escape(f'{i:02d}. {s["rue"]}, {DEFAULT_POSTCODE} {s["loc"]}' + (f" ({who})" if who else ""))
        out.append(f'  <wpt lat="{s["lat"]:.7f}" lon="{s["lon"]:.7f}"><name>{name}</name></wpt>')
    out.append(f'  <trk><name>{xml_escape(title)}</name><trkseg>')
    for lon, lat in geom:
        out.append(f'    <trkpt lat="{lat:.7f}" lon="{lon:.7f}"></trkpt>')
    out.append("  </trkseg></trk>")
    out.append("</gpx>")
    open(path, "w", encoding="utf-8").write("\n".join(out) + "\n")


def main() -> int:
    ap = argparse.ArgumentParser(description="Convertit un tableur d'adresses en GPX.")
    ap.add_argument("xls", help="Fichier Excel source (.xls)")
    ap.add_argument("gpx", help="Fichier GPX de sortie")
    ap.add_argument("--title", default="Itinéraire - Marche-en-Famenne")
    ap.add_argument("--cache", default=None, help="Chemin du cache de géocodage JSON")
    args = ap.parse_args()

    cache = args.cache or (os.path.splitext(args.gpx)[0] + ".geocache.json")
    geocoder = Geocoder(cache)

    stops = parse_rows(args.xls)
    print(f"{len(stops)} adresses à géocoder")

    n_exact = n_fallback = n_override = 0
    missing = []
    for s in stops:
        key = (s["rue"].lower(), s["loc"].lower())
        if key in OVERRIDES:
            lat, lon, note = OVERRIDES[key]
            s["lat"], s["lon"], s["exact"] = lat, lon, False
            n_override += 1
            print(f"OVR  {s['rue']:<35} {s['loc']:<10} ({note})")
            continue
        p, exact = geocoder.geocode(s["street"], s["num"], s["loc"])
        s["lat"], s["lon"], s["exact"] = (p[0], p[1], exact) if p else (None, None, False)
        if p is None:
            missing.append(s)
            print(f"MISS {s['rue']:<35} {s['loc']}")
        elif exact:
            n_exact += 1
        else:
            n_fallback += 1
            print(f"~fb  {s['rue']:<35} {s['loc']:<10} (centre de rue)")

    if missing:
        print(f"\n{len(missing)} adresse(s) introuvable(s) — ajoutez-les dans OVERRIDES :", file=sys.stderr)
        for m in missing:
            print(f"  ({m['rue'].lower()!r}, {m['loc'].lower()!r})", file=sys.stderr)
        return 1

    geom, km = osrm_route(stops)
    write_gpx(args.gpx, args.title, stops, geom, km)
    print(f"\nÉcrit {args.gpx} — {len(stops)} arrêts, {len(geom)} points de tracé, {km:.1f} km")
    print(f"  exact={n_exact}  centre-de-rue={n_fallback}  surcharges={n_override}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
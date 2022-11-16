<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Gpx;
use AcMarche\Pivot\Repository\PivotRemoteRepository;

trait ParseGpxTrait
{
    /**
     * @required
     */
    public PivotRemoteRepository $pivotRemoteRepository;

    public function parseGpx(Offre $offre)
    {
        $gpxs = $this->parseDocs($offre);
        if (count($gpxs) > 0) {
            if ($km = $this->findByUrn($offre, 'urn:fld:dist', returnData: true)) {
                $offre->gpx_distance = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:idcirkwi', returnData: true)) {
                $offre->gpx_id = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:infusgvttdur', returnData: true)) {
                $offre->gpx_duree = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:infusgvttdiff', returnData: true)) {
                $offre->gpx_difficulte = $km[0]->value;
            }
        }
    }

    /**
     * @param Offre $offre
     * @return array|Gpx[]
     */
    public function parseDocs(Offre $offre): array
    {
        foreach ($offre->documents as $document) {
            if ($document->extension == 'gpx') {
                $gpx = new Gpx();
                $gpx->code = $offre->codeCgt;
                $gpx->data_raw = $this->pivotRemoteRepository->gpxRead($document->url);
                $gpxXml = simplexml_load_string($gpx->data_raw);
                foreach ($gpxXml->metadata as $pt) {
                    $gpx->name = (string)$pt->name;
                    $gpx->desc = (string)$pt->desc;
                    $gpx->url = $document->url;
                    foreach ($pt->link as $link) {
                        $gpx->links[] = (string)$link->attributes();
                    }
                }
                $offre->gpxs[] = $gpx;
            }
        }

        return $offre->gpxs;
    }
}
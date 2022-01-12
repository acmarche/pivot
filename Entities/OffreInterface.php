<?php


namespace AcMarche\Pivot\Entities;

use DOMElement;
use DOMDocument;
/**
 * @property string $id
 *
 * @property string $titre
 *
 * @property Libelle $libelle
 *
 * @property string $reference
 *
 * @property string $description
 *
 * @property Geocode $geocode
 *
 * @property Localite $localisation
 *
 * @property string $url
 *
 * @property Contact[] $contacts
 *
 * @property Description[] $descriptions
 *
 * @property Media[] $medias
 *
 * @property Categorie[] $categories
 *
 * @property Selection[] $selections
 *
 * @property Horaire[] $horaires
 *
 * @property int[] $parentIds
 *
 * @property int[] $enfantIds
 *
 * @property OffreInterface[] $parents
 *
 * @property OffreInterface[] $enfants
 *
 */
interface OffreInterface
{
    public function getTitre(?string $language = 'fr'): string;

    function contactPrincipal(): ?Contact;

    function communcationPrincipal(): array;

    function emailPrincipal(): ?string;

    function telPrincipal();

    function sitePrincipal();

    static function createFromDom(DOMElement $offre, DOMDocument $document): ?Offre;

    /**
     * Utilise dans @return Horline|null
     * @see EventUtils
     */
    function firstHorline(): ?Horline;

    /**
     * Raccourcis util a react
     *
     * @return Horline[]
     */
    function dates(): array;

    function firstImage(): ?string;

}

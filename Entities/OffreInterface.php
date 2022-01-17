<?php

namespace AcMarche\Pivot\Entities;

use DOMDocument;
use DOMElement;

/**
 * @property string           $id
 * @property string           $titre
 * @property Libelle          $libelle
 * @property string           $reference
 * @property string           $description
 * @property Geocode          $geocode
 * @property Localite         $localisation
 * @property string           $url
 * @property Contact[]        $contacts
 * @property Description[]    $descriptions
 * @property Media[]          $medias
 * @property Categorie[]      $categories
 * @property Selection[]      $selections
 * @property Horaire[]        $horaires
 * @property int[]            $parentIds
 * @property int[]            $enfantIds
 * @property OffreInterface[] $parents
 * @property OffreInterface[] $enfants
 */
interface OffreInterface
{
    public function getTitre(?string $language = 'fr'): string;

    public function contactPrincipal(): ?Contact;

    public function communcationPrincipal(): array;

    public function emailPrincipal(): ?string;

    public function telPrincipal();

    public function sitePrincipal();

    public static function createFromDom(DOMElement $offre, DOMDocument $document): ?Offre;

    /**
     * Utilise dans @return Horline|null.
     *
     * @see EventUtils
     */
    public function firstHorline(): ?Horline;

    /**
     * Raccourcis util a react.
     *
     * @return Horline[]
     */
    public function dates(): array;

    public function firstImage(): ?string;
}

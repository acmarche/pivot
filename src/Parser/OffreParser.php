<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Category;
use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Spec\SpecSearchTrait;
use AcMarche\Pivot\Spec\SpecTypeEnum;
use AcMarche\Pivot\Spec\UrnCatList;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnSubCatList;
use AcMarche\Pivot\Spec\UrnTypeList;

class OffreParser
{
    use SpecSearchTrait;
    use ParseImagesTrait;
    use ParseRelatedOffersTrait;
    use ParseRelationOffresTgtTrait;
    use ParseSpecificationsTrait;
    use ParserEventTrait;
    use ParseImagesTrait;

    public function launchParse(Offre $offre)
    {
        $this->specitificationsByOffre($offre);
        $this->parseOffre($offre);
        $this->parseDatesEvent($offre);
        $this->parseRelatedOffers($offre);
        $this->parseRelOffresTgt($offre);
    }

    public function parseOffre(Offre $offre)
    {
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

        $offre->homepage = $this->findByUrnReturnValue($offre, UrnList::HOMEPAGE->value);
        $offre->active = $this->findByUrnReturnValue($offre, UrnList::ACTIVE->value);

        foreach ($this->findByUrn($offre, SpecTypeEnum::EMAIL->value, SpecData::KEY_TYPE, returnData: true) as $spec) {
            $offre->emails[] = $spec->value;
        }
        foreach ($this->findByUrn($offre, SpecTypeEnum::PHONE->value, SpecData::KEY_TYPE, returnData: true) as $spec) {
            $offre->tels[] = $spec->value;
        }
        foreach ($this->findByUrn($offre, SpecTypeEnum::GSM->value, SpecData::KEY_TYPE, returnData: true) as $spec) {
            $offre->tels[] = $spec->value;
        }

        $offre->descriptions = $this->findByUrn(
            $offre,
            UrnCatList::DESCRIPTION->value,
            SpecData::KEY_CAT,
            returnData: true
        );
        $descriptionsMarketing = $this->findByUrn($offre, UrnCatList::DESCRIPTION_MARKETING->value, returnData: true);
        if (count($descriptionsMarketing) > 0) {
            $descriptions = [];
            foreach ($descriptionsMarketing[0]->spec as $descriptionMarketing) {
                if ($descriptionMarketing->type == 'TextML') {
                    $descriptions[] = $descriptionMarketing;
                }
            }
            $offre->descriptions = array_merge($offre->descriptions, $descriptions);
        }

        $offre->tarifs = $this->findByUrn($offre, UrnList::TARIF->value, returnData: true);
        $offre->webs = $this->findByUrn($offre, UrnList::WEB->value, returnData: true);
        $offre->webs = [...$offre->webs,...$this->findByUrn($offre, UrnList::FACEBOOK->value, returnData: true)];

        $offre->hades_ids = $this->findByUrn($offre, UrnList::HADES_ID->value, returnData: true);
        $offre->communications = $this->findByUrn(
            $offre,
            UrnCatList::COMMUNICATION->value,
            SpecData::KEY_CAT,
            returnData: true
        );
        $offre->adresse_rue = $this->findByUrn($offre, UrnList::ADRESSE_RUE->value, returnData: true);
        $offre->equipements = $this->findByUrn(
            $offre,
            UrnCatList::EQUIPEMENTS->value,
            SpecData::KEY_CAT,
            returnData: true
        );
        $offre->accueils = $this->findByUrn($offre, UrnCatList::ACCUEIL->value, SpecData::KEY_CAT, returnData: true);

        $this->setCategories($offre);
        $this->setNameByLanguages($offre);
    }

    private function setNameByLanguages(Offre $offre)
    {
        $labels = [];
        $noms = $this->findByUrn($offre, UrnSubCatList::NOM_OFFRE->value, SpecData::KEY_SUB_CAT, returnData: true);
        foreach ($noms as $nom) {
            $label = new Label();
            $label->value = $nom->value;
            $language = substr($nom->urn, 0, 2);
            $label->lang = $language;
            $labels[] = $label;
        }
        $label = new Label();
        $label->value = $offre->nom;
        $label->lang = "fr";
        $labels[] = $label;
        $offre->label = $labels;
    }

    private function setCategories(Offre $offre)
    {
        $urn = match ($offre->typeOffre->idTypeOffre) {
            UrnTypeList::evenement()->typeId => UrnList::CATEGORIE_EVENT,
            UrnTypeList::restauration()->typeId => UrnList::CLASSIFICATION_LABEL,
            UrnTypeList::produitDeTerroir()->typeId => UrnList::CATEGORIE_PDT,
            UrnTypeList::producteur()->typeId => UrnList::CATEGORIE_PRD,
            UrnTypeList::artisan()->typeId => UrnList::CATEGORIE_ATS,
            default => UrnList::CATEGORIE
        };

        $specifications = $this->findByUrn($offre, $urn->value, SpecData::KEY_CAT, contains: true);
        foreach ($specifications as $specification) {
            if ($specification->data->type == SpecTypeEnum::BOOLEAN->value) {//skip gaultmil,michstar...
                $order = $specification->data->order;
                $labels = $specification->urnDefinition->label;
                $offre->categories[$specification->data->order] = new Category(
                    $specification->data->urn,
                    $order,
                    $labels
                );
            } else {
                $offre->classements[] = $specification;
            }
        }
    }
}
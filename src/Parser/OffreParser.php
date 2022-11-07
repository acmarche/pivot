<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Category;
use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecInfo;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Spec\SpecSearchTrait;
use AcMarche\Pivot\Spec\SpecTypeEnum;
use AcMarche\Pivot\Spec\UrnCatList;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnSubCatList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Spec\UrnUtils;

class OffreParser
{
    use SpecSearchTrait;
    use ParserEventTrait;

    public function __construct(private UrnUtils $urnUtils)
    {
    }

    public function parseOffre(Offre $offre)
    {
        $this->specs = $offre->spec;
        foreach ($this->specs as $spec) {
            $offre->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }

        if ($km = $this->findByUrn('urn:fld:dist')) {
            $offre->gpx_distance = $km[0]->value;
        }
        if ($km = $this->findByUrn('urn:fld:idcirkwi')) {
            $offre->gpx_id = $km[0]->value;
        }
        if ($km = $this->findByUrn('urn:fld:infusgvttdur')) {
            $offre->gpx_duree = $km[0]->value;
        }
        if ($km = $this->findByUrn('urn:fld:infusgvttdiff')) {
            $offre->gpx_difficulte = $km[0]->value;
        }

        $offre->homepage = $this->findByUrnReturnValue(UrnList::HOMEPAGE->value);
        $offre->active = $this->findByUrnReturnValue(UrnList::ACTIVE->value);

        foreach ($this->findByUrn(SpecTypeEnum::EMAIL->value, "type") as $spec) {
            $offre->emails[] = $spec->value;
        }
        foreach ($this->findByUrn(SpecTypeEnum::TEL->value, "type") as $spec) {
            $offre->tels[] = $spec->value;
        }

        $offre->descriptions = $this->findByUrn(UrnCatList::DESCRIPTION->value, "urnCat");
        $descriptionsMarketing = $this->findByUrn(UrnCatList::DESCRIPTION_MARKETING->value);
        if (count($descriptionsMarketing) > 0) {
            $descriptions = [];
            foreach ($descriptionsMarketing[0]->spec as $descriptionMarketing) {
                if ($descriptionMarketing->type == 'TextML') {
                    $descriptions[] = $descriptionMarketing;
                }
            }
            $offre->descriptions = array_merge($offre->descriptions, $descriptions);
        }

        $offre->tarifs = $this->findByUrn(UrnList::TARIF->value);
        $offre->webs = $this->findByUrn(UrnList::WEB->value);
        $classements = $this->findByUrn(UrnSubCatList::CLASSIF->value, "urnSubCat");
        foreach ($classements as $classement) {
            $offre->classements[] = new SpecInfo($this->urnUtils->getInfosUrn($classement->urn), $classement);
        }
        $offre->hades_ids = $this->findByUrn(UrnList::HADES_ID->value);
        $offre->communications = $this->findByUrn(UrnCatList::COMMUNICATION->value, "urnCat");
        $offre->adresse_rue = $this->findByUrn(UrnList::ADRESSE_RUE->value);
        $offre->equipements = $this->findByUrn(UrnCatList::EQUIPEMENTS->value, "urnCat");
        $offre->accueils = $this->findByUrn(UrnCatList::ACCUEIL->value, "urnCat");

        $this->setCategories($offre);
        $this->setNameByLanguages($offre);
    }

    /**
     * Complète la class Event
     * Date de début, date de fin,...
     *
     * @param array $events
     */
    public function parseDatesEvents(array $events, bool $removeObsolete = false): void
    {
        array_map(function ($event) use ($removeObsolete) {
            $this->parseDatesEvent($event, $removeObsolete);
        }, $events);
    }

    public function parseDatesEvent(Offre $offre, bool $removeObsolete = false): void
    {
        if ($offre->typeOffre->idTypeOffre !== UrnTypeList::evenement()->typeId) {
            return;
        }

        $offre->dates = $this->getDates();
        $fistDate = $offre->firstDate();
        if ($fistDate) {
            $offre->dateBegin = $fistDate->date_begin;
            $offre->dateEnd = $fistDate->date_end;
        }

        if ($removeObsolete) {
            foreach ($offre->dates as $key => $dateBeginEnd) {
                if (EventUtils::isDateBeginEndObsolete($dateBeginEnd)) {
                    unset($offre->dates[$key]);
                }
            }
            $offre->dates = array_values($offre->dates);//reset index
            $fistDate = $offre->firstDate();
            if ($fistDate) {
                $offre->dateBegin = $fistDate->date_begin;
                $offre->dateEnd = $fistDate->date_end;
            }
        }
    }

    private function setNameByLanguages(Offre $offre)
    {
        $labels = [];
        $noms = $this->findByUrn(UrnSubCatList::NOM_OFFRE->value, "urnSubCat");
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
            9 => UrnList::CATEGORIE_EVENT,
            267 => UrnList::CATEGORIE_PDT,
            258 => UrnList::CATEGORIE_PRD,
            259 => UrnList::CATEGORIE_ATS,
            default => UrnList::CATEGORIE
        };

        $cats = $this->findByUrn($urn->value, "urn", true);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $order = $cat->order;
                $labels = $info->label;
                $offre->categories[$cat->order] = new Category($cat->urn, $order, $labels);
            }
        }
    }
}

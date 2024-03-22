<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Gpx;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Spec\UrnList;
use Symfony\Contracts\Service\Attribute\Required;

trait ParseGpxTrait
{
    #[Required]
    public PivotRemoteRepository $pivotRemoteRepository;
    #[Required]
    public UrnDefinitionRepository $urnDefinitionRepository;

    public function parseGpx(Offre $offre)
    {
        $gpxs = $this->parseDocs($offre);
        if (count($gpxs) > 0) {
            if ($km = $this->findByUrn($offre, 'urn:fld:dist', returnData: true)) {
                $offre->gpx_distance = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:idcirkwi', returnData: true)) {
                $offre->idcirkwi = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:infusgvttdur', returnData: true)) {
                $offre->gpx_duree = $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:typecirc')) {
                $offre->gpx_type_circuit = $km[0];
            }
            if ($km = $this->findByUrn($offre, 'urn:fld:catcirc')) {
                $offre->gpx_cat_circuit = $km[0];
            }

            if ($km = $this->findByUrn($offre, UrnList::VTT_DIFF->value, returnData: true)) {
                $urnDefinition = $this->urnDefinitionRepository->findByUrn($km[0]->value);
                $offre->gpx_difficulte = $urnDefinition ? $urnDefinition->labelByLanguage('fr') : $km[0]->value;
            }
            if ($km = $this->findByUrn($offre, UrnList::PEDESTRE_DIFF->value, returnData: true)) {
                $urnDefinition = $this->urnDefinitionRepository->findByUrn($km[0]->value);
                $offre->gpx_difficulte = $urnDefinition ? $urnDefinition->labelByLanguage('fr') : $km[0]->value;
            }
            if ($link = $this->findByUrn($offre, 'urn:fld:cirkwi:link', returnData: true)) {
                $offre->cirkwi_link = $link[0]->value;
            }

        }
    }

    /**
     * @return array|Gpx[]
     */
    public function parseDocs(Offre $offre): array
    {
        foreach ($offre->documents as $document) {
            if ($document->extension == 'gpx') {
                $gpx = new Gpx();
                $gpx->codeCgt = $document->codeCgt;
                $gpx->data_raw = $this->pivotRemoteRepository->gpxRead($document->url);
                $gpx->url = $document->url;
                $gpx->urn = $document->urn;
                $gpxXml = simplexml_load_string($gpx->data_raw);
                foreach ($gpxXml->metadata as $pt) {
                    $gpx->name = (string)$pt->name;
                    $gpx->desc = (string)$pt->desc;
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

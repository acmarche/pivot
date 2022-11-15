<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Gpx;

trait ParseGpxTrait
{
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
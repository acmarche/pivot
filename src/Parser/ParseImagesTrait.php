<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Document;
use AcMarche\Pivot\Spec\UrnList;
use Exception;
use Symfony\Component\String\UnicodeString;

trait ParseImagesTrait
{
    public function parseImages(Offre $offre): array
    {
        $docs = ['images' => [], 'documents' => []];
        foreach ($offre->relOffre as $relOffre) {
            if (!in_array(
                $relOffre->urn,
                [UrnList::MEDIAS_PARTIAL->value, UrnList::MEDIA_DEFAULT->value, UrnList::MEDIAS_AUTRE->value]
            )) {
                continue;
            }
            $codeCgt = $relOffre->offre['codeCgt'];
            try {
                $relatedOffer = $this->pivotRepository->fetchOffreByCgt($codeCgt, $relOffre->offre['dateModification']);
            } catch (Exception) {
                continue;
            }
            if (!$relatedOffer instanceof Offre) {
                continue;
            }
            if ($relOffre->urn == UrnList::MEDIA_DEFAULT->value) {
                $offre->media_default = $relatedOffer;
            }
            foreach ($relatedOffer->spec as $specData) {
                if ($specData->urn == UrnList::URL->value) {
                    $value = str_replace("http:", "https:", $specData->value);
                    $string = new UnicodeString($value);
                    $extension = $string->slice(-3);
                    $document = new Document();
                    $document->extension = $extension->toString();
                    $document->url = $value;
                    $document->codeCgt = $relatedOffer->codeCgt;
                    $document->urn = $relatedOffer->url;
                    if (in_array($extension, ['jpg', 'png'])) {
                        if (!array_search($value, $docs['images'])) {
                            $docs['images'][] = $value;
                        }
                    } else {
                        $docs['documents'][] = $document;
                    }
                }
            }
        }

        $offre->images = $docs['images'];
        $offre->documents = $docs['documents'];

        return $docs;
    }
}

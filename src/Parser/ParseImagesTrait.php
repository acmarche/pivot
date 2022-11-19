<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Document;
use AcMarche\Pivot\Spec\UrnList;
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
                $relatedOffer = $this->pivotRepository->fetchOffreByCgt($codeCgt, class: Offre::class);
            } catch (\Exception $exception) {
                continue;
            }
            if (!$relatedOffer instanceof Offre) {
                continue;
            }
            foreach ($relatedOffer->spec as $specData) {
                if ($specData->urn == UrnList::URL->value) {
                    $value = str_replace("http:", "https:", $specData->value);
                    $string = new UnicodeString($value);
                    $extension = $string->slice(-3);
                    $document = new Document();
                    $document->extension = $extension;
                    $document->url = $value;
                    if (in_array($extension, ['jpg', 'png'])) {
                        $docs['images'][] = $value;
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

    private function parseExtraFromImage($offre, $relation, Offre $relatedOffer)
    {
        if ($relation->urn == UrnList::CONTACT_DIRECTION->value) {
            $offre->contact_direction = $relatedOffer;
        }
        if ($relation->urn === UrnList::POIS->value) {
            $offre->pois[] = $relatedOffer;
        }
        if ($relation->urn == UrnList::MEDIA_DEFAULT->value) {
            $offre->media_default = $relatedOffer;
        }
    }

}
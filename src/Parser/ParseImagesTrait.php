<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Document;
use AcMarche\Pivot\Spec\UrnList;
use Symfony\Component\String\UnicodeString;

trait ParseImagesTrait
{
    public function parseImages(Offre $offre)
    {
        $specificationMedias = $this->findByUrn($offre, UrnList::URL->value);
        foreach ($specificationMedias as $specificationMedia) {
            $value = str_replace("http:", "https:", $specificationMedia->data->value);
            $string = new UnicodeString($value);
            $extension = $string->slice(-3);
            $document = new Document();
            $document->extension = $extension;
            $document->url = $value;

            if (in_array($extension, ['jpg', 'png'])) {
                $offre->images[] = $value;
            } else {
                $offre->documents[] = $document;
            }
        }

        $specificationImages = $this->findByUrn($offre, UrnList::MEDIAS_PARTIAL->value, contains: true);
        foreach ($specificationImages as $specificationImage) {
            $value = str_replace("http:", "https:", $specificationImage->data->value);
            $offre->images[] = $value;
        }
        if (count($offre->images) > 0) {
            $offre->image = $offre->images[0];
        }
    }

}
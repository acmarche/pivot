<?php

namespace AcMarche\Pivot\Entities\Offre;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;

class TypeOffreShort2
{
    use LabelTrait;

    public int $idTypeOffre;
    // public Label $label;

    /* public function labelByLanguage(string $language = Label::FR): string
     {
         if ($this->label->get($language)) {
             return $this->label->get($language);
         }

         return 'title found';
     }*/
}

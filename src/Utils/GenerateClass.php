<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\ThesaurusEnum;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateClass
{
    public function __construct(private PivotRemoteRepository $pivotRemoteRepository, private SluggerInterface $slugger)
    {
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeurn;fmt=json
     * bin/console pivot:admin > AcMarche/Pivot/src/Spec/UrnTypeList.php
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function generateTypeUrn()
    {
        $thesaurus = json_decode(
            $this->pivotRemoteRepository->getThesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value)
        );

        echo "<?php \n ";
        echo "namespace AcMarche\Pivot\Spec; \n ";
        echo "Class UrnTypeList: int { \n ";

        foreach ($thesaurus->spec as $spec) {
            $label = new Label();
            $label->value = $spec->label[0]->value;
            $label->lang = $spec->label[0]->lang;
            $deprecated = 'false';
            $root = $spec->root == 1 ?? 0;
            $slug = strtoupper($this->slugger->slug($label->value, "_"));
            echo "  public static function $slug(): Urn { \n ";
            echo "return new Urn(
                '$spec->urn', '$spec->code', $spec->order, $deprecated, '$spec->type', '$label->value', '$spec->root'
            ); \n ";
            echo "} \n ";
        }
        echo "}";
    }

}
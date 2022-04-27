<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\Urn\Urn;
use AcMarche\Pivot\PivotTypeEnum;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use AcMarche\Pivot\ThesaurusEnum;
use Symfony\Component\String\Slugger\SluggerInterface;

class GenerateClass
{
    public function __construct(private PivotRemoteRepository $pivotRemoteRepository, private SluggerInterface $slugger)
    {
    }

    /**
     * Generate Class @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @see PivotTypeEnum
     * http://pivot.tourismewallonie.be/index.php/9-pivot-gest-pc/142-types-de-fiches-pivot
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json
     */
    public function generateTypeOffre()
    {
        $thesaurus = json_decode(
            $this->pivotRemoteRepository->getThesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value)
        );
        echo "<?php \n ";
        echo "namespace AcMarche\Pivot; \n ";
        echo "enum PivotType: int { \n ";
        foreach ($thesaurus->spec as $spec) {
            $label = $spec->label[0]->value;
            $slug = strtoupper($this->slugger->slug($label, "_"));
            echo "/** \n ";
            echo $label."\n";
            echo "Root ".$spec->root." \n ";
            echo "Urn ".$spec->urn." \n ";
            echo "Code ".$spec->code." \n ";
            if ($spec->deprecated) {
                echo "@eprecated ".$spec->deprecated." \n ";
            }
            echo "*/ \n";
            echo 'case '.$slug.' = '."$spec->order; \n ";
        }
        echo "}";
    }

    /**
     * Generate Class @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @see PivotTypeEnum
     * http://pivot.tourismewallonie.be/index.php/9-pivot-gest-pc/142-types-de-fiches-pivot
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json
     */
    public function generateTypeOffreClass()
    {
        $thesaurus = json_decode(
            $this->pivotRemoteRepository->getThesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value)
        );
        echo "<?php \n ";
        echo "namespace AcMarche\Pivot; \n ";
        echo "enum PivotType: int { \n ";
        foreach ($thesaurus->spec as $spec) {
            $label = $spec->label[0]->value;
            $slug = strtoupper($this->slugger->slug($label, "_"));
            echo "/** \n ";
            echo $label."\n";
            echo "Root ".$spec->root." \n ";
            echo "Urn ".$spec->urn." \n ";
            echo "Code ".$spec->code." \n ";
            if ($spec->deprecated) {
                echo "@eprecated ".$spec->deprecated." \n ";
            }
            echo "*/ \n";
            echo 'case '.$slug.' = '."$spec->order; \n ";
        }
        echo "}";
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
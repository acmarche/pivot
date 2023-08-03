<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use AcMarche\Pivot\Api\ThesaurusEnum;
use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Repository\PivotRemoteRepository;
use Symfony\Component\String\UnicodeString;

class GenerateClass
{
    public function __construct(private readonly PivotRemoteRepository $pivotRemoteRepository)
    {
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeurn;fmt=json
     * bin/console pivot:admin > AcMarche/Pivot/src/Spec/UrnTypeList.php
     * @return void
     * @throws TransportExceptionInterface
     */
    public function generateTypeUrn()
    {
        $thesaurus = json_decode(
            $this->pivotRemoteRepository->thesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value),
            null,
            512,
            JSON_THROW_ON_ERROR
        );

        echo "<?php \n ";
        echo "namespace AcMarche\Pivot\Spec; \n ";
        echo "
use AcMarche\Pivot\Entities\Urn\Urn;
/**
             * @see GenerateClass
             */";
        echo "Class UrnTypeList { \n ";

        $codes = [];
        foreach ($thesaurus->spec as $spec) {
            $label = new Label();
            $label->value = $spec->label[0]->value;
            $label->lang = $spec->label[0]->lang;
            $deprecated = 'false';
            $root = $spec->root == 1 ?? 0;
            $slug = new UnicodeString($label->value);
            $slug = $slug->ascii()->camel();
            echo "  public static function $slug(): Urn { \n ";
            echo "return new Urn(
                '$spec->urn', '$spec->code', $spec->order, $deprecated, '$spec->type', '$label->value', '$spec->root'
            ); \n ";
            echo "} \n ";
            $codes[] = $spec->code;
        }

        echo "public static function getAllCode():array {";
        echo "return ['";
        echo implode("','", $codes);
        echo "'];";
        echo "}";
        echo "}";
    }
}

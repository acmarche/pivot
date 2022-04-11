<?php

namespace AcMarche\Pivot\Utils;

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
        $thesaurus = json_decode($this->pivotRemoteRepository->getThesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE));
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
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function generateTypeUrn()
    {
        $thesaurus = $this->pivotRemoteRepository->getThesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE);
        echo $thesaurus;
    }

}
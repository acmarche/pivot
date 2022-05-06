<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Api\ContentEnum;
use AcMarche\Pivot\Api\FormatEnum;
use AcMarche\Pivot\Api\ThesaurusEnum;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PivotRemoteRepository
{
    use ConnectionPivotTrait;

    public function __construct()
    {
        $this->connect('application/json');
    }

    /**
     * @throws Exception|TransportExceptionInterface
     */
    public function offreByCgt(string $codeCgt, array $options = []): string
    {
        $options = [
            'output' => 'html',
            'page' => 1,
            'fmt' => 'json',
            'info' => true,//labels des specs et relations
            'infolvl' => 0,//de 0 a 10
            'nofmt,' => true,//convertir automatiquement ces contenus HTML en texte brut avec mise en page.
            'content' => ContentEnum::LVL_DEFAULT->value,
        ];

        $options = [
            'content' => ContentEnum::LVL_DEFAULT->value,
            'info' => true,
            'infolvl' => 0,
        ];

        $params = "";
        foreach ($options as $key => $value) {
            $params .= $key.'='.$value.';';
        }
        $params = substr($params, 0, -1);

        return $this->executeRequest($this->base_uri.'/offer/'.$codeCgt);
    }

    /**
     *
     * @throws Exception|TransportExceptionInterface
     */
    public function offreExist(string $codeCgt): string
    {
        return $this->executeRequest($this->base_uri.'/offer/'.$codeCgt.'/exists');
    }

    /**
     * tins
     * /thesaurus/typeofr;fmt=xml
     *
     * @throws Exception|TransportExceptionInterface
     */
    public function getThesaurus(string $type): string
    {
        return $this->executeRequest($this->base_uri.'/thesaurus/'.$type);
    }

    /**
     * thesaurus/family/urn:fam:1;fmt=xml (fam:1: hebergements)
     *
     * @param string $type
     * @param string|null $sousType
     */
    public function thesaurusFamily(string $type, ?string $sousType): string
    {
        $url = $this->base_uri.'/thesaurus/'.ThesaurusEnum::THESAURUS_FAMILY->value.'/'.$type;
        if ($sousType) {
            $url .= '';
        }

        return $this->executeRequest($url);
    }

    /**
     * /thesaurus/typeofr/ 1 ; fmt=xml (1 => hotel)
     *
     * @param int $sousType
     *
     * @return string
     */
    public function thesaurusLogique($sousType): string
    {
        $url = $this->base_uri.'/'.ThesaurusEnum::THESAURUS_TYPE_OFFRE->value.'/'.$sousType;

        return $this->executeRequest($url);
    }

    public function thesaurusLocalite(?int $idLocalite = null): string
    {
        $url = $this->base_uri.'/thesaurus/'.ThesaurusEnum::THESAURUS_TYPE_TINS->value;
        if ($idLocalite) {
            $url .= '/'.$idLocalite;
        }

        return $this->executeRequest($url);
    }

    /**
     * cp = code postal
     * loc = localité
     * com = commune
     * prv = province
     * mdt = organisme touristique (identifiant de l’organisme)
     * pn = parc naturel (identifiant du parc naturel)
     * mix = recherche sur les colonnes code postal, localité et commune
     *
     * @param int|null $idLocalite
     *
     * @return string
     */
    public function thesaurusLocaliteSearch(string $field, string $value): string
    {
        $url = $this->base_uri.'/'.ThesaurusEnum::THESAURUS_TYPE_TINS->value.'/'.$field.'/'.$value;

        return $this->executeRequest($url);
    }

    public function thesaurusCategories(): string
    {
        $url = $this->base_uri.'/cat/urn:fld:filtcat;content=2';

        return $this->executeRequest($url);
    }

    /**
     * tins
     * /thesaurus/typeofr;fmt=xml
     * @throws Exception|TransportExceptionInterface
     */
    public function getImage(string $codeCgt): string
    {
        return $this->executeRequest($this->base_uri.'/img/'.$codeCgt);
    }

    /**
     * Ces requêtes sont créées et stockées par les opérateurs de PIVOT afin de fournir des flux
     * de données. Les requêtes sont accessibles au moyen d’un code identifiant unique (codeCgt).
     * @throws Exception|TransportExceptionInterface
     */
    public function query(): string
    {
        $options = [
            'content' => 2,//ContentEnum::LVL_DEFAULT->value,
            'info' => true,
            'infolvl' => 0,
        ];
        $params = ";";
        foreach ($options as $key => $value) {
            $params .= $key.'='.$value.';';
        }
        $params = substr($params, 0, -1);

        $t =  $this->executeRequest($this->base_uri.'/query/'.$this->code_query);
        var_dump(55555, $t);
        return $t;
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:cat;fmt=xml
     * @param int $typeId
     */
    public function fetchSousTypes(int $typeId)
    {
        $url = $this->base_uri.'/thesaurus/typeofr/'.$typeId.'/urn:fld:cat';

        return $this->executeRequest($url);
    }

}

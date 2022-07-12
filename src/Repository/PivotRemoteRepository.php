<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Api\ContentEnum;
use AcMarche\Pivot\Api\ThesaurusEnum;
use Exception;

class PivotRemoteRepository
{
    use ConnectionPivotTrait;

    public function __construct()
    {
        $this->connect('application/json');
    }

    /**
     * @param string $codeCgt
     * @param array $options
     * @return string|null
     * @throws Exception
     */
    public function offreByCgt(string $codeCgt, array $options = []): ?string
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

    public function offreExist(string $codeCgt): ?string
    {
        return $this->executeRequest($this->base_uri.'/offer/'.$codeCgt.'/exists');
    }

    /**
     * tins
     * /thesaurus/typeofr;fmt=xml
     * @throws Exception
     */
    public function getThesaurus(string $type): ?string
    {
        return $this->executeRequest($this->base_uri.'/thesaurus/'.$type);
    }

    /**
     * thesaurus/family/urn:fam:1;fmt=xml (fam:1: hebergements)
     *
     * @param string|null $type
     * @param string|null $sousType
     * @return string|null
     * @throws Exception
     */
    public function thesaurusFamily(?string $type = null, ?string $sousType = null): ?string
    {
        $url = $this->base_uri.'/thesaurus/'.ThesaurusEnum::THESAURUS_FAMILY->value;
        if ($type) {
            $url .= '/'.$type;
        }
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
     * @return string|null
     * @throws Exception
     */
    public function thesaurusLogique($sousType): ?string
    {
        $url = $this->base_uri.'/'.ThesaurusEnum::THESAURUS_TYPE_OFFRE->value.'/'.$sousType;

        return $this->executeRequest($url);
    }

    public function thesaurusLocalite(?int $idLocalite = null): ?string
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
     * @param string $field
     * @param string $value
     * @return string|null
     * @throws Exception
     */
    public function thesaurusLocaliteSearch(string $field, string $value): ?string
    {
        $url = $this->base_uri.'/'.ThesaurusEnum::THESAURUS_TYPE_TINS->value.'/'.$field.'/'.$value;

        return $this->executeRequest($url);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function thesaurusCategories(): string
    {
        $url = $this->base_uri.'/cat/urn:fld:filtcat;content=2';

        return $this->executeRequest($url);
    }

    /**
     * tins
     * /thesaurus/typeofr;fmt=xml
     * @throws Exception
     */
    public function getImage(string $codeCgt): ?string
    {
        return $this->executeRequest($this->base_uri.'/img/'.$codeCgt);
    }

    /**
     * Ces requêtes sont créées et stockées par les opérateurs de PIVOT afin de fournir des flux
     * de données. Les requêtes sont accessibles au moyen d’un code identifiant unique (codeCgt).
     * @throws Exception
     */
    public function query(string $query = null): ?string
    {
        if (!$query) {
            $query = $this->code_query;
        }

        return $this->executeRequest($this->base_uri.'/query/'.$query);
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/family/urn:fam:1;fmt=xml
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:cat;fmt=xml
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/9/urn:fld:catevt;fmt=xml
     * @param int $typeId
     * @param string $urn
     * @return string|null
     * @throws Exception
     */
    public function fetchSousTypes(int $typeId, string $urn): ?string
    {
        $url = $this->base_uri.'/thesaurus/typeofr/'.$typeId.'/urn:fld:cat'.$urn;

        return $this->executeRequest($url);
    }
}

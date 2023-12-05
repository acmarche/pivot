<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Api\ContentEnum;
use AcMarche\Pivot\Api\ThesaurusEnum;
use AcMarche\Pivot\Spec\UrnList;
use Exception;

class PivotRemoteRepository
{
    use ConnectionPivotTrait;

    public function __construct()
    {
        $this->connect('application/json');
    }

    /**
     * @throws Exception
     */
    public function offreByCgt(string $codeCgt, array $options = []): ?string
    {
        $options = [
            'output' => 'html',
            'page' => 1,
            'fmt' => 'json',
            'info' => true, //labels des specs et relations
            'infolvl' => 0, //de 0 a 10
            'nofmt,' => true, //convertir automatiquement ces contenus HTML en texte brut avec mise en page.
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
    public function thesaurus(string $type, ?string $params = null): ?string
    {
        $url = $this->base_uri.'/thesaurus/'.$type;
        if ($params) {
            $url .= '/'.$params;
        }

        return $this->executeRequest($url);
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/11/urn:cat:classlab;fmt=json
     * @return string
     * @throws Exception
     */
    public function thesaurusCategories(int $typeId): string
    {
        $params = $typeId.'/'.UrnList::CLASSIFICATION_LABEL->value;

        return $this->thesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value, $params);
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/family/urn:fam:1;fmt=xml
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:cat;fmt=xml
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/9/urn:fld:catevt;fmt=xml
     * @return string|null
     * @throws Exception
     */
    public function thesaurusSousTypes(int $typeId, string $urn): ?string
    {
        $params = '/'.$typeId.'/urn:fld:cat'.$urn;

        return $this->thesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value, $params);
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/family/urn:fam:2;fmt=json (fam:2: decouvertes)
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/11;fmt=json
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/11/urn:cat:classlab;fmt=json
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/1/1;fmt=json
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn/urn:typ:11;fmt=json
     *
     * @param string|null $type
     * @param string|null $sousType
     * @return string|null
     * @throws Exception
     */
    public function thesaurusFamily(): ?string
    {
        return $this->thesaurus(ThesaurusEnum::THESAURUS_FAMILY->value);
    }

    public function thesaurusUrn(string $urnName): ?string
    {
        return $this->thesaurus('urn/'.$urnName);
    }

    public function thesaurusLocalite(?int $idLocalite = null): ?string
    {
        $params = null;
        if ($idLocalite) {
            $params = '/'.$idLocalite;
        }

        return $this->thesaurus(ThesaurusEnum::THESAURUS_TYPE_TINS->value, $params);
    }

    public function thesaurusImage(string $codeCgt): ?string
    {
        return $this->thesaurus(ThesaurusEnum::THESAURUS_LISTE_PICTOS->value, $codeCgt);
    }

    public function gpxRead(string $url): ?string
    {
        return $this->executeRequest($url);
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
}

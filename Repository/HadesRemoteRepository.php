<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\ConnectionHadesTrait;
use AcMarche\Pivot\Filtre\HadesFiltres;
use AcMarche\Pivot\Utils\Cache;
use AcMarche\Pivot\Utils\Mailer;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HadesRemoteRepository
{
    use ConnectionHadesTrait;

    private CacheInterface $cache;

    public function __construct()
    {
        $this->connect();
        $this->cache = Cache::instance();
    }

    /**
     * http://w3.ftlb.be/wiki/index.php/Flux.
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function loadOffresFromFlux(array $args, string $tbl = 'xmlcomplet'): string
    {
        $args['tbl'] = $tbl;
        $args['com_id'] = HadesFiltres::COMMUNE;
        //'reg_id' => Hades::PAYS,
        //'cat_id' => $categorie,
        //'from_datetime'=>'2020-06-26%2012:27:00'
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->url,
                [
                    'query' => $args,
                ]
            );

            return $request->getContent();
        } catch (ClientException $exception) {
           // Mailer::sendError('Erreur avec le xml hades', $exception->getMessage());
            throw  new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * http://w3.ftlb.be/webservice/h2o.php?com_id=263&tbl=xmlcomplet&cat_id=evt_sport,cine_club,conference,exposition,festival,fete_festiv,anim_jeux,livre_conte,manifestatio,foire_brocan,evt_promenad,spectacle,stage_ateli,evt_vis_guid.
     *
     * @throws InvalidArgumentException
     */
    public function getOffresByArgs(array $types = []): string
    {
        $args = [];
        if (\count($types) >= 0) {
            $args = [
                'cat_id' => implode(',', $types),
            ];
        }

        $key = implode('-', $args);

        //  echo($t);
        return $this->cache->get(
            'hebergements_hades_remote'.$key,
            fn () => $this->loadOffresFromFlux($args)
        );
    }

    /**
     * http://w3.ftlb.be/webservice/h2o.php?tbl=xmlcomplet&off_id=84670.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOffreById(string $id): string
    {
        return $this->cache->get(
            'offre_hades_remote_'.$id,
            fn () => $this->loadOffresFromFlux([
                'off_id' => $id,
            ])
        );
    }
}

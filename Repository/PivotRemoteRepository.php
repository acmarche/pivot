<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\ConnectionPivotTrait;
use AcMarche\Pivot\Utils\Cache;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PivotRemoteRepository
{
    use ConnectionPivotTrait;

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
        try {
            $request = $this->httpClient->request(
                'GET',
                $this->base_uri,
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
     * http://w3.ftlb.be/webservice/h2o.php?tbl=xmlcomplet&off_id=84670.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function getOffreById(string $id): string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                $this->base_uri.'/offer/'.$id,
                [
                ]
            );

            return $response->getContent();
        } catch (ClientException $exception) {
            // Mailer::sendError('Erreur avec le xml hades', $exception->getMessage());
            throw  new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function debug(ResponseInterface $request)
    {
        var_dump($request->getInfo('debug'));
    }
}

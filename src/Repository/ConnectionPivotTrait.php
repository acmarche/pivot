<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\FormatEnum;
use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait ConnectionPivotTrait
{
    private HttpClientInterface $httpClient;
    private ?string $code_query;
    private ?string $base_uri;
    private ?string $ws_key;

    public function connect(FormatEnum $output): void
    {
        $this->base_uri = $_ENV['PIVOT_BASE_URI'] ?? null;
        $this->ws_key = $_ENV['PIVOT_WS_KEY'] ?? null;
        $this->code_query = $_ENV['PIVOT_CODE'] ?? null;

        $headers = [
            'headers' => [
                'Accept' => $output->value,
                'ws_key' => $this->ws_key,
            ],
        ];

        $this->httpClient = HttpClient::create($headers);
    }


    /**
     * @throws Exception
     */
    private function executeRequest(string $url, array $options = [], string $method = 'GET'): string
    {
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                $options
            );

            return $response->getContent();
        } catch (ClientException|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            // Mailer::sendError('Erreur avec le xml hades', $exception->getMessage());
            throw  new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function debug(ResponseInterface $response)
    {
        var_dump($response->getInfo('debug'));
    }

}
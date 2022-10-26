<?php

namespace AcMarche\Pivot\Repository;

use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait ConnectionPivotTrait
{
    private HttpClientInterface $httpClient;
    private ?string $code_query = null;
    private ?string $base_uri = null;
    private ?string $ws_key = null;
    public ?string $url_executed = null;
    public ?string $data_raw = null;
    public array $headersCurl = [];

    public function connect(string $output): void
    {
        $this->base_uri = $_ENV['PIVOT_BASE_URI'] ?? null;
        $this->ws_key = $_ENV['PIVOT_WS_KEY'] ?? null;
        $this->code_query = $_ENV['PIVOT_CODE'] ?? null;

        $headers = [
            'headers' => [
                'Accept' => $output,
                'ws_key' => $this->ws_key,
            ],
        ];

        $this->headersCurl = [
            'Accept: '.$output,
            'ws_key: '.$this->ws_key,
        ];

        // $this->httpClient = HttpClient::create($headers);
    }

    /**
     * @throws Exception
     */
    private function executeRequest(string $url, array $options = [], string $method = 'GET'): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headersCurl);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            throw  new Exception(curl_error($ch));
        }

        curl_close($ch);
        $this->data_raw = $output;

        return $output;
    }

    /**
     * @throws Exception
     */
    private function executeRequestSf(string $url, array $options = [], string $method = 'GET'): string
    {
        $this->url_executed = $url;
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                $options
            );

            $this->data_raw = $response->getContent();

            return $this->data_raw;
        } catch (ClientException|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            throw  new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function debug(ResponseInterface $response)
    {
        var_dump($response->getInfo('debug'));
    }

}

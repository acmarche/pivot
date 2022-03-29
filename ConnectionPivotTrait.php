<?php

namespace AcMarche\Pivot;

use AcMarche\Pivot\Utils\Env;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ConnectionPivotTrait
{
    private HttpClientInterface $httpClient;
    private string $code;
    private string $base_uri;
    private string $ws_key;

    public function connect(string $output): void
    {
        Env::loadEnv();
        $this->base_uri = $_ENV['PIVOT_BASE_URI'];
        $this->ws_key = $_ENV['PIVOT_WS_KEY'];
        $this->code = $_ENV['PIVOT_CODE'];

        $headers = [
            'headers' => [
                'Accept' => $output,
                'ws_key' => $this->ws_key,
            ],
        ];

        $this->httpClient = HttpClient::create($headers);
    }
}

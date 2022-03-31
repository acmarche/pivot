<?php

namespace AcMarche\Pivot;

use AcMarche\Pivot\Utils\Env;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ConnectionHadesTrait
{
    private HttpClientInterface $httpClient;
    private string $code;
    private string $url;
    private string $clef;

    public function connect()
    {
        Env::loadEnv();
        $this->url = $_ENV['HADES_URL'];
        $user = $_ENV['HADES_USER'];
        $password = $_ENV['HADES_PASSWORD'];

        $options = [
            'auth_basic' => [$user, $password],
        ];

        $this->httpClient = HttpClient::create($options);
    }
}

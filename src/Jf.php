<?php

namespace AcMarche\Pivot;

use Symfony\Component\Serializer\SerializerInterface;

class Jf
{
    public function __construct(private SerializerInterface $serializer)
    {

    }

    public function test()
    {
        $data = ['nom' => 'jf', 'prenom' => 'sene'];
        dump($this->serializer->serialize($data, 'json'));
    }
}
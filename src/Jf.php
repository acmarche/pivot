<?php

namespace AcMarche\Pivot;

use AcMarche\Pivot\Entities\Person;
use Symfony\Component\Serializer\SerializerInterface;

class Jf
{
    public function __construct(private SerializerInterface $serializer)
    {

    }

    public function test()
    {
        $string = json_encode([
            'nom' => "jf",
            "prenom" => "jennni",
            "adresse" => [
                ["rue" => "rue jolis bois", "numero" => 24],
                ["rue" => "rue cares", "numero" => 12],
            ],
        ]);
        dd($this->serializer);
        dd($this->serializer->deserialize($string, Person::class, 'json'));
    }
}
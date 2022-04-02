<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Person;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerPivot
{
    public function __construct(private SerializerInterface $serializer)
    {
    }
    public function test3()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $serializer = new Serializer(
            [
                new ObjectNormalizer(
                    $classMetadataFactory,
                    null,
                    null,
                    $extractor,
                    new ClassDiscriminatorFromClassMetadata($classMetadataFactory)
                ),
            ],
            ['json' => new JsonEncoder()]
        );

        $actual = $serializer->deserialize('{ "v": { "a": 0 }}', DummyUnionWithAAndB::class, 'json', [
            AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
        ]);
    }

    public function test2()
    {
        $person = new Person();
        $person->setName('foo');
        $person->setAge(99);
        $person->setSportsperson(false);
        $json = json_encode($person);
        var_dump($json);
        $jsonContent = $this->serializer->deserialize($json, Person::class, 'json');
        dump($jsonContent);
    }

    public static function create(): SerializerInterface
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $normalizers = [
            new GetSetMethodNormalizer(),
            new PropertyNormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
            new ObjectNormalizer(null, null, null, new PhpDocExtractor()),
            new ArrayDenormalizer(),
            new DateTimeNormalizer(),
        ];
        $encoders = [
            new XmlEncoder(),
            new JsonEncoder(),
        ];

        return new Serializer($normalizers, $encoders);
    }

    public static function create2(): SerializerInterface
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);

        $normalizers = [$normalizer];

        return new Serializer($normalizers, $encoders);
    }
}

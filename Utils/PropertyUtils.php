<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Libelle;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class PropertyUtils
{
    private PropertyAccessor $propertyAccessor;
    private PropertyInfoExtractor $propertyInfo;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        // a full list of extractors is shown further below
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        // list of PropertyListExtractorInterface (any iterable)
        $listExtractors = [$reflectionExtractor];
        // list of PropertyTypeExtractorInterface (any iterable)
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        // list of PropertyDescriptionExtractorInterface (any iterable)
        $descriptionExtractors = [$phpDocExtractor];
        // list of PropertyAccessExtractorInterface (any iterable)
        $accessExtractors = [$reflectionExtractor];
        // list of PropertyInitializableExtractorInterface (any iterable)
        $propertyInitializableExtractors = [$reflectionExtractor];

        $this->propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
    }

    public function getProperties(string $class, object $object): void
    {
        foreach ($this->propertyInfo->getProperties($class) as $property) {
            $value = $this->getValue($class, $property);
            if ('numero' === $property) {
            }
            $this->propertyAccessor->setValue($object, $property, $value);
        }
    }

    private function getValue(string $class, string $property)
    {
        $types = $this->propertyInfo->getTypes($class, $property);
        if ('lib' === $property) {
            return new Libelle();
        }

        $type = $types[0];
        if ($type->isNullable()) {
            return null;
        }
        if ('string' === $type->getBuiltinType()) {
            return '';
        }
        if ('array' === $type->getBuiltinType()) {
            return [];
        }
        if ('int' === $type->getBuiltinType()) {
            return 0;
        }

        return '';
    }
}

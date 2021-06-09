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

    public function t(string $class, object $object)
    {
        foreach ($this->propertyInfo->getProperties($class) as $property) {
            $value = $this->getValue($class, $property);
            if($property === 'numero') {

            }
            $this->propertyAccessor->setValue($object, $property, $value);
        }
    }

    private function getValue(string $class, string $property)
    {
        $types = $this->propertyInfo->getTypes($class, $property);
        if($property === 'lib'){
            return new Libelle();
        }

        $type = $types[0];
        if ($type->isNullable()) {
            return null;
        }
        if ($type->getBuiltinType() === 'string') {
            return '';
        }
        if ($type->getBuiltinType() === 'array') {
            return [];
        }
        if ($type->getBuiltinType() === 'int') {
            return 0;
        }

        return '';
    }
}

<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Label;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

class PropertyUtils
{
    private readonly PropertyAccessor $propertyAccessor;
    private readonly PropertyInfoExtractor $propertyInfo;

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

    /**
     * Pour eviter erreur php not initialized
     * @return void
     */
    public function initAttributesObject(string $class, object $object): void
    {
        foreach ($this->propertyInfo->getProperties($class) as $property) {
            $value = $this->getTypeOfProperty($class, $property);
            if ('numero' === $property) {
            }
            $this->propertyAccessor->setValue($object, $property, $value);
        }
    }

    private function getTypeOfProperty(string $class, string $property): string|int|null|Label|array
    {
        $types = $this->propertyInfo->getTypes($class, $property);

        if ('lib' === $property) {
            return new Label();
        }
        if (!is_array($types)) {
            return null;
        }
        /**
         * @var Type $type
         */
        $type = $types[0];
        if ($type->isNullable()) {
            return null;
        }

        $builtinType = $type->getBuiltinType();

        return match ($builtinType) {
            Type::BUILTIN_TYPE_STRING => '',
            Type::BUILTIN_TYPE_ARRAY => [],
            Type::BUILTIN_TYPE_INT => 0,
            default => null
        };
    }
}

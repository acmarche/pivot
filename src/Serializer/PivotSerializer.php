<?php

namespace AcMarche\Pivot\Serializer;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Entities\Response\UrnResponse;
use AcMarche\Pivot\Entities\Urn\Urn;
use AcMarche\Pivot\Entities\Urn\UrnDefinition;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class PivotSerializer
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function deserializeToClass(
        string $data,
        string $class,
        string $format = 'json'
    ): Offre|ResultOfferDetail|ResponseQuery|Urn|UrnDefinition|UrnResponse|array|null {
        try {
            return $this->serializer->deserialize($data, $class, $format, [
                DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
            ]);
        } catch (PartialDenormalizationException $exception) {
            $this->getErrors($exception, $data);
        }

        return null;
    }

    public function deserializeOffer(string $data, string $class): ?ResultOfferDetail
    {
        try {
            $t = $this->serializer->deserialize($data, ResultOfferDetail::class, 'json', [
                DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
            ]);

            return $t;
        } catch (PartialDenormalizationException $exception) {
            $this->getErrors($exception, $data);
        }

        return null;
    }

    private function getErrors(\Exception|PartialDenormalizationException $exception, string $data)
    {
        $violations = new ConstraintViolationList();
        /** @var NotNormalizableValueException */
        foreach ($exception->getErrors() as $exception) {
            dump($exception);
            dump($data);
            //todo log it !
            $message = sprintf(
                'The type must be one2 of "%s" ("%s" given).',
                implode(', ', $exception->getExpectedTypes()),
                $exception->getCurrentType()
            );
            $parameters = [];
            if ($exception->canUseMessageForUser()) {
                $parameters['hint'] = $exception->getMessage();
            }
            $violations->add(
                new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null)
            );
        }
    }
}
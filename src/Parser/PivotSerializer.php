<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Hebergement\Hotel;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
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
    ): Offre|Event|Hotel|ResultOfferDetail|ResponseQuery|null {
        try {
            return $this->serializer->deserialize($data, $class, $format, [
                DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
            ]);
        } catch (PartialDenormalizationException $exception) {
            $this->getErrors($exception);
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
            $this->getErrors($exception);
        }

        return null;
    }

    private function getErrors(\Exception|PartialDenormalizationException $exception)
    {
        $violations = new ConstraintViolationList();
        /** @var NotNormalizableValueException */
        foreach ($exception->getErrors() as $exception) {
            dump($exception);
            $message = sprintf(
                'The type must be one of "%s" ("%s" given).',
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
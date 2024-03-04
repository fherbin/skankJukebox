<?php
declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsDecorator(decorates: 'serializer.normalizer.datetime')]
class NullableDateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        #[AutowireDecorated]
        private DateTimeNormalizer $dateTimeNormalizer,
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($data === '') {
            return null;
        }

        return $this->dateTimeNormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): bool {
        return $this->dateTimeNormalizer->supportsDenormalization($data, $type, $format, $context);
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null {

        return $this->dateTimeNormalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->dateTimeNormalizer->supportsNormalization($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->dateTimeNormalizer->getSupportedTypes($format);
    }


}
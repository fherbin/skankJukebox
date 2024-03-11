<?php

namespace App\Tests\Serializer;

use App\Serializer\NullableDateTimeNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class NullableDateTimeNormalizerTest extends TestCase
{
    private NullableDateTimeNormalizer $normalized;

    protected function setUp(): void
    {
        $this->normalized = new NullableDateTimeNormalizer(new DateTimeNormalizer());
    }

    public function testDenormalize(): void
    {
        $this->assertNull($this->normalized->denormalize('', 'Datetime', 'json'));
        $date = new \DateTimeImmutable('1982-10-12');
        $this->assertEquals(
            $date,
            $this->normalized->denormalize($date->format(\DateTimeInterface::ATOM), 'Datetime', 'json')
        );
    }
}

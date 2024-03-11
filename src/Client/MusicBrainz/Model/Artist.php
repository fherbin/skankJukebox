<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

class Artist implements MusicBrainzModelInterface
{
    public function __construct(
        public ?string $id,
        public ?string $type = null,
        public ?int $score = null,
        public ?string $name = null,
        public ?string $gender = null,
        public ?string $country = null,
    ) {
    }
}

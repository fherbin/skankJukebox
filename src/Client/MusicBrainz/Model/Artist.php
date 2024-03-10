<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

class Artist implements MusicBrainzModelInterface
{
    public function __construct(
        public ?string $id,
        public ?string $type,
        public ?int $score,
        public ?string $name,
        public ?string $gender,
        public ?string $country,
    ) {
    }
}

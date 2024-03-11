<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Release implements MusicBrainzModelInterface
{
    public function __construct(
        public string $id,
        public ?int $score = null,
        public ?int $count = null,
        public ?string $title = null,
        public ?string $status = null,
        public ?\DateTimeImmutable $date = null,
        #[SerializedName('track-count')]
        public ?int $trackCount = null,
        /** @var Artist[]|null */
        #[SerializedName('artist-credit')]
        public ?array $artists = null,
    ) {
    }
}

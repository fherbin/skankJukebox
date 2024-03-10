<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Recording implements MusicBrainzModelInterface
{
    public function __construct(
        public string $id,
        public ?int $score,
        public ?string $title,
        public ?int $length,
        #[SerializedName('first-release-date')]
        public ?\DateTimeImmutable $firstReleaseDate,
        /** @var Release[] */
        public ?array $releases,
    ) {
    }
}

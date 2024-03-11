<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Recording implements MusicBrainzModelInterface
{
    public function __construct(
        public string $id,
        public ?int $score = null,
        public ?string $title = null,
        public ?int $length = null,
        #[SerializedName('first-release-date')]
        public ?\DateTimeImmutable $firstReleaseDate = null,
        /** @var Release[] */
        public ?array $releases = null,
    ) {
    }
}

<?php
declare(strict_types=1);

namespace App\Client\MusicBrainz\Model;

use Symfony\Component\Serializer\Attribute\SerializedName;

class Release implements MusicBrainzModelInterface
{
    public function __construct(
        public string $id,
        public ?int $score,
        public ?int $count,
        public ?string $title,
        public ?string $status,
        public ?\DateTimeImmutable $date,
        #[SerializedName('track-count')]
        public ?int $trackCount,
    )
    {
    }
}
<?php
declare(strict_types=1);

namespace App\Client\Model;

class Search implements MusicBrainzModelInterface
{
    public function __construct(
        public int $count,
        /** @var Recording[] */
        public array $recordings = [],
        /** @var Artist[] */
        public array $artists = [],
    )
    {
    }
}
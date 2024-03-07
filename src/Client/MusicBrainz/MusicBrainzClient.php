<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz;

use App\Client\MusicBrainz\Model\{Artist, Recording, Release, Search};
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusicBrainzClient
{
    public function __construct(
        private readonly HttpClientInterface $musicbrainzClient,
        private readonly SerializerInterface $serializer
    ) {
    }

    /** @return Release[] */
    public function searchRelease(string $search): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::RELEASE->value,
            ['query' => ['query' => $search, 'inc' => 'recordings']]
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->releases;
    }

    /** @return Artist[] */
    public function searchArtist(string $search): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::ARTIST->value,
            ['query' => ['query' => $search]]
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->artists;
    }

    /** @return Release[] */
    public function getReleasesByArtist(string $artistId): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::RELEASE->value,
            ['query' => ['query' => 'arid:'.$artistId]],
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->releases;
    }

    /** @return Recording[] */
    public function getRecordingsByRelease(string $releaseId): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::RECORDING->value,
            ['query' => ['query' => 'reid:'.$releaseId]],
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->recordings;
    }
}
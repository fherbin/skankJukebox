<?php

declare(strict_types=1);

namespace App\Client\MusicBrainz;

use App\Client\Model\Artist;
use App\Client\Model\Recording;
use App\Client\Model\Search;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MusicBrainzClient
{
    public function __construct(private HttpClientInterface $musicbrainzClient, private SerializerInterface $serializer)
    {
    }

    /** @return Recording[] */
    public function searchRecordings(string $search): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::RECORDING->value,
            ['query' => ['query' => $search]]
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->recordings;
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

    /** @return Recording[] */
    public function getRecordingsByArtist(string $artistId): array
    {
        $response = $this->musicbrainzClient->request(
            'GET',
            MusicBrainzEntityEnum::RECORDING->value,
            ['query' => ['query' => 'arid:'.$artistId]],
        );

        return $this->serializer->deserialize($response->getContent(), Search::class, 'json')->recordings;
    }
}
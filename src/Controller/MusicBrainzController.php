<?php

declare(strict_types=1);

namespace App\Controller;

use App\Client\MusicBrainz\MusicBrainzClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
readonly class MusicBrainzController
{
    public function __construct(private MusicBrainzClient $client)
    {
    }

    #[Route('search/release/{query}', name: 'release_search')]
    public function searchRelease(string $query): JsonResponse
    {
        return new JsonResponse($this->client->searchRelease($query));
    }

    #[Route('search/artist/{query}', name: 'artist_search')]
    public function searchArtist(string $query): JsonResponse
    {
        return new JsonResponse($this->client->searchArtist($query));
    }

    #[Route('artist/{artistId}/releases', name: 'artist_releases_get')]
    public function searchArtistReleases(string $artistId): JsonResponse
    {
        return new JsonResponse($this->client->getReleasesByArtist($artistId));
    }

    #[Route('release/{releaseId}/recordings', name: 'release_recordings_get')]
    public function getRecordingsByRelease(string $releaseId): JsonResponse
    {
        return new JsonResponse($this->client->getRecordingsByRelease($releaseId));
    }
}

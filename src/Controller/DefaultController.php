<?php
declare(strict_types=1);

namespace App\Controller;

use App\Client\MusicBrainz\MusicBrainzClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class DefaultController
{
    public function __construct(private MusicBrainzClient $client)
    {
    }

    #[Route('search/recording/{query}', name: 'recording_search')]
    public function searchRecording(string $query): JsonResponse
    {
        return new JsonResponse($this->client->searchRecordings($query));
    }

    #[Route('search/artist/{query}', name: 'artist_search')]
    public function searchArtist(string $query): JsonResponse
    {
        return new JsonResponse($this->client->searchArtist($query));
    }

    #[Route('search/artist/{artistId}/recordings', name: 'artist_recordings_search')]
    public function searchArtistRecordings(string $artistId): JsonResponse
    {
        return new JsonResponse($this->client->getRecordingsByArtist($artistId));
    }
}
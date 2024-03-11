<?php

namespace App\Tests\Twig\CdLoader;

use App\Client\MusicBrainz\Model\Artist;
use App\Client\MusicBrainz\Model\Recording;
use App\Client\MusicBrainz\Model\Release;
use App\Client\MusicBrainz\MusicBrainzClient;
use App\Entity\Cd;
use App\Entity\Slot;
use App\Entity\Track;
use App\Repository\SlotRepository;
use App\Twig\CdLoader\SearchCd;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchCdTest extends KernelTestCase
{
    private MockObject $client;
    private MockObject $entityManager;
    private Session $session;
    private SearchCd $component;
    private MockObject $repository;

    protected function setUp(): void
    {
        $this->component = $this->createPartialMock(SearchCd::class, ['addFlash', 'redirectToRoute']);
        $this->component->__construct(
            $this->client = $this->createMock(MusicBrainzClient::class),
            $requestStack = $this->createMock(RequestStack::class),
            $translator = $this->createMock(TranslatorInterface::class),
            $this->entityManager = $this->createMock(EntityManagerInterface::class),
            new NullLogger(),
        );
        $requestStack->method('getSession')
            ->willReturn($this->session = new Session(new MockArraySessionStorage()));
        $translator->method('trans')
            ->willReturn('translated string');
        $this->entityManager->method('getRepository')
            ->with(Slot::class)->willReturn($this->repository = $this->createMock(SlotRepository::class));
    }

    public function testLoadCdByArtist(): void
    {
        // search artist
        $this->component->search = 'search an artist';
        $this->client->expects($this->once())->method('searchArtist')
            ->with('search an artist')->willReturn([new Artist('artist-id')]);
        $this->component->searchArtist();

        // search artist releases
        $release = new Release(id: 'release-id', title: 'release-title', artists: [
            new Artist(
                id: 'artist-id',
                name: 'artist-name'
            ),
        ]);
        $this->client->expects($this->once())->method('getReleasesByArtist')
            ->with('artist-id')->willReturn([$release]);
        $this->component->getReleasesByArtist('artist-id');

        // search release recordings
        $this->client->expects($this->once())->method('getRecordingsByRelease')
            ->with('release-id')->willReturn([new Recording(id: 'recording-id', title: 'recording-title')]);
        $this->repository->expects($this->once())->method('getAll')
            ->willReturn([(new Slot())->setNumber(1)]);
        $this->component->getRecordingsByRelease('release-id');

        // persist
        $expectedCd = (new Cd())
            ->setName('release-title')
            ->setArtist('artist-name')
            ->addTrack((new Track())->setName('recording-title'));
        $this->entityManager->expects($this->once())->method('persist')->with($expectedCd);
        $this->entityManager->expects($this->once())->method('flush');

        $response = $this->component->persist();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(0, $this->session->count());
    }

    public function testLoadCdByRelease(): void
    {
        $release = new Release(id: 'release-id', title: 'release-title', artists: [
            new Artist(
                id: 'artist-id',
                name: 'artist-name'
            ),
        ]);
        // search release
        $this->component->search = 'search a release';
        $this->client->expects($this->once())->method('searchRelease')
            ->with('search a release')->willReturn([$release]);
        $this->component->searchRelease();

        // search release recordings
        $this->client->expects($this->once())->method('getRecordingsByRelease')
            ->with('release-id')->willReturn([new Recording(id: 'recording-id', title: 'recording-title')]);
        $this->repository->expects($this->once())->method('getAll')
            ->willReturn([(new Slot())->setNumber(1)]);
        $this->component->getRecordingsByRelease('release-id');

        // persist
        $expectedCd = (new Cd())
            ->setName('release-title')
            ->setArtist('artist-name')
            ->addTrack((new Track())->setName('recording-title'));
        $this->entityManager->expects($this->once())->method('persist')->with($expectedCd);
        $this->entityManager->expects($this->once())->method('flush');

        $response = $this->component->persist();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(0, $this->session->count());
    }
}

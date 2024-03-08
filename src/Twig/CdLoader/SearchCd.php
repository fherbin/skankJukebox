<?php

namespace App\Twig\CdLoader;

use App\Client\MusicBrainz\Model\Artist;
use App\Client\MusicBrainz\Model\Recording;
use App\Client\MusicBrainz\Model\Release;
use App\Client\MusicBrainz\MusicBrainzClient;
use App\Client\MusicBrainz\MusicBrainzEntityEnum;
use App\Entity\Cd;
use App\Entity\Track;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'cd-loader:search-cd')]
class SearchCd extends AbstractController
{
    use DefaultActionTrait;


    #[LiveProp(writable: true)]
    public string $search = '';

    #[LiveProp(writable: true)]
    public string $musicBrainzEntity = MusicBrainzEntityEnum::ARTIST->name;

    /** @var Artist[] */
    public array $artists = [];

    /** @var Release[] */
    public array $releases = [];

    /** @var Recording[] */
    public array $recordings = [];

    public function __construct(
        private readonly MusicBrainzClient $client,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[LiveAction]
    public function persist(): RedirectResponse
    {
        try {
            $this->addFlash('success',$this->translator->trans('page.cd-loader.flash.success'));
            $cd = $this->getCdFromSession();
            $this->reset();
            //TODO persist
        } catch (\Throwable $throwable) {
            $this->addFlash('danger', $this->translator->trans('page.cd-loader.flash.fail').$throwable->getMessage());
        }

        return $this->redirectToRoute('cd-loader');
    }

    #[LiveAction]
    public function searchArtist(): void
    {
        $this->reset();
        $this->artists = $this->client->searchArtist($this->search);
    }

    #[LiveAction]
    public function searchRelease(): void
    {
        $this->reset();
        $this->releases = $this->client->searchRelease($this->search);
        $this->setTmpReleases();
    }

    #[LiveAction]
    public function getReleasesByArtist(#[LiveArg] string $artistId): void
    {
        $this->artists = $this->recordings = [];
        $this->releases = $this->client->getReleasesByArtist($artistId);
        if ($this->releases) {
            $this->requestStack->getSession()->set('artist-name', $this->releases[0]->artists[0]->name);
            $this->setTmpReleases();
        }
    }

    #[LiveAction]
    public function getRecordingsByRelease(#[LiveArg] string $releaseId): void
    {
        $this->artists = $this->releases = [];
        $this->recordings = $this->client->getRecordingsByRelease($releaseId);
        $session = $this->requestStack->getSession();
        $session->set('release-name', $session->get('releases')[$releaseId]->title);
        $session->set('recordings', $this->recordings);
    }

    private function reset(): void
    {
        $this->artists = $this->releases = $this->recordings = [];
        $this->requestStack->getSession()->remove('artist-name');
        $this->requestStack->getSession()->remove('release-name');
        $this->requestStack->getSession()->remove('releases');
        $this->requestStack->getSession()->remove('recordings');
    }

    private function setTmpReleases(): void
    {
        $this->requestStack->getSession()->set(
            'releases',
            array_combine(
                array_map(
                    fn(Release $release) => $release->id,
                    $this->releases
                ),
                $this->releases
            )
        );
    }

    private function getCdFromSession(): Cd
    {
        $sessionContent = $this->requestStack->getSession()->all();

        $cd = (new Cd())
            ->setName($sessionContent['release-name'])
            ->setArtist($sessionContent['artist-name']);

        /** @var Recording $recording */
        foreach ($sessionContent['recordings'] as $key => $recording) {
            $cd->addTrack(
                (new Track())
                    ->setName($recording->title)
                    ->setLength($recording->length)
            );
        }

        return $cd;
    }

}

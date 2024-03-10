<?php

namespace App\Twig\Remote;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'remote:cd-list')]
class CdList extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $search = '';

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $perPage = 9;

    /** @var Paginator<Slot>|null */
    public ?Paginator $slotsPaginated = null;

    public int $maxPage = 0;

    public function __construct(
        private readonly SlotRepository $slotRepository,
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /** @return Paginator<Slot> */
    public function getSlots(): Paginator
    {
        try {
            $this->slotsPaginated = $this->slotRepository->getChargedPaginated($this->page, $this->perPage, $this->search);
            $this->maxPage = ceil($this->slotsPaginated->count() / $this->perPage);
        } catch (\Throwable $throwable) {
            $message = $this->translator->trans('page.remote.cd-list.flash.fail').$throwable->getMessage();
            $this->addFlash('danger', $message);
            $this->logger->error($message);

            $this->redirectToRoute('remote');
        }

        return $this->slotsPaginated;
    }
}

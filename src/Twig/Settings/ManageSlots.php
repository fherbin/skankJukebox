<?php

namespace App\Twig\Settings;

use App\Exception\InvalidSlotNumberArgumentException;
use App\Repository\SlotRepository;
use App\Service\SlotService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'settings:manage-slots')]
class ManageSlots extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $slotNumber;

    public function __construct(
        readonly SlotRepository $slotRepository,
        private readonly LoggerInterface $logger,
        private readonly SlotService $slotService,
        private readonly TranslatorInterface $translator,
    ) {
        $this->slotNumber = $this->slotRepository->count();
    }

    #[LiveAction]
    public function persist(): RedirectResponse
    {
        try {
            if ($this->slotNumber < 1 || $this->slotNumber > 1000) {
                $this->addFlash('warning', $this->translator->trans('page.settings.manage-slots.flash.range'));

                return $this->redirectToRoute('settings');
            }
            $this->slotService->updateSlotsNumber($this->slotNumber);
            $message = sprintf($this->translator->trans('page.settings.manage-slots.flash.success'), $this->slotNumber);
            $this->logger->info($message);
            $this->addFlash('success', $message);
        } catch (InvalidSlotNumberArgumentException $throwable) {
            $message = $this->translator->trans('page.settings.manage-slots.flash.fail').$throwable->getMessage();
            $this->logger->warning($message);
            $this->addFlash('warning', $message);
        } catch (\Throwable $throwable) {
            $message = $this->translator->trans('page.settings.manage-slots.flash.fail').$throwable->getMessage();
            $this->logger->error($message);
            $this->addFlash('danger', $message);
        }

        return $this->redirectToRoute('settings');
    }
}

<?php

namespace App\Service;

use App\Entity\Slot;
use App\Exception\InvalidSlotNumberArgumentException;
use Doctrine\ORM\EntityManagerInterface;

class SlotService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function updateSlotsNumber(int $number): void
    {
        $slotRepository = $this->entityManager->getRepository(Slot::class);
        $actualSlotNumber = $slotRepository->count();
        if ($number === $actualSlotNumber) {
            throw new InvalidSlotNumberArgumentException('The number of slots must be different from the current number.');
        }
        $firstNewSlot = $actualSlotNumber + 1;
        if ($number < $actualSlotNumber) {
            foreach (range($number + 1, $actualSlotNumber) as $slotNumber) {
                $this->entityManager->remove($slotRepository->findOneByNumber($slotNumber));
            }
            $this->entityManager->flush();

            return;
        }
        foreach (range($firstNewSlot, $number) as $slotNumber) {
            $this->entityManager->persist((new Slot())->setNumber($slotNumber));
        }
        $this->entityManager->flush();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Slot;
use App\Exception\InvalidSlotNumberArgumentException;
use App\Repository\SlotRepository;
use App\Service\SlotService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SlotServiceTest extends TestCase
{
    private SlotService $service;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        $this->service = new SlotService(
            $this->entityManager = $this->createMock(EntityManagerInterface::class)
        );
    }

    #[DataProvider('getSlots')]
    public function testUpdateSlotsNumber(
        int $number,
        int $actualSlotNumber,
        int $removeNumber,
        int $persistNumber,
        bool $throw
    ): void {
        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->willReturn($repository = $this->createMock(SlotRepository::class));
        $repository->expects(self::once())
            ->method('count')
            ->willReturn($actualSlotNumber);
        $repository->expects(self::exactly($removeNumber))
            ->method('__call')
            ->with('findOneByNumber')
            ->willReturn(new Slot());
        $this->entityManager->expects(self::exactly($removeNumber))
            ->method('remove');
        $this->entityManager->expects(self::exactly($persistNumber))
            ->method('persist');
        if ($throw) {
            $this->expectException(InvalidSlotNumberArgumentException::class);
        } else {
            $this->entityManager->expects(self::once())
                ->method('flush');
        }
        $this->service->updateSlotsNumber($number);
    }

    public static function getSlots(): \Generator
    {
        yield [0, 0, 0, 0, true];
        yield [40, 40, 0, 0, true];
        yield [50, 100, 50, 0, false];
        yield [100, 50, 0, 50, false];
    }
}

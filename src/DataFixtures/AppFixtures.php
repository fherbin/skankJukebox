<?php

namespace App\DataFixtures;

use App\Entity\Cd;
use App\Entity\Slot;
use App\Entity\Track;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const SLOT_NUMER = 300;
    private const BATCH_COUNT = 100;


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $total = $batchCount = 0;
        foreach (range(1, self::SLOT_NUMER) as $slotNumber) {
            $slot = (new Slot())->setNumber($slotNumber);
            $cd = (new Cd())
                ->setName($faker->realTextBetween(10, 75))
                ->setArtist($faker->name())
                ->setSlot($slot);
            foreach (range(1, 10) as $trackNumber) {
                $cd->addTrack(
                    (new Track())
                        ->setName($faker->realTextBetween(10, 75))
                        ->setLength(9999)
                );
            }
            $manager->persist($slot);
            $batchCount++;
            $total++;
            if ($batchCount >= self::BATCH_COUNT) {
                $manager->flush();
                $manager->clear();
                printf($total." Slot fixtures loaded ...\n");
                $batchCount = 0;
            }
        }
        $manager->flush();
        $manager->clear();
        printf($total." Slot fixtures loaded ...\n");
    }
}

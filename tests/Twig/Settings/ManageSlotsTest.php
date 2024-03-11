<?php

namespace App\Tests\Twig\Settings;

use App\Tests\Utils\DropDatabase;
use App\Twig\Settings\ManageSlots;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class ManageSlotsTest extends KernelTestCase
{
    use DropDatabase;
    use InteractsWithLiveComponents;

    protected function setUp(): void
    {
        $this->dropDatabase();
    }

    public function testPersist(): void
    {
        $testComponent = $this->createLiveComponent(ManageSlots::class);
        /** @var ManageSlots $component */
        $component = $testComponent->component();
        $this->assertSame(0, $component->slotNumber);
        $testComponent->set('slotNumber', 300);
        $response = $testComponent->call('persist')->response();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode(), $response->getContent() ?: 'no response');

        $testComponent = $this->createLiveComponent(ManageSlots::class);
        /** @var ManageSlots $component */
        $component = $testComponent->component();
        $this->assertSame(300, $component->slotNumber);
        $testComponent->set('slotNumber', 150);
        $testComponent->call('persist');

        $testComponent = $this->createLiveComponent(ManageSlots::class);
        /** @var ManageSlots $component */
        $component = $testComponent->component();
        $this->assertSame(150, $component->slotNumber);
        $testComponent->set('slotNumber', 200);
        $testComponent->call('persist');

        $testComponent = $this->createLiveComponent(ManageSlots::class);
        /** @var ManageSlots $component */
        $component = $testComponent->component();
        $this->assertSame(200, $component->slotNumber);
    }
}

<?php

namespace Tests\Unit\Service\BookKeepingMigrationTools;

use App\Service\BookKeepingMigrationTools;
use Mockery;
use Tests\TestCase;

class IsSourceLaterTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_returns_true_because_the_source_is_later(): void
    {
        $sourceUpdateAt = '2024-02-25T08:39:02+09:00';
        $destinationUpdateAt = '2023-02-25T08:39:02+09:00';

        $service = new BookKeepingMigrationTools();
        $isSourceLater_actual = $service->isSourceLater($sourceUpdateAt, $destinationUpdateAt);

        $this->assertTrue($isSourceLater_actual);
    }

    public function test_it_returns_true_because_the_destination_is_invalid(): void
    {
        $sourceUpdateAt = '2024-02-25T08:39:02+09:00';
        $destinationUpdateAt = '2023-02-25 08:39:02+09:00';

        $service = new BookKeepingMigrationTools();
        $isSourceLater_actual = $service->isSourceLater($sourceUpdateAt, $destinationUpdateAt);

        $this->assertTrue($isSourceLater_actual);
    }

    public function test_it_returns_true_because_the_destination_is_null(): void
    {
        $sourceUpdateAt = '2024-02-25T08:39:02+09:00';
        $destinationUpdateAt = null;

        $service = new BookKeepingMigrationTools();
        $isSourceLater_actual = $service->isSourceLater($sourceUpdateAt, $destinationUpdateAt);

        $this->assertTrue($isSourceLater_actual);
    }

    public function test_it_returns_false_because_source_is_invalid(): void
    {
        $sourceUpdateAt = '2024-02-25 08:39:02+09:00';
        $destinationUpdateAt = '2023-02-25 08:39:02+09:00';

        $service = new BookKeepingMigrationTools();
        $isSourceLater_actual = $service->isSourceLater($sourceUpdateAt, $destinationUpdateAt);

        $this->assertFalse($isSourceLater_actual);
    }

    public function test_it_returns_false_because_source_is_null(): void
    {
        $sourceUpdateAt = null;
        $destinationUpdateAt = '2023-02-25 08:39:02+09:00';

        $service = new BookKeepingMigrationTools();
        $isSourceLater_actual = $service->isSourceLater($sourceUpdateAt, $destinationUpdateAt);

        $this->assertFalse($isSourceLater_actual);
    }
}

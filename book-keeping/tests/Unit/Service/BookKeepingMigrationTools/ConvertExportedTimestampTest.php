<?php

namespace Tests\Unit\Service\BookKeepingMigrationTools;

use App\Service\BookKeepingMigrationTools;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ConvertExportedTimestampTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_convert_the_format_of_the_string(): void
    {
        $createdAt_expected = '2023-12-09T21:01:01+00:00';

        $service = new BookKeepingMigrationTools();
        $createdAt_actual = $service->convertExportedTimestamp($createdAt_expected);

        $this->assertSame($createdAt_expected, $createdAt_actual);
    }
}

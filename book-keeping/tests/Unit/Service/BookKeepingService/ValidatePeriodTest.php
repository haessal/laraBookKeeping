<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class ValidatePeriodTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    /**
     * @dataProvider forTestValidatePeriod
     */
    public function test_it_validates_the_specified_period($fromDate, $toDate, $success_expected): void
    {
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        Carbon::setTestNow(new Carbon('2020-05-03 09:59:59'));

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $success_actual = $BookKeeping->validatePeriod($fromDate, $toDate);

        $this->assertSame($success_expected, $success_actual);
    }

    public static function forTestValidatePeriod()
    {
        return [
            [null,         null,         true],
            ['',           '',           true],
            ['',           '2020-04-31', false],
            ['',           '2020-05-01', true],
            ['2020-02-31', '',           false],
            ['2020-02-31', '2020-04-31', false],
            ['2020-02-31', '2020-05-01', false],
            ['2020-03-01', '',           true],
            ['2020-03-01', '2020-04-31', false],
            ['2020-03-01', '2020-05-01', true],
            ['2020-04-01', '2020-04-02', true],
            ['2020-04-01', '2020-04-01', true],
            ['2020-04-01', '2020-03-31', false],
            ['2020-05-02', '',           true],
            ['2020-05-03', '',           true],
            ['2020-05-04', '',           false],
            ['',           '1970-01-01', false],
            ['',           '1970-01-02', true],
            ['',           '1970-01-02', true],
            ['1970-01-01', '2020-05-05', true],
        ];
    }
}

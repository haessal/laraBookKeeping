<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Mockery;
use Tests\TestCase;

class ValidateDateFormatTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @dataProvider forTestValidateDateFormat
     */
    public function test_it_validates_the_format_of_the_specified_date($date, $success_expected): void
    {
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $success_actual = $BookKeeping->validateDateFormat($date);

        $this->assertSame($success_expected, $success_actual);
    }

    public static function forTestValidateDateFormat()
    {
        return [
            ['2019-01-32', false],
            ['2019-02-29', false],
            ['2020-02-29', true],
            ['2020-04-31', false],
            ['2020-03-01', true],
            ['2020/03/01', false],
            ['20200301',   false],
            ['2020-03-1',  false],
            ['2020-3-1',   false],
            ['2020-3-01',  false],
        ];
    }
}

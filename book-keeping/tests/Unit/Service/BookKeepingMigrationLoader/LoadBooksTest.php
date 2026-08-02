<?php

namespace Tests\Unit\Service\BookKeepingMigrationLoader;

use App\Service\AccountMigrationLoaderService;
use App\Service\BookKeepingMigrationLoader;
use App\Service\BookKeepingService;
use App\Service\BookMigrationLoaderService;
use App\Service\CreditCardStatementMigrationLoaderService;
use App\Service\SlipMigrationLoaderService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadBooksTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_the_books_with_the_loaded_contents_in_v2_0_format(): void
    {
        $userId = 24;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName31',
        ];
        $bookResult = [
            'bookId' => $bookId,
            'result' => 'created',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $accountsResult = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'result' => 'created',
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'result' => 'created',
                    ],
                ],
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $slipsResult = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'result' => 'created',
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'result' => 'created',
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_NORMAL,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [
                        'book' => $bookResult,
                        'accounts' => $accountsResult,
                        'slips' => $slipsResult,
                    ],
                ],
            ],
            null,
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(null);
        $bookMock->shouldNotReceive('retrieveBook');  // call from isImportable
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([$bookResult, null]);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldReceive('loadAccounts')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.0';
            }), $bookId, $accounts)
            ->andReturn([$accountsResult, null]);
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldReceive('loadSlips')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.0';
            }), $bookId, $slips)
            ->andReturn([$slipsResult, null]);
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_updates_the_books_with_the_loaded_contents_in_v2_1_0_format(): void
    {
        $userId = 162;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName169',
        ];
        $bookResult = [
            'bookId' => $bookId,
            'result' => 'updated',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $accountsResult = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'result' => 'updated',
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'result' => 'updated',
                    ],
                ],
            ],
        ];
        $creditCardStatementId_1 = (string) Str::uuid();
        $creditCardStatements = [
            $creditCardStatementId_1 => [
                'credit_card_statement_id' => $creditCardStatementId_1,
            ],
        ];
        $creditCardStatementsResult = [
            $creditCardStatementId_1 => [
                'credit_card_statement_id' => $creditCardStatementId_1,
                'result' => 'updated',
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $slipsResult = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'result' => 'updated',
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'result' => 'updated',
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.1.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'creditCardStatements' => $creditCardStatements,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_NORMAL,
            [
                'version' => '2.1.0',
                'books' => [
                    $bookId => [
                        'book' => $bookResult,
                        'accounts' => $accountsResult,
                        'creditCardStatements' => $creditCardStatementsResult,
                        'slips' => $slipsResult,
                    ],
                ],
            ],
            null,
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => true]);
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([$bookResult, null]);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldReceive('loadAccounts')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.1.0';
            }), $bookId, $accounts)
            ->andReturn([$accountsResult, null]);
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldReceive('loadSlips')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.1.0';
            }), $bookId, $slips)
            ->andReturn([$slipsResult, null]);
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldReceive('loadCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatements)
            ->andReturn([$creditCardStatementsResult, null]);

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_contents_does_not_have_the_version(): void
    {
        $contents = [];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [],
            'invalid data format: version',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldNotReceive('loadInformation');
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_contents_does_not_have_the_books(): void
    {
        $contents = [
            'version' => '2.0',
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.0',
            ],
            'invalid data format: books',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldNotReceive('loadInformation');
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_books_does_not_have_its_id(): void
    {
        $bookId = (string) Str::uuid();
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [],
                ],
            ],
            'invalid data format: book_id',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldNotReceive('loadInformation');
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_user_does_not_have_permission_to_find_the_book(): void
    {
        $userId = 356;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [],
                ],
            ],
            'The book is already exist and prohibit to write. '.$bookId,
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(null);
        $bookMock->shouldNotReceive('loadInformation');
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_user_does_not_have_permission_to_write_the_book(): void
    {
        $userId = 356;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [],
                ],
            ],
            'The book is already exist and prohibit to write. '.$bookId,
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => false]);
        $bookMock->shouldNotReceive('loadInformation');
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_loads_the_books_but_the_information_can_not_be_updated(): void
    {
        $userId = 480;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName487',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [
                        'book' => ['bookId' => null, 'result' => null],
                    ],
                ],
            ],
            'invalid data format: book',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => true]);
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([['bookId' => null, 'result' => null], 'invalid data format: book']);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldNotReceive('loadAccounts');
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_loads_the_books_but_the_accounts_can_not_be_updated(): void
    {
        $userId = 581;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName588',
        ];
        $bookResult = [
            'bookId' => $bookId,
            'result' => 'updated',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [
                        'book' => $bookResult,
                        'accounts' => [],
                    ],
                ],
            ],
            'invalid data format: account_group_id',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => true]);
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([$bookResult, null]);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldReceive('loadAccounts')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.0';
            }), $bookId, $accounts)
            ->andReturn([[], 'invalid data format: account_group_id']);
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_loads_the_books_but_the_slips_can_not_be_updated(): void
    {
        $userId = 690;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName697',
        ];
        $bookResult = [
            'bookId' => $bookId,
            'result' => 'updated',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $accountsResult = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'result' => 'updated',
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'result' => 'updated',
                    ],
                ],
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.0',
                'books' => [
                    $bookId => [
                        'book' => $bookResult,
                        'accounts' => $accountsResult,
                        'slips' => [],
                    ],
                ],
            ],
            'invalid data format: slip_id',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => true]);
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([$bookResult, null]);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldReceive('loadAccounts')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.0';
            }), $bookId, $accounts)
            ->andReturn([$accountsResult, null]);
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldReceive('loadSlips')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.0';
            }), $bookId, $slips)
            ->andReturn([[], 'invalid data format: slip_id']);
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldNotReceive('loadCreditCardStatements');

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_loads_the_books_but_the_credit_card_statements_can_not_be_updated(): void
    {
        $userId = 877;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookId = (string) Str::uuid();
        $bookInformation = [
            'book_id' => $bookId,
            'book_name' => 'bookName884',
        ];
        $bookResult = [
            'bookId' => $bookId,
            'result' => 'updated',
        ];
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItemId_1 = (string) Str::uuid();
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
        ];
        $accounts = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $accountsResult = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'result' => 'updated',
                'items' => [
                    [
                        'account_id' => $accountItemId_1,
                        'result' => 'updated',
                    ],
                ],
            ],
        ];
        $creditCardStatementId_1 = (string) Str::uuid();
        $creditCardStatements = [
            $creditCardStatementId_1 => [
                'credit_card_statement_id' => $creditCardStatementId_1,
            ],
        ];
        $slipId_1 = (string) Str::uuid();
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
        ];
        $slips = [
            [
                'slip_id' => $slipId_1,
                'slips' => $slip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $contents = [
            'version' => '2.1.0',
            'books' => [
                $bookId => [
                    'book_id' => $bookId,
                    'book' => $bookInformation,
                    'accounts' => $accounts,
                    'creditCardStatements' => $creditCardStatements,
                    'slips' => $slips,
                ],
            ],
        ];
        $result_expected = [
            BookKeepingService::STATUS_ERROR_BAD_CONDITION,
            [
                'version' => '2.1.0',
                'books' => [
                    $bookId => [
                        'book' => $bookResult,
                        'accounts' => $accountsResult,
                        'creditCardStatements' => [],
                    ],
                ],
            ],
            'invalid data format: credit card statement',
        ];
        /** @var \App\Service\BookMigrationLoaderService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookMigrationLoaderService::class);
        $bookMock->shouldReceive('retrieveInformationOf')  // call from isImportable
            ->once()
            ->with($bookId)
            ->andReturn(['book_id' => $bookId]);
        $bookMock->shouldReceive('retrieveBook')  // call from isImportable
            ->once()
            ->with($bookId, $userId)
            ->andReturn(['modifiable' => true]);
        $bookMock->shouldReceive('loadInformation')
            ->once()
            ->with($userId, $bookInformation)
            ->andReturn([$bookResult, null]);
        /** @var \App\Service\AccountMigrationLoaderService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountMigrationLoaderService::class);
        $accountMock->shouldReceive('loadAccounts')
            ->once()
            ->with(Mockery::on(function ($version) {
                return $version->toString() == '2.1.0';
            }), $bookId, $accounts)
            ->andReturn([$accountsResult, null]);
        /** @var \App\Service\SlipMigrationLoaderService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipMigrationLoaderService::class);
        $slipMock->shouldNotReceive('loadSlips');
        /** @var \App\Service\CreditCardStatementMigrationLoaderService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementMigrationLoaderService::class);
        $creditCardStatementMock->shouldReceive('loadCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatements)
            ->andReturn([[], 'invalid data format: credit card statement']);

        $service = new BookKeepingMigrationLoader($bookMock, $accountMock, $slipMock, $creditCardStatementMock);
        $result_actual = $service->loadBooks($contents);

        $this->assertSame($result_expected, $result_actual);
    }
}

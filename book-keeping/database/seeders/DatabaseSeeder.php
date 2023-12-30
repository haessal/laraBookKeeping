<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Budget;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\SlipGroup;
use App\Models\SlipGroupEntry;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $userIds = $this->createUser(4);
        $bookIds = $this->createBook(4);
        $this->createPermission($userIds, $bookIds);
    }

    /**
     * Seed the registered user.
     *
     * @param  int  $count
     * @return array<int, int>
     */
    private function createUser($count): array
    {
        $users = User::factory($count)->create();
        foreach ($users as $user) {
            $userIds[] = $user->id;
        }

        return $userIds;
    }

    /**
     * Seed the book and its contents.
     *
     * @param  int  $count
     * @return array<int, string>
     */
    private function createBook($count): array
    {
        $books = Book::factory($count)->create();
        foreach ($books as $book) {
            $this->createBookContents($book->book_id);
            $bookIds[] = $book->book_id;
        }

        return $bookIds;
    }

    /**
     * Seed the book contents.
     *
     * @param  string  $bookId
     * @return void
     */
    private function createBookContents($bookId)
    {
        $accountIds = $this->createAccount($bookId);
        $this->createBudget($bookId, $accountIds);
        $slipIds = $this->createSlip($bookId, $accountIds);
        $this->createSlipGroup($bookId, $slipIds);
    }

    /**
     * Seed the accout item and group.
     *
     * @param  string  $bookId
     * @return array<int, string>
     */
    private function createAccount($bookId): array
    {
        $accountIds = [];
        $currentAssetGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'asset',
            'is_current' => true,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $currentAssetGroups->account_group_id,
            'selectable' => true,
        ]);
        $nonCurrentAssetGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'asset',
            'is_current' => false,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $nonCurrentAssetGroups->account_group_id,
            'selectable' => true,
        ]);
        $currentLiabilityGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'liability',
            'is_current' => true,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $currentLiabilityGroups->account_group_id,
            'selectable' => true,
        ]);
        $nonCurrentLiabilityGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'liability',
            'is_current' => false,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $nonCurrentLiabilityGroups->account_group_id,
            'selectable' => true,
        ]);
        $expenseGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'expense',
            'is_current' => false,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $expenseGroups->account_group_id,
            'selectable' => true,
        ]);
        $revenueGroups = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'revenue',
            'is_current' => false,
        ]);
        $accounts[] = Account::factory()->create([
            'account_group_id' => $revenueGroups->account_group_id,
            'selectable' => true,
        ]);
        foreach ($accounts as $account) {
            $accountIds[] = $account->account_id;
        }

        return $accountIds;
    }

    /**
     * Seed the budget.
     *
     * @param  string  $bookId
     * @param  array<int, string>  $accountIds
     * @return void
     */
    private function createBudget($bookId, array $accountIds)
    {
        foreach ($accountIds as $accountId) {
            Budget::factory()->create([
                'book_id' => $bookId,
                'account_code' => $accountId,
            ]);
        }
    }

    /**
     * Seed the slip and its entry.
     *
     * @param  string  $bookId
     * @param  array<int, string>  $accountIds
     * @return array<int, string>
     */
    private function createSlip($bookId, array $accountIds): array
    {
        $slipIds = [];
        $slips = Slip::factory(2)->create([
            'book_id' => $bookId,
            'is_draft' => true,
        ]);
        foreach ($slips as $slip) {
            $slipIds[] = $slip->slip_id;
        }
        shuffle($accountIds);
        SlipEntry::factory()->create([
            'slip_id' => $slipIds[0],
            'debit' => $accountIds[0],
            'credit' => $accountIds[1],
        ]);
        $debitIds = $accountIds;
        $creditIds = $accountIds;
        foreach ($debitIds as $debitId) {
            foreach ($creditIds as $creditId) {
                if ($debitId != $creditId) {
                    SlipEntry::factory()->create([
                        'slip_id' => $slipIds[1],
                        'debit' => $debitId,
                        'credit' => $creditId,
                    ]);
                }
            }
        }

        return $slipIds;
    }

    /**
     * Seed the slip group and its entry.
     *
     * @param  string  $bookId
     * @param  array<int, string>  $slipIds
     * @return void
     */
    private function createSlipGroup(string $bookId, array $slipIds)
    {
        $slipGroup = SlipGroup::factory()->create([
            'book_id' => $bookId,
        ]);
        foreach ($slipIds as $slipId) {
            SlipGroupEntry::factory()->create([
                'slip_group_id' => $slipGroup->slip_group_id,
                'related_slip' => $slipId,
            ]);
        }
    }

    /**
     * Seed the permission.
     *
     * @param  array<int, int>  $userIds
     * @param  array<int, string>  $bookIds
     * @return void
     */
    private function createPermission(array $userIds, array $bookIds)
    {
        Permission::factory()->create([
            'permitted_user' => $userIds[0],
            'readable_book' => $bookIds[0],
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        Permission::factory()->create([
            'permitted_user' => $userIds[0],
            'readable_book' => $bookIds[3],
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $userIds[0],
            'readable_book' => $bookIds[1],
            'modifiable' => true,
            'is_owner' => false,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $userIds[0],
            'readable_book' => $bookIds[2],
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $userIds[1],
            'readable_book' => $bookIds[1],
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        Permission::factory()->create([
            'permitted_user' => $userIds[2],
            'readable_book' => $bookIds[2],
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
    }
}

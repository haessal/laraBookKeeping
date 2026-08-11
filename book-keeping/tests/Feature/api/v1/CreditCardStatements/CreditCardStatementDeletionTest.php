<?php

namespace Tests\Feature\api\v1\SlipEntries;

use App\Models\DataProvider\Eloquent\Account;
use App\Models\DataProvider\Eloquent\AccountGroup;
use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\CreditCardStatement;
use App\Models\DataProvider\Eloquent\Permission;
use App\Models\DataProvider\Eloquent\Slip;
use App\Models\DataProvider\Eloquent\SlipEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreditCardStatementDeletionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\DataProvider\Eloquent\Book */
    private $book;

    /** @var \App\Models\DataProvider\Eloquent\Book */
    private $unavailableBook;

    /** @var \App\Models\DataProvider\Eloquent\AccountGroup */
    private $accountGroup;

    /** @var \App\Models\DataProvider\Eloquent\Account */
    private $debit;

    /** @var \App\Models\DataProvider\Eloquent\Account */
    private $credit;

    /** @var \App\Models\DataProvider\Eloquent\Slip */
    private $slip;

    /** @var \App\Models\DataProvider\Eloquent\SlipEntry */
    private $slipEntry;

    /** @var \App\Models\DataProvider\Eloquent\CreditCardStatement */
    private $creditCardStatement;

    /** @var \App\Models\DataProvider\Eloquent\CreditCardStatement */
    private $unavailableCreditCardStatement;

    /** @var \App\Models\DataProvider\Eloquent\CreditCardStatement */
    private $nonDeletableCreditCardStatement;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        $this->creditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->unavailableCreditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->unavailableBook->book_id,
        ]);
        $this->nonDeletableCreditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => 'asset',
            'is_current' => true,
        ]);
        $this->debit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable' => true,
        ]);
        $this->credit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable' => true,
        ]);
        $this->slip = Slip::factory()->create([
            'book_id' => $this->book->book_id,
            'is_draft' => false,
        ]);
        $this->slipEntry = SlipEntry::factory()->create([
            'slip_id' => $this->slip->slip_id,
            'debit' => $this->debit->account_id,
            'credit' => $this->credit->account_id,
            'credit_card_statement_id' => $this->nonDeletableCreditCardStatement->credit_card_statement_id,
        ]);
    }

    public function test_specified_credit_card_statement_of_specified_book_can_be_deleted(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertNoContent();
        $this->assertSoftDeleted('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $this->creditCardStatement->credit_card_statement_id,
        ]);
    }

    public function test_credit_card_statement_is_not_deleted_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/0/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_deleted_with_invalid_path_parameter_for_credit_card_statement_id(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/0');

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/'.$this->unavailableBook->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertNotFound();
    }

    public function test_specified_credit_card_statement_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->unavailableCreditCardStatement->credit_card_statement_id);

        $response->assertNotFound();
    }

    public function test_credit_card_statement_is_not_deleted_without_permission(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->delete('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertForbidden();
    }

    public function test_credit_card_statement_is_not_deleted_with_bad_condition(): void
    {
        $response = $this->actingAs($this->user)
            ->delete('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->nonDeletableCreditCardStatement->credit_card_statement_id);

        $response->assertUnprocessable();
    }
}

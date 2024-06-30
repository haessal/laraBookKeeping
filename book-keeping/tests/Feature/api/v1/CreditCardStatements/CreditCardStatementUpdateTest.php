<?php

namespace Tests\Feature\api\v1\CreditCardStatements;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\CreditCardStatement;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreditCardStatementUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\Book */
    private $unavailableBook;

    /** @var \App\Models\CreditCardStatement */
    private $creditCardStatement;

    /** @var \App\Models\CreditCardStatement */
    private $unavailableCreditCardStatement;

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
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->unavailableBook->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        $this->creditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->unavailableCreditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->unavailableBook->book_id,
        ]);
    }

    public function test_specified_credit_card_statement_of_specified_book_can_be_updated(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);
        $this->assertDatabaseHas('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $response[0]['id'],
            'book_id' => $this->book->book_id,
            'credit_card_statement_outline' => $newOutline,
            'credit_card_statement_memo' => $newMemo,
            'date' => $newDate,
        ]);
    }

    public function test_credit_card_statement_is_not_updated_with_invalid_path_parameter_for_book_id(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/0/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_updated_with_invalid_path_parameter_for_credit_card_statement_id(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/0', [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_updated_with_invalid_request_body_1(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'unknown' => 'unknown',
            ]);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_updated_with_invalid_request_body_2(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'outline' => null,
                'memo' => null,
                'date' => '2024-06-',
            ]);

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->unavailableBook->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertNotFound();
    }

    public function test_specified_credit_card_statement_is_not_found(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->unavailableCreditCardStatement->credit_card_statement_id, [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertNotFound();
    }

    public function test_credit_card_statement_is_not_updated_without_permission(): void
    {
        $newOutline = $this->faker->sentence();
        $newMemo = $this->faker->sentence();
        $newDate = $this->faker->date();

        $response = $this->actingAs($this->otherUser)
            ->patch('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id, [
                'outline' => $newOutline,
                'memo' => $newMemo,
                'date' => $newDate,
            ]);

        $response->assertForbidden();
    }
}

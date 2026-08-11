<?php

namespace Tests\Feature\api\v1\CreditCardStatements;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreditCardStatementCreationTest extends TestCase
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
    }

    public function test_credit_card_statement_of_specified_book_can_be_created(): void
    {
        $creditCardStatementOutline = $this->faker->sentence();
        $creditCardStatementMemo = $this->faker->sentence();
        $creditCardStatementDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/creditcardstatements', [
                'outline' => $creditCardStatementOutline,
                'memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ]);

        $response->assertCreated()
            ->assertJsonStructure([
                ['id', 'outline', 'memo', 'date'],
            ])
            ->assertJsonFragment([
                'outline' => $creditCardStatementOutline,
                'memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ]);
        $this->assertDatabaseHas('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $response[0]['id'],
            'book_id' => $this->book->book_id,
            'credit_card_statement_outline' => $creditCardStatementOutline,
            'credit_card_statement_memo' => $creditCardStatementMemo,
            'date' => $creditCardStatementDate,
        ]);
    }

    public function test_credit_card_statement_is_not_created_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/0/creditcardstatements', []);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_created_with_invalid_request_body_1(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/creditcardstatements', [
                'outline' => null,
                'memo' => null,
                'date' => null,
            ]);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_created_with_invalid_request_body_2(): void
    {
        $response = $this->actingAs($this->user)
        ->post('/api/v1/books/'.$this->book->book_id.'/creditcardstatements', [
            'outline' => 0,
            'memo' => 0,
            'date' => '2024-07-',
        ]);

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $creditCardStatementOutline = $this->faker->sentence();
        $creditCardStatementMemo = $this->faker->sentence();
        $creditCardStatementDate = $this->faker->date();

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->unavailableBook->book_id.'/creditcardstatements', [
                'outline' => $creditCardStatementOutline,
                'memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ]);

        $response->assertNotFound();
    }

    public function test_credit_card_statement_is_not_created_without_permission(): void
    {
        $creditCardStatementOutline = $this->faker->sentence();
        $creditCardStatementMemo = $this->faker->sentence();
        $creditCardStatementDate = $this->faker->date();

        $response = $this->actingAs($this->otherUser)
            ->post('/api/v1/books/'.$this->book->book_id.'/creditcardstatements', [
                'outline' => $creditCardStatementOutline,
                'memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ]);

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature\api\v1\CreditCardStatements;

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

class CreditCardStatementRetrievalTest extends TestCase
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

    /** @var \App\Models\DataProvider\Eloquent\CreditCardStatement */
    private $creditCardStatement;

    /** @var \App\Models\DataProvider\Eloquent\CreditCardStatement */
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
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        $this->creditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->unavailableCreditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => $this->unavailableBook->book_id,
        ]);
    }

    public function test_specified_credit_card_statement_of_specified_book_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertOk()
            ->assertJsonFragment([
                'credit_card_statement_id' => $this->creditCardStatement->credit_card_statement_id,
            ]);
    }

    public function test_credit_card_statement_is_not_retrieved_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
        ->get('/api/v1/books/0/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertBadRequest();
    }

    public function test_credit_card_statement_is_not_retrieved_with_invalid_path_parameter_for_credit_card_statement_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/0');

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->creditCardStatement->credit_card_statement_id);

        $response->assertNotFound();
    }

    public function test_specified_credit_card_statement_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->book->book_id.'/creditcardstatements/'.$this->unavailableCreditCardStatement->credit_card_statement_id);

        $response->assertNotFound();
    }
}

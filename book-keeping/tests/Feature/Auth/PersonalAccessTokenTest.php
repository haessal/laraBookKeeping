<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PersonalAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_access_token_can_be_issued(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profile/token');

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'token',
            'created_at',
        ]);
    }

    public function test_generation_date_can_be_retrieved(): void
    {
        $user = User::factory()->create();

        // issue a token to ensure there is a token to retrieve the creation date for
        $this->actingAs($user)->post('/profile/token');

        $response = $this->actingAs($user)->get('/profile/token');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'created_at',
        ]);
    }

    public function test_personal_access_token_can_be_revoked(): void
    {
        $user = User::factory()->create();

        // issue a token to ensure there is a token to revoke
        $this->actingAs($user)->post('/profile/token');

        $response = $this->actingAs($user)->delete('/profile/token');

        $response->assertNoContent();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateAccessTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_personal_access_token_screen_can_be_rendered_without_token()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings/tokens');

        $response->assertStatus(200);

        $response->assertSee(__('There is no token available.'));
    }

    public function test_personal_access_token_screen_can_be_rendered_with_token()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->createToken('personal-access-token');

        $response = $this->actingAs($user)->get('/settings/tokens');

        $response->assertStatus(200);

        $response->assertSee(__('The token was generated at '));
    }

    public function test_issueing_personal_access_token_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/settings/tokens');

        $response->assertStatus(200);

        $response->assertSee(__('Make sure to copy your new personal access token now.'));
    }

    public function test_deleting_personal_access_token_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->createToken('personal-access-token');

        $response = $this->actingAs($user)->delete('/settings/tokens');

        $response->assertStatus(200);

        $response->assertSee(__('There is no token available.'));
    }
}

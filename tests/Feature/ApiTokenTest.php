<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'api_token' => Str::random(60)
        ]);
    }

    /**
     * @test
     */
    public function canGetUserByToken()
    {
        $response = $this->json('GET', '/api/user', [
            'api_token' => $this->user->api_token
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]);
    }

    /**
     * @test
     */
    public function cannotGetAnyUserByUnknownToken()
    {
        $response = $this->json('GET', '/api/user', [
            'api_token' => Str::random(60)
        ]);

        $response
            ->assertStatus(401)
            ->assertJsonFragment([
                'message' =>'Unauthenticated.',
            ]);
    }
}

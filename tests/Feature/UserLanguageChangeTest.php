<?php

namespace Tests\Feature;

use App\Models\Tenant\User;
use App\Models\Tenant\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLanguageChangeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test department
        Department::factory()->create();
    }

    /** @test */
    public function it_can_change_user_language_to_arabic()
    {
        $user = User::factory()->create(['lang' => 'en']);
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", [
            'lang' => 'ar'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User language changed successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'lang' => 'ar'
        ]);
    }

    /** @test */
    public function it_can_change_user_language_to_french()
    {
        $user = User::factory()->create(['lang' => 'en']);
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", [
            'lang' => 'fr'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User language changed successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'lang' => 'fr'
        ]);
    }

    /** @test */
    public function it_can_change_user_language_to_spanish()
    {
        $user = User::factory()->create(['lang' => 'en']);
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", [
            'lang' => 'es'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User language changed successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'lang' => 'es'
        ]);
    }

    /** @test */
    public function it_can_change_user_language_to_english()
    {
        $user = User::factory()->create(['lang' => 'ar']);
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", [
            'lang' => 'en'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'User language changed successfully'
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'lang' => 'en'
        ]);
    }

    /** @test */
    public function it_validates_language_field_is_required()
    {
        $user = User::factory()->create();
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['lang']);
    }

    /** @test */
    public function it_validates_language_field_is_valid()
    {
        $user = User::factory()->create();
        
        $response = $this->postJson("/api/users/{$user->id}/change-language", [
            'lang' => 'invalid'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['lang']);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user()
    {
        $response = $this->postJson("/api/users/999/change-language", [
            'lang' => 'ar'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_accepts_only_valid_language_codes()
    {
        $user = User::factory()->create();
        
        $validLanguages = ['ar', 'en', 'fr', 'es'];
        
        foreach ($validLanguages as $lang) {
            $response = $this->postJson("/api/users/{$user->id}/change-language", [
                'lang' => $lang
            ]);
            
            $response->assertStatus(200);
        }
    }
}

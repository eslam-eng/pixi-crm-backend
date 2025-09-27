<?php

namespace Tests\Unit;

use App\Models\Tenant\User;
use App\Models\Tenant\Department;
use App\Services\Tenant\Users\UserService;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceLanguageChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test department
        Department::factory()->create();
    }

    /** @test */
    public function it_can_change_user_language()
    {
        $user = User::factory()->create(['lang' => 'en']);
        $userService = new UserService(new User());

        $result = $userService->changeLanguage($user->id, 'ar');

        $this->assertTrue($result);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'lang' => 'ar'
        ]);
    }

    /** @test */
    public function it_throws_exception_for_nonexistent_user()
    {
        $userService = new UserService(new User());

        $this->expectException(NotFoundException::class);

        $userService->changeLanguage(999, 'ar');
    }

    /** @test */
    public function it_can_change_language_multiple_times()
    {
        $user = User::factory()->create(['lang' => 'en']);
        $userService = new UserService(new User());

        // Change to Arabic
        $userService->changeLanguage($user->id, 'ar');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'lang' => 'ar']);

        // Change to French
        $userService->changeLanguage($user->id, 'fr');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'lang' => 'fr']);

        // Change to Spanish
        $userService->changeLanguage($user->id, 'es');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'lang' => 'es']);

        // Change back to English
        $userService->changeLanguage($user->id, 'en');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'lang' => 'en']);
    }
}

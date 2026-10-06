<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'Password1!Strong',
            'password_confirmation' => 'Password1!Strong',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(UserRole::User, $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_password_is_hashed_not_stored_in_plaintext(): void
    {
        $this->post('/register', [
            'name'                  => 'Hashed User',
            'email'                 => 'hash@example.com',
            'password'              => 'Password1!Strong',
            'password_confirmation' => 'Password1!Strong',
        ]);

        $user = User::where('email', 'hash@example.com')->first();

        $this->assertNotSame('Password1!Strong', $user->password);
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    public function test_weak_password_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Weak User',
            'email'                 => 'weak@example.com',
            'password'              => 'abc',
            'password_confirmation' => 'abc',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'Dup User',
            'email'                 => 'existing@example.com',
            'password'              => 'Password1!Strong',
            'password_confirmation' => 'Password1!Strong',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Mismatch User',
            'email'                 => 'mismatch@example.com',
            'password'              => 'Password1!Strong',
            'password_confirmation' => 'Different1!Strong',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
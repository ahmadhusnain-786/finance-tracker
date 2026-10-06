    public function test_deactivated_users_are_blocked(): void
    {
        $user = User::factory()->inactive()->create([
            'email'    => 'inactive@example.com',
            'password' => Hash::make('Password1!Strong'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'inactive@example.com',
            'password' => 'Password1!Strong',
        ]);

        // Next authenticated page should kick them out
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
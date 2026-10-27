<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowBoxAppTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the homepage loads successfully.
     */
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('BorrowBox.');
    }

    /**
     * Test that a user can register successfully.
     */
    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sadia Test',
            'email' => 'sadia@university.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'sadia@university.edu',
        ]);
    }

    /**
     * Test that registration fails with mismatched passwords.
     */
    public function test_registration_validation_fails(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sadia Test',
            'email' => 'sadia@university.edu',
            'password' => 'Password123!',
            'password_confirmation' => 'WrongPassword!',
            'terms' => 'on',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test that an existing user can log in.
     */
    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'student@university.edu',
            'password' => 'secretPassword123',
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'student@university.edu',
            'password' => 'secretPassword123',
        ]);

        $loginResponse->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // Test logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test creating a shared item.
     */
    public function test_user_can_create_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/items', [
            'item_name' => 'Casio FX-991EX Calculator',
            'description' => 'Scientific calculator in good working condition.',
            'category' => 'Calculators',
            'condition' => 'Good',
            'owner_name' => $user->name,
        ]);

        $response->assertRedirect(route('items.create'));
        $this->assertDatabaseHas('items', [
            'item_name' => 'Casio FX-991EX Calculator',
            'user_id' => $user->id,
            'reward_coins' => 100,
            'is_available' => true,
        ]);
    }

    /**
     * Test category and search filtering on homepage.
     */
    public function test_search_and_category_filter(): void
    {
        Item::create([
            'item_name' => 'Data Structures Book',
            'description' => 'Great textbook for algorithms class.',
            'category' => 'Books',
            'condition' => 'Good',
            'owner_name' => 'Afrin',
            'reward_coins' => 100,
            'is_available' => true,
        ]);

        Item::create([
            'item_name' => 'USB-C Adapter',
            'description' => 'HDMI and USB adapter for laptops.',
            'category' => 'Electronics',
            'condition' => 'New',
            'owner_name' => 'Maliha',
            'reward_coins' => 100,
            'is_available' => true,
        ]);

        // Filter by category
        $responseCat = $this->get('/?category=Books');
        $responseCat->assertStatus(200);
        $responseCat->assertSee('Data Structures Book');
        $responseCat->assertDontSee('USB-C Adapter');

        // Filter by search keyword
        $responseSearch = $this->get('/?search=Adapter');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('USB-C Adapter');
        $responseSearch->assertDontSee('Data Structures Book');
    }
}

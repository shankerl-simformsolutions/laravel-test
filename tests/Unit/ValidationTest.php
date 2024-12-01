<?php

namespace Tests\Unit;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Rules\UniqueUsername;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function password_must_contain_special_characters()
    {
        // Arrange
        $rule = new StrongPassword;

        // Assert - Valid passwords
        $this->assertTrue($rule->passes('password', 'Test@123'));
        $this->assertTrue($rule->passes('password', 'Complex#Pass123'));

        // Assert - Invalid passwords
        $this->assertFalse($rule->passes('password', 'simplepass'));
        $this->assertFalse($rule->passes('password', '12345678'));
    }

    /** @test */
    public function username_must_be_unique_case_insensitive()
    {
        // Arrange
        $rule = new UniqueUsername;
        
        // Create a user with username
        User::factory()->create(['name' => 'TestUser']);

        // Assert
        $this->assertFalse($rule->passes('name', 'testuser')); // Same username different case
        $this->assertFalse($rule->passes('name', 'TestUser')); // Exact same username
        $this->assertTrue($rule->passes('name', 'DifferentUser')); // Different username
    }

    /** @test */
    public function user_registration_validation_rules()
    {
        // Arrange
        $validator = Validator::make([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'short',
            'terms' => false
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'terms' => 'accepted'
        ]);

        // Assert
        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('terms', $errors);
    }
}
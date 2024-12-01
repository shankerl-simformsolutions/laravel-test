<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function database_transaction_commits_successfully()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        DB::transaction(function () use ($user) {
            $post = Post::create([
                'user_id' => $user->id,
                'title' => 'Test Post',
                'content' => 'Test Content'
            ]);

            Comment::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'content' => 'Test Comment'
            ]);
        });

        // Assert
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
        $this->assertDatabaseHas('comments', ['content' => 'Test Comment']);
    }

    /** @test */
    public function database_transaction_rolls_back_on_error()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        try {
            DB::transaction(function () use ($user) {
                Post::create([
                    'user_id' => $user->id,
                    'title' => 'Test Post',
                    'content' => 'Test Content'
                ]);

                // This will fail due to missing required fields
                Comment::create([]);
            });
        } catch (\Exception $e) {
            // Exception expected
        }

        // Assert
        $this->assertDatabaseMissing('posts', ['title' => 'Test Post']);
        $this->assertEquals(0, Post::count());
    }

    /** @test */
    public function database_query_builder_works_correctly()
    {
        // Arrange
        User::factory()->count(5)->create();
        $specificEmail = 'test@example.com';
        User::factory()->create(['email' => $specificEmail]);

        // Act & Assert
        $this->assertEquals(6, DB::table('users')->count());
        
        $user = DB::table('users')
            ->where('email', $specificEmail)
            ->first();
            
        $this->assertEquals($specificEmail, $user->email);
    }

    /** @test */
    public function eloquent_relationships_work_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->count(3)->create(['post_id' => $post->id]);
        $this->assertDatabaseCount('comments', 3);


        // Act & Assert
        $this->assertEquals(1, $user->posts()->count());
        $this->assertEquals(3, $post->comments()->count());
        $this->assertEquals($user->id, $post->user->id);
        $this->assertEquals(3, $comment->count());
        
    }
}
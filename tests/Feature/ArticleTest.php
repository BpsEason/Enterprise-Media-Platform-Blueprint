<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_get_all_articles()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/articles');
        $response->assertStatus(200);
    }
    
    /** @test */
    public function a_user_can_create_an_article()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $data = [
            'title' => 'Test Article',
            'content' => 'This is the content of the test article.'
        ];

        $response = $this->postJson('/api/articles', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
    }
    
    /** @test */
    public function a_user_can_get_a_single_article()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        
        $article = \App\Models\Article::factory()->create();
        $response = $this->getJson('/api/articles/' . $article->id);
        $response->assertStatus(200)
                 ->assertJsonPath('data.title', $article->title);
    }
    
    /** @test */
    public function a_user_can_search_articles()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        \App\Models\Article::factory()->create(['title' => 'Laravel Framework']);
        \App\Models\Article::factory()->create(['title' => 'PHP in Depth']);

        $response = $this->postJson('/api/articles/search', ['query' => 'Laravel']);
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function ask_ai_endpoint_returns_response_with_sanctum_auth()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $prompt = 'What is the capital of France?';
        Http::fake([
            env('AI_SERVICE_URL') . '/query' => Http::response(['response' => 'Paris'], 200),
        ]);

        $response = $this->postJson('/api/articles/ask-ai', ['prompt' => $prompt]);

        $response->assertStatus(200)
                 ->assertJson(['response' => 'Paris']);
    }
}

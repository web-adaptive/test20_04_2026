<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [
            'name' => 'Test',
            'description' => 'Desc',
            'price' => 99.5,
            'category_id' => $category->id,
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('products', 0);
    }

    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'Новый товар',
            'description' => 'Описание',
            'price' => 150.25,
            'category_id' => $category->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Новый товар')
            ->assertJsonPath('data.category_id', $category->id);

        $this->assertDatabaseHas('products', [
            'name' => 'Новый товар',
            'category_id' => $category->id,
        ]);
    }

    public function test_validation_rejects_invalid_price(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'X',
            'price' => 0,
            'category_id' => $category->id,
        ]);

        $response->assertUnprocessable();
    }

    public function test_public_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }
}

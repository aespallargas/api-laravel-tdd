<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductTest extends TestCase
{
    
    /**
     * @test
     */
    public function givenEmptyProductControler_whenProductCreated_thenProductFoundOnDatabase()
    {
        $name = $this->faker->name;

        $data = [
            "name" => $name,
            "description" => $this->faker ->text(500),
            "stock" => $this->faker ->numberBetween(10, 100),
            "available" => true,
        ];
        
        $response = $this->post(route(name: "products.store"), $data);

        $response->assertStatus(201);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas("products", [
            "name" => $name,
        ]);
    }

    /** @test */
    public function givenEmptyProductController_whenProductCreatedWithoutName_thenValidationNameFails() {
        $data = [
            "description" => $this->faker->text(500),
            "stock" => $this->faker->numberBetween(10, 100),
            "available" => true,
        ];
        $response = $this->post(route("products.store"), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors("name");
    }

    /** @test */
    public function givenProductControllerWithMultipleProducts_whenFetchAll_thenProductsCanBeListed() {
        $products = Product::factory(10)->create();

        $response = $this->get(route("products.index"));
        $response->assertStatus(200);
        $response->assertExactJson($products->toArray());
        $response->assertJsonCount($products->count());
        $response->assertJsonFragment([
            "name" => $products->first()->name,
        ]);
        $response->assertJsonFragment([
            "stock" => $products->last()->stock,
        ]);
    }

    /** @test */
    public function givenProductControllerWithOneProduct_whenFindById_thenProductCanBeFetched() {
        $product = Product::factory()->create();

        $response = $this->get(route("products.show", $product->id));
        $response->assertStatus(200);
        $response->assertExactJson($product->toArray());
        $response->assertJsonStructure(["name", "description", "stock", "available"]);

        $this->assertDatabaseHas("products", [
            "id" => $product->id,
        ]);
    }

    /** @test */
    public function givenEmptyProductController_whenProductNotFound_thenStatusCodeIs404() {
        $response = $this->get(route("products.show", 1));
        $response->assertStatus(404);
    }

    /** @test */
    public function givenEmptyProductController_whenProductNotFoundWithoutHandlingException_thenThrowsModelNotFoundException() {
        $this->expectException(ModelNotFoundException::class);

        $this->withoutExceptionHandling();

        $this->get(route("products.show", 1));
    }

    /** @test */
    public function givenProductControllerWithMultipleProducts_whenFindByName_thenProductFoundOnDatabase() {
        Product::factory()->create();
        $product = Product::factory()->create();
        Product::factory()->create();

        $response = $this->get(route("products.find_by_name", $product->name));
        $response->assertStatus(200);
        $response->assertExactJson($product->toArray());

        $this->assertDatabaseHas("products", [
            "name" => $product->name,
        ]);
    }

    /** @test */
    public function givenProductControllerWithOneProduct_whenProductUpdated_thenProductFoundOnDatabase() {
        $product = Product::factory()->create();

        $product->name = $product->name . " updated";

        $response = $this->put(route("products.update", $product->id), $product->toArray());
        $response->assertStatus(200);
        $response->assertExactJson($product->toArray());

        $this->assertDatabaseHas("products", [
            "name" => $product->name,
        ]);
    }

    /** @test */
    public function givenProductControllerWithOneProduct_whenProductDeleted_thenProductNotFoundOnDatabase() {
        $product = Product::factory()->create();

        $this->assertDatabaseCount("products", 1);

        $response = $this->delete(route("products.destroy", $product->id));
        $response->assertStatus(201);

        $this->assertDatabaseCount("products", 0);
        $this->assertDatabaseMissing("products", [
            "id" => $product->id,
        ]);
    }

}

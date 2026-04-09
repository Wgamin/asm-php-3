<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows a dedicated cart page with item details and totals', function () {
    $category = Category::create([
        'name' => 'Rau xanh',
        'slug' => 'rau-xanh',
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Rau cải ngọt',
        'product_type' => 'simple',
        'price' => 35000,
        'stock' => 10,
        'description' => 'Mô tả',
        'content' => 'Nội dung',
        'image' => 'products/test.jpg',
    ]);

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                'p-' . $product->id => [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
        ])
        ->get(route('cart.index'));

    $response->assertOk();
    $response->assertSee('Giỏ hàng của bạn');
    $response->assertSee($product->name);
    $response->assertSee('70,000đ', false);
    $response->assertSee('Tiến hành thanh toán');
});

<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createReviewCategory(string $name, string $slug): Category
{
    return Category::create([
        'name' => $name,
        'slug' => $slug,
    ]);
}

function createReviewProduct(Category $category, string $name, float $price): Product
{
    return Product::create([
        'category_id' => $category->id,
        'name' => $name,
        'product_type' => 'simple',
        'price' => $price,
        'sale_price' => null,
        'cost_price' => $price * 0.6,
        'stock' => 20,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

it('allows a completed customer to submit a review and shows it on the product page', function () {
    $user = User::factory()->create();
    $category = createReviewCategory('Rau cu', 'rau-cu-review');
    $product = createReviewProduct($category, 'Ca rot huu co', 100000);

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-REVIEW-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => $user->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => 100000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 20000,
        'total_amount' => 100000,
        'payable_amount' => 120000,
        'status' => 'completed',
        'payment_method' => 'cod',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100000,
        'cost_price' => 60000,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'title' => 'Rat hai long',
            'content' => 'Dong goi gon gang, chat luong tuoi va giao hang dung hen.',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('product_reviews', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
        'title' => 'Rat hai long',
    ]);

    $detail = $this->get(route('product.detail', $product->id));
    $detail->assertOk();
    $detail->assertSee('Dong goi gon gang, chat luong tuoi va giao hang dung hen.');
});

it('shows related products from the same category and similar price range', function () {
    $mainCategory = createReviewCategory('Trai cay', 'trai-cay-main');
    $otherCategory = createReviewCategory('Hat', 'hat-main');

    $product = createReviewProduct($mainCategory, 'Bo sap', 100000);
    $sameCategory = createReviewProduct($mainCategory, 'Xoai cat', 220000);
    $similarPrice = createReviewProduct($otherCategory, 'Hat dieu tuoi', 110000);
    $farPrice = createReviewProduct($otherCategory, 'Dong trung ha thao', 500000);

    $response = $this->get(route('product.detail', $product->id));

    $response->assertOk();
    $response->assertSee('Xoai cat');
    $response->assertSee('Hat dieu tuoi');
    $response->assertDontSee('Dong trung ha thao');
});

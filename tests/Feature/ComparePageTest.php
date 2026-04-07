<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCompareCategory(): Category
{
    return Category::firstOrCreate([
        'slug' => 'so-sanh-test',
    ], [
        'name' => 'So sanh test',
    ]);
}

function createCompareProduct(string $name, array $attributes = []): Product
{
    $category = createCompareCategory();

    return Product::create(array_merge([
        'category_id' => $category->id,
        'name' => $name,
        'product_type' => 'simple',
        'price' => 100000,
        'stock' => 10,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ], $attributes));
}

it('shows compared products from session in the same order', function () {
    $first = createCompareProduct('Ca rot huu co');
    $second = createCompareProduct('Xoai cat Hoa Loc');

    $response = $this
        ->withSession([
            'compare' => [$second->id, $first->id],
        ])
        ->get(route('compare.index'));

    $response->assertOk();
    $response->assertSeeInOrder([$second->name, $first->name]);
});

it('adds a product to compare session', function () {
    $product = createCompareProduct('Bo sap');

    $response = $this
        ->from(route('products.index'))
        ->post(route('compare.add', $product));

    $response->assertRedirect(route('products.index'));
    $response->assertSessionHas('compare', [$product->id]);
});

it('removes a product from compare session', function () {
    $first = createCompareProduct('Thanh long');
    $second = createCompareProduct('Cam sanh');

    $response = $this
        ->from(route('compare.index'))
        ->withSession([
            'compare' => [$first->id, $second->id],
        ])
        ->delete(route('compare.remove', $first));

    $response->assertRedirect(route('compare.index'));
    $response->assertSessionHas('compare', [$second->id]);
});

it('clears the compare session', function () {
    $first = createCompareProduct('Dua hau');
    $second = createCompareProduct('Dua luoi');

    $response = $this
        ->from(route('compare.index'))
        ->withSession([
            'compare' => [$first->id, $second->id],
        ])
        ->delete(route('compare.clear'));

    $response->assertRedirect(route('compare.index'));
    $response->assertSessionMissing('compare');
});

it('prevents adding more than four products to compare', function () {
    $products = collect([
        createCompareProduct('San pham 1'),
        createCompareProduct('San pham 2'),
        createCompareProduct('San pham 3'),
        createCompareProduct('San pham 4'),
        createCompareProduct('San pham 5'),
    ]);

    $response = $this
        ->from(route('products.index'))
        ->withSession([
            'compare' => $products->take(4)->pluck('id')->all(),
        ])
        ->post(route('compare.add', $products->last()));

    $response->assertRedirect(route('products.index'));
    $response->assertSessionHas('error');
    $response->assertSessionHas('compare', $products->take(4)->pluck('id')->all());
});

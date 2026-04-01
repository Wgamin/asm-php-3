<?php

use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createNewsArticle(array $attributes = []): NewsArticle
{
    $author = User::factory()->create();

    return NewsArticle::create(array_merge([
        'user_id' => $author->id,
        'title' => 'Bai viet tin tuc mau',
        'slug' => 'bai-viet-tin-tuc-mau-' . fake()->unique()->numerify('###'),
        'excerpt' => 'Mo ta ngan cho bai viet.',
        'content' => 'Noi dung chi tiet cua bai viet.',
        'meta_title' => 'Meta title bai viet',
        'meta_description' => 'Meta description bai viet',
        'is_published' => true,
        'published_at' => now()->subDay(),
    ], $attributes));
}

it('shows only published news articles on the news index page', function () {
    $publishedArticle = createNewsArticle([
        'title' => 'Tin da xuat ban',
        'slug' => 'tin-da-xuat-ban',
    ]);

    createNewsArticle([
        'title' => 'Tin ban nhap',
        'slug' => 'tin-ban-nhap',
        'is_published' => false,
        'published_at' => null,
    ]);

    createNewsArticle([
        'title' => 'Tin hen gio',
        'slug' => 'tin-hen-gio',
        'published_at' => now()->addDay(),
    ]);

    $response = $this->get(route('news.index'));

    $response->assertOk();
    $response->assertSee($publishedArticle->title);
    $response->assertDontSee('Tin ban nhap');
    $response->assertDontSee('Tin hen gio');
});

it('hides the empty state when there is only one featured article', function () {
    $article = createNewsArticle([
        'title' => 'Chi co mot bai viet',
        'slug' => 'chi-co-mot-bai-viet',
    ]);

    $response = $this->get(route('news.index'));

    $response->assertOk();
    $response->assertSee($article->title);
    $response->assertDontSee('Chưa có bài viết nào được xuất bản.');
    $response->assertSee('Hiển thị 1 bài viết công khai');
});

it('renders published news detail with seo meta and schema', function () {
    $article = createNewsArticle([
        'title' => 'Nong san sach vao mua',
        'slug' => 'nong-san-sach-vao-mua',
        'meta_title' => 'Nong san sach vao mua | SEO',
        'meta_description' => 'Cap nhat xu huong nong san sach vao mua.',
    ]);

    $response = $this->get(route('news.show', $article->slug));

    $response->assertOk();
    $response->assertSee($article->title);
    $response->assertSee('Cap nhat xu huong nong san sach vao mua.');
    $response->assertSee(route('news.show', $article->slug));
    $response->assertSee('NewsArticle', false);
});

it('returns 404 for unpublished news detail pages', function () {
    $draftArticle = createNewsArticle([
        'title' => 'Ban nhap bi an',
        'slug' => 'ban-nhap-bi-an',
        'is_published' => false,
        'published_at' => null,
    ]);

    $this->get(route('news.show', $draftArticle->slug))->assertNotFound();
});

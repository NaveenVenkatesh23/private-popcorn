<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/private-theatre-in-banglore', function () {
    return view('about-us');
});

Route::get('terms-and-conditions', function () {
    return view('terms-and-conditions');
});

Route::get('privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('cancellation-and-refund', function () {
    return view('cancellation-and-refund');
});

Route::get('shipping', function () {
    return view('shipping');
});


Route::get('/booking', [BookingController::class, 'index']);
Route::post('/create-order', [BookingController::class, 'createOrder']);
Route::post('/save-booking', [BookingController::class, 'store']);
Route::get('/booking/success', [BookingController::class, 'success']);
Route::get('/booking/failed', [BookingController::class, 'failed']);
Route::get('/get-available-slots', [BookingController::class, 'getAvailableSlots']);


Route::get('/booking/receipt/{id}', [App\Http\Controllers\BookingController::class, 'downloadReceipt']);

use App\Http\Controllers\BlogController;

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/booking/receipt/{id}/download', [App\Http\Controllers\BookingController::class, 'downloadReceiptPdf']);


Route::get('/sitemap.xml', function () {
    $blogs = App\Models\Blog::all();
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Static pages
    $staticPages = [
        ['loc' => 'https://privatepopcorn.com/', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => 'https://privatepopcorn.com/private-theatre-in-banglore', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => 'https://privatepopcorn.com/booking', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => 'https://privatepopcorn.com/blogs', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['loc' => 'https://privatepopcorn.com/privacy-policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'https://privatepopcorn.com/terms-and-conditions', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'https://privatepopcorn.com/cancellation-and-refund', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'https://privatepopcorn.com/shipping', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ];

    foreach ($staticPages as $page) {
        $xml .= '<url>';
        $xml .= '<loc>' . $page['loc'] . '</loc>';
        $xml .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
        $xml .= '<priority>' . $page['priority'] . '</priority>';
        $xml .= '</url>';
    }

    // Dynamic blog pages
    foreach ($blogs as $blog) {
        $xml .= '<url>';
        $xml .= '<loc>https://privatepopcorn.com/blogs/' . $blog->slug . '</loc>';
        $xml .= '<lastmod>' . $blog->updated_at->toAtomString() . '</lastmod>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.6</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
});


Route::post('/apply-coupon', [BookingController::class, 'applyCoupon']);

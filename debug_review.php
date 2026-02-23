<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::first();
    $product = \App\Models\Product::first();

    if (!$user) {
        echo "No users found.\n";
        exit;
    }
    if (!$product) {
        echo "No products found.\n";
        exit;
    }

    echo "User ID: " . $user->id . "\n";
    echo "Product ID: " . $product->id . "\n";

    $review = \App\Models\Review::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Debug comment'
    ]);

    echo "Review created successfully with ID: " . $review->id . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

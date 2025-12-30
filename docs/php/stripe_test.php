<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;

if (!$stripeSecretKey) {
    die("Error: STRIPE_SECRET_KEY not found in .env\n");
}

$client = new Client([
    'base_uri' => 'https://api.stripe.com/v1/',
    'headers' => [
        'Authorization' => 'Bearer ' . $stripeSecretKey,
        'Content-Type' => 'application/x-www-form-urlencoded',
    ],
]);

function printStep($step) {
    echo "\n=== $step ===\n";
}

try {
    // 1. Create a Product
    printStep("Creating Product");
    $response = $client->post('products', [
        'form_params' => [
            'name' => 'Otaku Integration Test Product (PHP)',
        ]
    ]);
    $product = json_decode($response->getBody(), true);
    $productId = $product['id'];
    echo "Product created: $productId ({$product['name']})\n";

    // 2. Create a Price
    printStep("Creating Price");
    $response = $client->post('prices', [
        'form_params' => [
            'unit_amount' => 100000, // 1000.00 BRL
            'currency' => 'brl',
            'product' => $productId,
            'metadata' => [
                'order' => json_encode(['id' => 'ord_123456'])
            ]
        ]
    ]);
    $price = json_decode($response->getBody(), true);
    $priceId = $price['id'];
    echo "Price created: $priceId (Amount: {$price['unit_amount']} {$price['currency']})\n";

    // 3. Update Price (Metadata)
    printStep("Updating Price Metadata");
    $response = $client->post("prices/$priceId", [
        'form_params' => [
            'metadata' => [
                'order' => json_encode(['id' => 'ord_123456']),
                'customer' => json_encode([
                    'id' => 'cus_123456',
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com'
                ])
            ]
        ]
    ]);
    $updatedPrice = json_decode($response->getBody(), true);
    echo "Price updated. Metadata keys: " . implode(', ', array_keys($updatedPrice['metadata'])) . "\n";

    // 4. Create Payment Link
    printStep("Creating Payment Link");
    $response = $client->post('payment_links', [
        'form_params' => [
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1
                ]
            ]
        ]
    ]);
    $paymentLink = json_decode($response->getBody(), true);
    echo "Payment Link created: {$paymentLink['url']}\n";

    // 5. List Prices (Limit 3)
    printStep("Listing Prices (Recent 3)");
    $response = $client->get('prices', [
        'query' => ['limit' => 3]
    ]);
    $prices = json_decode($response->getBody(), true);
    foreach ($prices['data'] as $p) {
        echo "- {$p['id']} (Produit: {$p['product']})\n";
    }

    // 6. List Payment Links (Limit 3)
    printStep("Listing Payment Links (Recent 3)");
    $response = $client->get('payment_links', [
        'query' => ['limit' => 3]
    ]);
    $links = json_decode($response->getBody(), true);
    foreach ($links['data'] as $l) {
        echo "- {$l['id']} -> {$l['url']}\n";
    }

} catch (RequestException $e) {
    echo "Request Error:\n";
    if ($e->hasResponse()) {
        echo $e->getResponse()->getBody();
    } else {
        echo $e->getMessage();
    }
}

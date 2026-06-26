<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Database ===\n";

// Get all terms directly
$terms = \App\Models\Term::all();
echo "Total terms: " . count($terms) . "\n";
foreach ($terms as $t) {
    echo "  - ID: $t->id, Name: " . (empty($t->name) ? "[KOSONG]" : $t->name) . "\n";
}

// Get all users with admin role
$admins = \App\Models\User::role('admin')->get();
echo "\nUsers with 'admin' role: " . count($admins) . "\n";
foreach ($admins as $u) {
    echo "  - ID: $u->id, Name: $u->name\n";
}

// Get the first user (typically the authenticated admin)
$firstUser = \App\Models\User::first();
if ($firstUser) {
    echo "\nFirst user in DB:\n";
    echo "  Name: $firstUser->name\n";
    echo "  Roles: " . implode(', ', $firstUser->getRoleNames()->toArray() ?: ['NONE']) . "\n";
    echo "  Has 'admin' role: " . ($firstUser->hasRole('admin') ? 'YES' : 'NO') . "\n";
    
    // Check if first user has terms
    $userTerms = $firstUser->terms()->count();
    echo "  Linked terms: $userTerms\n";
}

?>

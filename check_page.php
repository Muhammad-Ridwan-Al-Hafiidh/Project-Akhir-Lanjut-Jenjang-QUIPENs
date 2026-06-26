<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check terms
$terms = \App\Models\Term::all();
echo "Total terms: " . count($terms) . "\n";
foreach ($terms as $t) {
    echo "  - ID: $t->id, Name: $t->name\n";
}

// Check current user and role
$user = auth()->user();
if ($user) {
    echo "\n✓ User logged in: $user->name (ID: $user->id)\n";
    echo "  Roles: " . (count($user->getRoleNames()) > 0 ? implode(', ', $user->getRoleNames()->toArray()) : "NO ROLES") . "\n";
    echo "  Is Admin: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";
} else {
    echo "\n✗ No user authenticated!\n";
}

?>

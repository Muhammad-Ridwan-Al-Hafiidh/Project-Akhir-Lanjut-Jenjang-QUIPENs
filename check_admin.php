<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Admin Users ===\n";

// Get all users and their roles
$users = \App\Models\User::all();
echo "Total users in DB: " . count($users) . "\n\n";

foreach ($users as $u) {
    $roles = $u->getRoleNames()->toArray();
    echo "User ID {$u->id}: {$u->name} ({$u->email})\n";
    echo "  Roles: " . (count($roles) > 0 ? implode(', ', $roles) : "NONE") . "\n";
    echo "  Is Admin: " . ($u->hasRole('admin') ? 'YES' : 'NO') . "\n";
}

echo "\n=== Term Info ===\n";
$terms = \App\Models\Term::all();
foreach ($terms as $t) {
    echo "Term ID {$t->id}: '" . ($t->name ?: "[EMPTY]") . "'\n";
}

?>

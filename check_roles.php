<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Http\Kernel")->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "All roles in system:\n";
$roles = \Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "  - {$role->name}\n";
    $userCount = \App\Models\User::role($role->name)->count();
    echo "    Users with this role: $userCount\n";
}


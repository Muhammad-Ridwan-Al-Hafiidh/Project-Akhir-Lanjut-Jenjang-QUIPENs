<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

$participants = \App\Models\Participant::count();
$terms = \App\Models\Term::count();
$users = \App\Models\User::count();

echo "Total Participants: $participants\n";
echo "Total Terms: $terms\n";
echo "Total Users: $users\n";

if ($terms > 0) {
    $first_term = \App\Models\Term::first();
    $term_id = $first_term->id;
    $part_in_term = \App\Models\Participant::where('term_id', $term_id)->count();
    echo "\nFirst term ID: $term_id, Name: {$first_term->name}, Participants: $part_in_term\n";
}

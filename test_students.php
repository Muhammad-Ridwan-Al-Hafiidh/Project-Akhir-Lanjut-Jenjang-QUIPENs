<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Http\Kernel")->handle(
    $request = Illuminate\Http\Request::capture()
);

$termId = 1;
$participants = \App\Models\Participant::where("term_id", $termId)->with("User")->get();
echo "Participants in term 1: " . $participants->count() . "\n";
foreach ($participants as $p) {
    echo "  - ID: {$p->id}, User: {$p->User->name}\n";
}


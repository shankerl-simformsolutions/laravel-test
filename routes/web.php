<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ProcessPodcast;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    $podcast = "bhawani";
    ProcessPodcast::dispatch($podcast);
});
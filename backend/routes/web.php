<?php

use Illuminate\Support\Facades\Route;

Route::get('/files/{folder}/{filename}', function (string $folder, string $filename) {
    $path = storage_path("app/public/{$folder}/{$filename}");
    abort_unless(file_exists($path), 404);
    return response()->file($path);
});

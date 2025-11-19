<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\PhotoController;

Route::get('/', [AlbumController::class, 'index'])->name('liste-albums');
Route::get('/album/{id}', [AlbumController::class, 'show'])->name('voir-album');
Route::get('/album/{album_id}/ajouter-photo', [PhotoController::class, 'create'])->name('ajouter-photo');
Route::post('/photo/enregistrer', [PhotoController::class, 'store'])->name('enregistrer-photo');
Route::delete('/photo/{id}/supprimer', [PhotoController::class, 'destroy'])->name('supprimer-photo');

<?php

use App\Http\Controllers\GoogleDriveController;
use App\Models\Images;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/google/login', [GoogleDriveController::class, 'googleLogin'])->name('google.login');
Route::post('/google-drive/file-upload', [GoogleDriveController::class, 'googleDriveFilePpload'])->name('google.drive.file.upload');
Route::get('/', [GoogleDriveController::class, 'index']);
Route::get('/delete/{id}', [GoogleDriveController::class, 'delete']);

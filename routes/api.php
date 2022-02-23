<?php

use App\Jobs\TestFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function (Request $request) {
    $filename = '/tmp/_app_test';

    $db = false;

    try {
        $db = User::create(['name' =>'test', 'email' => 'user@example.org']) != null;
    } catch (Exception $ex) {
        report($ex);
    }

    unlink($filename);

    dispatch(new TestFile($filename));

    sleep(3);

    return response()->json([
        'version' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
        'database' => $db,
        'queue' => file_exists($filename) && env('QUEUE_CONNECTION') != 'sync'
    ]);
});

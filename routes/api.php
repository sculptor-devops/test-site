<?php

use App\Jobs\TestFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    $tag = "user " . time();

    $version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

    $created = false;

    $filename = "/tmp/_app_test_$version";

    $db = false;

    try {
        $db = User::create(['name' =>"test $tag", 'email' => "$tag@example.org", 'password' => Hash::make('some-password')]) != null;
    } catch (Exception $ex) {
        report($ex);
    }

    if (file_exists($filename)) {
        $created = true;

        unlink($filename);
    }

    dispatch(new TestFile($filename));

    sleep(3);

    return response()->json([
        'version' => $version,
        'database' => $db,
        'queue' => $created && env('QUEUE_CONNECTION') != 'sync'
    ]);
});

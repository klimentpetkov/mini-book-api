<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\V1\BookController;
use App\Models\User;
use Illuminate\Support\Facades\Log;

Route::prefix('v1')->group(function(){
    Route::post('/login', function (Request $req) {
        $data = $req->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        // normalize: trim + lowercase (без Str alias)
        $email = strtolower(trim((string) $data['email']));

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        // For Debug purposes only
        // $userCount = \App\Models\User::count();
        /* Log::info('LOGIN_STATE', [
            'email_in'   => $data['email'],
            'email_norm' => $email,
            'user_count' => $userCount,
            'found'      => (bool) $user,
            'hash_ok'    => $user ? Hash::check($data['password'], $user->password) : null,
            'first_user'   => User::first()?->only(['id','email']),
        ]); */

        abort_if(!$user || !Hash::check($data['password'], $user->password), 401, 'Invalid credentials');

        return response()->json([
            'token' => $user->createToken('api')->accessToken,
        ]);
    });

    Route::middleware(['auth:api'])->get('/me', fn (Request $req) => $req->user());

    Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
        Route::get('books/{book}/report', [BookController::class, 'report']);
        Route::apiResource('books', BookController::class);
    });

    Route::fallback(fn () => response()->json(['message' => 'Route not found',], 404));
});

// V2 (Future)
/*
Route::prefix('v2')->group(function(){
    //
}); */

Route::get('health', fn() => response()->json(['status'=>'ok','time'=>now()->toISOString()]));

// TODO: REMOVE THIS AFTER TESTS!!!
Route::get('debug/headers', fn (Request $r) =>
    response()->json(['auth' => $r->header('Authorization')]));

<?php

namespace App\Modules\Wisatawan\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Wisatawan\Http\Requests\Api\LoginRequest;
use App\Support\Auth\LoginLockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request, LoginLockout $lockout): JsonResponse 
    {
        //
        $email = $request->string('email')->toString();
        $pass = $request->string('password')->toString();

        if ($lockout->isLocked($email)) {
            return response()->json(['message' => 'Terkunci'], 429);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($pass, $user->password)) {
            $lockout->recordFailure($email);
            return response()->json(['message' => 'Gagal login'], 401);
        }

        $lockout->clear($email);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);

    }
}

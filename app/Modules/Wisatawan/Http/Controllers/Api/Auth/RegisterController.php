<?php

namespace App\Modules\Wisatawan\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Wisatawan\Actions\CreateNewUser;
use Illuminate\Http\JsonResponse;


class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, CreateNewUser $createNewUser,): JsonResponse 
    {
        //
        $user = $createNewUser->create(
            $request->only('name', 'email', 'password', 'password_confirmation')
        );

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'access_token' => $user ->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user->only('id', 'name', 'email', 'role'),
        ], 201);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param Request $request
     * @return JsonResponse message
     */
    public function signup(Request $request) : JsonResponse
    {
        $request->validate([
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'required|string|email|unique:users,email',
            'data.attributes.password' => 'required|string|confirmed',
            'data.attributes.phone_number'=>'required|numeric',
            'data.attributes.username'=>'required|unique:users,username|string',
            'data.attributes.birthday'=>['required', 'date_format:d/m/Y'],
        ]);

        $user = new User([
            'name' => $request->input('data')['attributes']['name'],
            'email' => $request->input('data')['attributes']['email'],
            'password' => Hash::make($request->input('data')['attributes']['password']),
            'phone_number'=>$request->input('data')['attributes']['phone_number'],
            'username'=>$request->input('data')['attributes']['username'],
            'birthday'=>Carbon::createFromFormat('d/m/Y', $request->input('data')['attributes']['birthday']),
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return JsonResponse access_token
     */
    public function login(Request $request) : JsonResponse
    {
        $request->validate([
            'data.attributes.email' => 'required|string|email',
            'data.attributes.password' => 'required|string',
            'data.attributes.remember_me' => 'boolean'
        ]);

        $credentials = [
            'email'=>$request->get('data')['attributes']['email'],
            'password'=>$request->get('data')['attributes']['password'],
        ];

        if(!Auth::attempt($credentials))
            return response()->json([
                'errors' =>
                    [
                        'status'=>401,
                        'title'=>"Unauthorized",
                        'detail'=>"Credentials don't match our records",
                    ]
            ], 401);

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;

        if ($request->input('remember_me'))
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function forgot(Request $request)
    {
        $email = ['email'=>$request->get('data')['attributes']['email']];
        try {
            $response = Password::sendResetLink($email);

            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return response()->json([], 204);
                case Password::INVALID_USER:
                    return response()->json([
                        'errors'=>[
                            'title' => 'Bad Request',
                            'detail' => trans($response),
                            'status' => '400',
                            'meta' => [
                                'key' => 'email',
                                'pointer' => '/data/attributes/email'
                            ]
                        ]
                    ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'errors'=>[
                    'title' => 'Bad Request',
                    'detail' => $ex->getMessage(),
                    'status' => '400',
                ]
            ]);
        }
    }

    public function reset(Request $request){
            $request->validate([
                'data.attributes.token' => 'required',
                'data.attributes.email' => 'required|email',
                'data.attributes.password' => 'required|confirmed|min:8',
            ]);

            $credentials = [
                'email'=>$request->get('data')['attributes']['email'],
                'password'=>$request->get('data')['attributes']['password'],
                'password_confirmation'=>$request->get('data')['attributes']['password_confirmation'],
                'token'=>$request->get('data')['attributes']['token'],
            ];

        try {
            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $response =Password::reset($credentials,
                function ($user, $password) {
                    $this->resetPassword($user, $password);
                }
            );

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            switch ($response) {
                case Password::PASSWORD_RESET:
                    return response()->json([], 204);
                case Password::INVALID_USER:
                    return response()->json([
                        'errors'=>[
                            'title' => 'Bad Request',
                            'detail' => trans($response),
                            'status' => '400',
                            'meta' => [
                                'key' => 'email',
                                'pointer' => '/data/attributes/email'
                            ]
                        ]
                    ]);
                case Password::INVALID_TOKEN:
                    return response()->json([
                        'errors'=>[
                        'title' => 'Bad Request',
                        'detail' => trans($response),
                        'status' => '400',
                        'meta' => [
                            'key' => 'token',
                            'pointer' => '/data/attributes/token'
                        ]]
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'errors'=>[
                'title' => 'Bad Request',
                'detail' => $e->getMessage(),
                'status' => '400',]
            ]);
        }
    }

    /**
     * Reset the given user's password.
     * We overwrite this method since we are stateless so the login and rember token
     * are no longer needed after password reset
     * Also we have a mutator on user model that automatically hashes the password
     *
     * @param Authenticatable $user
     * @param string $password
     * @return void
     */
    protected function resetPassword(Authenticatable $user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordReset($user));
    }
}

<?php

namespace  App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate a user.
     *
     *@group User
     * @bodyParam email string required
     * @bodyParam password string required
     * @responseFile responses/user.authenticate.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            "email" => "required|string",
            "password" => "required|string"
        ], Config::get('validation.messages'));
        $authResult = User::authenticate($request->input('email'), $request->input('password'), $request->ip(), true);
        if ($authResult !== FALSE) {
            return $authResult;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401 );
        }
    }

    /**
     * Returns the current logged user
     *
     * @return \Illuminate\Http\Response
     */
    public function showMe()
    {
        $user = User::find(Auth::user()->id);
        return $user;
    }

    /**
     * Display the permissions for the given entity.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getPermissions($id)
    {
        try {
            if (\Auth::user()['profile'] == 'admin') {
                $permissionResult = Permission::getPermissions($id);
                Activity::add(\Auth::user()['id'], $id, AuthServiceProvider::READ_ENTITY, TRUE, 'getPermissions', "{}");
                return (new ApiResponse($permissionResult, TRUE))->response();
            } else {
                Activity::add(\Auth::user()['id'], $id, AuthServiceProvider::READ_ENTITY, FALSE, 'getPermissions', json_encode(["error" => ApiResponse::TEXT_FORBIDDEN]));
                return (new ApiResponse(NULL, FALSE, ApiResponse::TEXT_FORBIDDEN, ApiResponse::STATUS_FORBIDDEN))->response();
            }
        } catch (\Exception $e) {
            $exceptionDetails = ExceptionDetails::filter($e);
            Activity::add(\Auth::user()['id'], $id, AuthServiceProvider::READ_ENTITY, FALSE, 'getPermissions', json_encode(["error" => $exceptionDetails['info']]));
            return (new ApiResponse(NULL, FALSE, $exceptionDetails['info'], $exceptionDetails['info']['code']))->response();
        }
    }
}

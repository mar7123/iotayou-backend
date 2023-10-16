<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserController extends Controller
{
    public function createUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'address' => 'required',
                'password' => 'required',
                'user_type' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $salt = Str::random(10);
            $user = User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_num' => $request->phone_num,
                'address' => $request->address,
                'salt' => $salt,
                'password' => $request->password,
                'user_type' => $request->user_type,
            ]);
            return Response([
                'status' => true,
                'message' => 'Created successfully',
            ], 201);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function loginUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $saltres = User::select('salt')->where('email', $request->email)->first();
            if ($saltres == null) {
                return Response([
                    'status' => false,
                    'message' => 'account not found'
                ], 401);
            }
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password . $saltres->salt])) {
                $request->session()->regenerate();
                $expiry = new DateTime();
                $expiry->modify('+30 minutes');
                $user = User::where('email', $request->email)->first();
                $success =  $user->createToken('User Token', ['*'], $expiry)->plainTextToken;
                activity()
                    ->causedBy($user)
                    ->event('logged in')
                    ->log('logged in');
                return Response([
                    'status' => true,
                    'message' => 'User logged in successfully',
                    'token' => $success
                ], 200);
            }
            return Response([
                'status' => false,
                'message' => 'email or password wrong'
            ], 401);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function newReg(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'address' => 'required',
                'password' => 'required',
                'user_type' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if ($request->user()->user_type >= $request->user_type) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $salt = Str::random(10);
            $user = User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_num' => $request->phone_num,
                'address' => $request->address,
                'salt' => $salt,
                'password' => $request->password,
                'user_type' => $request->user_type,
            ]);
            return Response([
                'status' => true,
                'message' => 'Created successfully',
            ], 201);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function updateReg(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required',
                'username' => 'required',
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'address' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if ($request->user()->user_type >= $request->user_type) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $reg = User::where('user_id', $request->user_id)->first();
            $reg->update([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_num' => $request->phone_num,
                'address' => $request->address,
            ]);
            return Response([
                'status' => true,
                'message' => 'updated successfully',
            ], 201);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function updateUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'address' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $user = $request->user();
            $user->update($request->all());
            return Response([
                'status' => true,
                'message' => 'updated successfully'
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function getClients(Request $request): Response
    {
        try {
            if ($request->user()->user_type > 1) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()->children()->get();
            return Response([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function getCustomers(Request $request): Response
    {
        try {
            // if ($request->user()->user_type > 2) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            // $result = $request->user()->children()->get();
            // if ($request->user()->user_type == 1) {
            //     $adm = new Collection();
            //     foreach ($result as $rs) {
            //         $temp = $rs->children()->get();
            //         $adm = $adm->concat($temp);
            //     }
            //     $result = $adm;
            // }
            $result = User::where('user_type', 3)->get();
            return Response([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function logout(Request $request): Response
    {
        try {
            $request->user()->tokens()->delete();
            activity()
                ->causedBy($request->user())
                ->event('logged out')
                ->log('logged out');
            return Response(['data' => 'User Logout successfully.'], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

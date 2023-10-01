<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
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
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'password' => 'required',
                'address' => 'required',
                'birth_date' => 'required|date',
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
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_num' => $request->phone_num,
                'salt' => $salt,
                'password' => Hash::make($request->password . $salt),
                'address' => $request->address,
                'birth_date' => $request->birth_date,
                'sendalmsms' => 1,
                'sendalmemail' => 1,
                'sendreport' => 1,
                'announcement' => 1,
                'user_type' => 1,
                'created_by' => 1,
            ]);
            return Response([
                'status' => true,
                'message' => 'User created successfully',
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
    public function updateUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_num' => 'required',
                'password' => 'required',
                'address' => 'required',
                'birth_date' => 'required|date',
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
                'message' => 'profile updated'
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function getAdminChildren(Request $request): Response
    {
        try {
            if ($request->user()->user_type != 1) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()->load(['children', 'children.children', 'children.children.sites', 'children.children.sites.printers']);
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
    public function getClientChildren(Request $request): Response
    {
        try {
            if ($request->user()->user_type != 2) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()->load(['children', 'children.sites', 'children.sites.printers']);
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
    public function getCustomerChildren(Request $request): Response
    {
        try {
            if ($request->user()->user_type != 3) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()->load(['sites', 'sites.printers']);
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
            return Response(['data' => 'User Logout successfully.'], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

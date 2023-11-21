<?php

namespace App\Http\Controllers;

use App\Models\Role;
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
                $user = User::where('email', $request->email)->first();
                $login_token = $user->tokens()->where('name', 'Login Token')->get();
                if ($login_token->first() != null) {
                    foreach ($login_token as $lt) {
                        $lt->delete();
                    }
                }
                $request->session()->regenerate();
                $expiry = new DateTime();
                $expiry->modify('+30 minutes');
                $success =  $user->createToken('Login Token', ['*'], $expiry)->plainTextToken;
                activity()
                    ->causedBy($user)
                    ->event('logged in')
                    ->log('logged in');
                return Response([
                    'status' => true,
                    'message' => 'User logged in successfully',
                    'token' => $success,
                    'userData' => $user->setVisible(['email'])
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
    public function createUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'name' => 'required',
                'password' => 'required',
                'status' => 'integer|between:6,7',
                'user_role_id' => 'required|uuid|exists:roles,role_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $role = Role::where('role_id', $request->user_role_id)->first();
            $req_role = $request->user()
                ->role()
                ->first();
            $temp = $role;
            while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->role_id != $req_role->role_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $permission = $req_role
                ->role_permissions()
                ->where('user_group_id', $role->role_type)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 1, 1) != "a") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $salt = Str::random(10);
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->name,
                'salt' => $salt,
                'password' => Hash::make($request->password . $salt),
                'status' => $request->status,
                'notes' => $request->notes,
                'user_role_id' => $request->user_role_id,
                'phone_num' => $request->phone_num,
                'picture' => $request->picture,
            ]);
            activity()
                ->causedBy($request->user())
                ->performedOn($user)
                ->withProperties($request->all())
                ->event('created')
                ->log('create user');
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
    public function updateUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required|uuid|exists:users,user_id',
                'name' => 'required',
                'password' => 'required',
                'status' => 'integer|between:6,7',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $user = User::where('user_id', $request->user_id)->first();
            if ($user->username != $request->username) {
                $validateUnique = Validator::make($request->all(), [
                    'username' => 'required|unique:users,username',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            if ($user->email != $request->email) {
                $validateUnique = Validator::make($request->all(), [
                    'email' => 'required|unique:users,email',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            $role = $user->role()->first();
            $req_role = $request->user()
                ->role()
                ->first();
            $temp = $role;
            while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->role_id != $req_role->role_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $permission = $req_role
                ->role_permissions()
                ->where('user_group_id', $role->role_type)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 2, 1) != "e") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $user = User::where('user_id', $request->user_id)->first();
            $salt = Str::random(10);
            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->name,
                'salt' => $salt,
                'password' => Hash::make($request->password . $salt),
                'status' => $request->status,
                'notes' => $request->notes,
                'phone_num' => $request->phone_num,
                'picture' => $request->picture,
            ]);
            activity()
                ->causedBy($request->user())
                ->performedOn($user)
                ->withProperties($request->all())
                ->event('updated')
                ->log('update user');
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
    public function deleteUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required|uuid|exists:users,user_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $role = User::where('user_id', $request->user_id)->first()->role()->first();
            $req_role = $request->user()
                ->role()
                ->first();
            $temp = $role;
            while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->role_id != $req_role->role_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $permission = $req_role
                ->role_permissions()
                ->where('user_group_id', $role->role_type)
                ->first();

            if ($permission == null || substr($permission->pivot->role_permission, 3, 1) != "d") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $user = User::where('user_id', $request->user_id)->first();
            $user->delete();
            activity()
                ->causedBy($request->user())
                ->performedOn($user)
                ->withProperties($user)
                ->event('deleted')
                ->log('delete user');
            return Response([
                'status' => true,
                'message' => 'deleted successfully'
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function getUsers(Request $request): Response
    {
        try {
            $req_role = $request->user()
                ->role()
                ->first();
            $temp = $req_role;
            while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                $temp = $temp->parent()->first();
            }
            $result = $req_role->children()->get();
            $users = new Collection();
            while ($result->first()->role_type != 3) {
                $user_temp = new Collection();
                foreach ($result as $rs) {
                    $us = $rs->users()
                        ->get();
                    $user_temp = $user_temp->concat($us);
                }
                $users = $users->concat($user_temp);
                $temp = new Collection();
                foreach ($result as $rs) {
                    $ch = $rs->children()
                        ->get();
                    $temp = $temp->concat($ch);
                }
                $result = $temp;
            }
            foreach ($result as $rs) {
                $us = $rs->users()
                    ->get();
                $user_temp = $user_temp->concat($us);
            }
            $users = $users->concat($user_temp);
            return Response([
                'status' => true,
                'data' => $users,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

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
    /**
     * AUTH
     */
    public function createUser(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'code' => 'required',
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
                'code' => $request->code,
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
    public function updateUser(Request $request): Response
    {
        try {
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

    /**
     * CRUD CLIENT CUSTOMER
     */
    public function newClientCust(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'code' => 'required',
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
            $parent = $request->user();
            $req_user_id = $parent->user_id;
            $req_user_type = $parent->user_type;
            $permission = $parent->user_permissions()->where('user_groups.user_group_id', $request->user_type)->first();
            if ($permission == null || substr($permission->pivot->user_permission, 1, 1) != "a") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            if ($request->user_type - $parent->user_type != 1) {
                $validateParent = Validator::make($request->all(), [
                    'parent_id' => 'required'
                ]);
                if ($validateParent->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateParent->errors()
                    ], 401);
                }
                $parent = User::where('user_id', $request->parent_id)->first();
                $temp = $parent;
                while ($temp->parent()->first() != null && $temp->user_type != $req_user_type) {
                    $temp = $temp->parent()->first();
                }
                if ($request->user_type - $parent->user_type != 1 || $temp->user_id != $req_user_id) {
                    return Response([
                        'status' => false,
                        'message' => 'invalid parent id',
                    ], 401);
                }
            }
            $salt = Str::random(10);
            $user = User::create([
                'code' => $request->code,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_num' => $request->phone_num,
                'address' => $request->address,
                'salt' => $salt,
                'password' => $request->password,
                'user_type' => $request->user_type,
            ]);
            $parent->children()->attach($user->user_id);
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
    public function updateClientCust(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = User::where('user_id', $request->user_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'User not found',
                ], 401);
            }
            $requs = $request->user();
            $permission = $requs->user_permissions()->where('user_groups.user_group_id', $reg->user_type)->first();
            if ($permission == null || substr($permission->pivot->user_permission, 2, 1) != "e") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $temp = $reg;
            while ($temp->parent()->first() != null && $temp->user_type != $requs->user_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->user_id != $requs->user_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $reg->update($request->except(['user_id']));
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
    public function deleteClientCust(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = User::where('user_id', $request->user_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'User not found',
                ], 401);
            }
            $requs = $request->user();
            $permission = $requs->user_permissions()->where('user_groups.user_group_id', $reg->user_type)->first();
            if ($permission == null || substr($permission->pivot->user_permission, 3, 1) != "d") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $temp = $reg;
            while ($temp->parent()->first() != null && $temp->user_type != $requs->user_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->user_id != $requs->user_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $reg->delete();
            return Response([
                'status' => true,
                'message' => 'deleted successfully',
            ], 201);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * USER CHILDREN
     */
    public function getClients(Request $request): Response
    {
        try {
            if ($request->user()->user_type > 1) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()
                ->children()
                ->get()
                ->each(function ($item, $key) {
                    $item->user_permissions;
                });
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
            if ($request->user()->user_type > 2) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $request->user()->children()->get();
            if ($request->user()->user_type == 1) {
                $adm = new Collection();
                foreach ($result as $rs) {
                    $temp = $rs->children()->get();
                    $adm = $adm->concat($temp);
                }
                $result = $adm;
            }
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
}

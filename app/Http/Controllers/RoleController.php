<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RoleController extends Controller
{
    /**
     * CRUD CLIENT CUSTOMER
     */
    public function newRole(Request $request): Response
    {
        try {
            $validateRole = Validator::make($request->all(), [
                'code' => 'required|unique:roles,code',
                'name' => 'required',
                'address' => 'required',
                'status' => 'integer|between:6,7',
                'role_type' => 'required|exists:user_groups,user_group_id',
            ]);
            if ($validateRole->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateRole->errors()
                ], 401);
            }
            $req_user = $request->user();
            $parent = $req_user
                ->role()
                ->first();
            $req_role_id = $parent->role_id;
            $req_role_type = $parent->role_type;
            $permission = $parent
                ->role_permissions()
                ->where('user_group_id', $request->role_type)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 1, 1) != "a") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            if ($request->role_type - $parent->role_type != 1) {
                $validateParent = Validator::make($request->all(), [
                    'parent_id' => 'required|exists:roles,role_id',
                ]);
                if ($validateParent->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateParent->errors()
                    ], 401);
                }
                $parent = Role::where('role_id', $request->parent_id)->first();
                $temp = $parent;
                while ($temp->parent()->first() != null && $temp->role_type != $req_role_type) {
                    $temp = $temp->parent()->first();
                }
                if ($temp->role_id != $req_role_id) {
                    return Response([
                        'status' => false,
                        'message' => 'invalid parent id',
                    ], 401);
                }
            }
            $role = Role::create([
                'code' => $request->code,
                'name' => $request->name,
                'address' => $request->address,
                'status' => $request->status,
                'notes' => $request->notes,
                'role_type' => $request->role_type,
                'parent_id' => $parent->role_id,
            ]);
            activity()
                ->causedBy($req_user)
                ->performedOn($role)
                ->withProperties($role)
                ->event('created')
                ->log('created ' . $role->role_t);
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
    public function updateRole(Request $request): Response
    {
        try {
            $validateRole = Validator::make($request->all(), [
                'role_id' => 'required|uuid|exists:roles,role_id',
                'name' => 'required',
                'address' => 'required',
                'status' => 'integer|between:6,7',
            ]);
            if ($validateRole->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateRole->errors()
                ], 401);
            }
            $role = Role::where('role_id', $request->role_id)->first();
            if ($role->code != $request->code) {
                $validateUnique = Validator::make($request->all(), [
                    'code' => 'required|unique:roles,code',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            $req_user = $request->user();
            $req_role = $req_user
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
            $role->update($request->except([
                'role_id',
                'role_type',
                'parent_id',
                'deleted_at'
            ]));
            activity()
                ->causedBy($req_user)
                ->performedOn($role)
                ->withProperties($request->all())
                ->event('updated')
                ->log('updated ' . $role->role_t);
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
    public function deleteRole(Request $request): Response
    {
        try {
            $validateRole = Validator::make($request->all(), [
                'role_id' => 'required|uuid|exists:roles,role_id',
            ]);
            if ($validateRole->fails()) {
                return Response([
                    'message' => 'validation error',
                    'errors' => $validateRole->errors()
                ], 401);
            }
            $role = Role::where('role_id', $request->role_id)->first();
            $req_user = $request->user();
            $req_role = $req_user
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
            $role->delete();
            activity()
                ->causedBy($req_user)
                ->performedOn($role)
                ->withProperties($role)
                ->event('deleted')
                ->log('deleted ' . $role->role_t);
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
    public function assignRolePermission(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'role_id' => 'required|uuid|exists:roles,role_id',
                'role_permissions' => 'required|array',
                'role_permissions.*.user_group_id' => 'required|exists:user_groups,user_group_id',
                'role_permissions.*.role_permission' => 'required|alpha_dash|size:4',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $role = Role::where('role_id', $request->role_id)->first();
            $perms = array();
            foreach ($request->role_permissions as $rp) {
                if ($rp["user_group_id"] <= $role->role_type) {
                    continue;
                }
                $perms[$rp["user_group_id"]] = ['role_permission' => $rp["role_permission"]];
            }
            $role->role_permissions()->sync($perms);
            activity()
                ->causedBy($request->user())
                ->performedOn($role)
                ->withProperties($perms)
                ->event('updated')
                ->log('update permission');
            return Response([
                'status' => true,
                'data' => "permission updated",
            ], 200);
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
    public function getChildren(Request $request): Response
    {
        try {
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
            $permission = $req_role->role_permissions()->where('user_group_id', '<=', 4)->get();
            if ($permission->first() == null) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            foreach ($permission as $pr) {
                if (substr($pr->pivot->role_permission, 0, 1) != "v") {
                    return Response([
                        'status' => false,
                        'data' => 'Unauthorized',
                    ], 401);
                }
            }
            $role_id = $req_role->role_id;
            $take = 5;
            $page = 1;
            if ($request->input('take')) {
                $take = $request->input('take');
            }
            if ($request->input('page')) {
                $page = $request->input('page');
            }
            $result = DB::select("with RECURSIVE cte as (select * from roles where role_id='" . $role_id . "' union all select e.* from roles e inner join cte on e.parent_id=cte.role_id ) select * from cte LIMIT " . $take . " OFFSET " . ($page - 1) * $take . "");
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
    public function getClients(Request $request): Response
    {
        try {
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
            if ($req_role->role_type > 2) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $permission = $req_role->role_permissions()->where('user_group_id', 3)->first();
            if ($permission == null || substr($permission->pivot->role_permission, 0, 1) != "v") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $req_role->children()->get();
            while ($result->first()->role_type != 2) {
                $temp = new Collection();
                foreach ($result as $rs) {
                    $ch = $rs->children()
                        ->get();
                    $temp = $temp->concat($ch);
                }
                $result = $temp;
            }
            if ($request->input('permission') == "true") {
                foreach ($result as $rs) {
                    $rs->load(['role_permissions']);
                }
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
    public function getCustomers(Request $request): Response
    {
        try {
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
            if ($req_role->role_type > 3) {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $permission = $req_role->role_permissions()->where('user_group_id', 4)->first();
            if ($permission == null || substr($permission->pivot->role_permission, 0, 1) != "v") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $req_role->children()->get();
            while ($result->first()->role_type != 4) {
                $temp = new Collection();
                foreach ($result as $rs) {
                    $ch = $rs->children()
                        ->get();
                    $temp = $temp->concat($ch);
                }
                $result = $temp;
            }
            if ($request->input('permission') == "true") {
                foreach ($result as $rs) {
                    $rs->load(['role_permissions']);
                }
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

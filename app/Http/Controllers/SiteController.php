<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\UserGroups;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SiteController extends Controller
{
    private $user_group_id;
    function __construct()
    {
        $ug = UserGroups::where('name', 'Site')->first();
        $this->user_group_id = $ug->user_group_id;
    }
    public function getSites(Request $request): Response
    {
        try {
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
            $permission = $req_user->user_permissions()->where('user_group_id', $this->user_group_id)->first();
            if ($permission == null || substr($permission->pivot->user_permission, 0, 1) != "v") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $result = $req_role->children()->get();
            while ($result->first()->role_type != 3) {
                $temp = new Collection();
                foreach ($result as $rs) {
                    $ch = $rs->children()
                        ->get();
                    $temp = $temp->concat($ch);
                }
                $result = $temp;
            }
            $st = new Collection();
            foreach ($result as $cu) {
                $temp = $cu->sites()->get();
                $st = $st->concat($temp);
            }
            return Response([
                'status' => true,
                'data' => $st,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function createSite(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'code' => 'required|unique:sites,code',
                'name' => 'required',
                'location' => 'required',
                'address' => 'required',
                'status' => 'integer|between:6,7',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            // if ($request->user()->user_type >= $request->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
            $parent = $req_role;
            $permission = $req_user
                ->user_permissions()
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->user_permission, 1, 1) != "a") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            if ($parent->role_type != 3) {
                $validateParent = Validator::make($request->all(), [
                    'customer_id' => 'required|uuid|exists:roles,role_id'
                ]);
                if ($validateParent->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateParent->errors()
                    ], 401);
                }
                $parent = Role::where('role_id', $request->customer_id)->first();
                $temp = $parent;
                while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                    $temp = $temp->parent()->first();
                }
                if ($temp->role_id != $req_role->role_id) {
                    return Response([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ], 401);
                }
            }
            $site = Site::create([
                "customer_id" => $parent->role_id,
                "code" => $request->code,
                "name" => $request->name,
                "address" => $request->address,
                "location" => $request->location,
                "status" => $request->status,
                "notes" => $request->notes,
            ]);
            activity()
                ->causedBy($req_user)
                ->performedOn($site)
                ->withProperties($site)
                ->event('created')
                ->log('created site');
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
    public function updateSite(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'site_id' => 'required|uuid|exists:sites,site_id',
                'name' => 'required',
                'address' => 'required',
                'location' => 'required',
                'status' => 'integer|between:6,7',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Site::where('site_id', $request->site_id)->first();
            $role = $reg->customers()->first();
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
            $permission = $req_user
                ->user_permissions()
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->user_permission, 2, 1) != "e") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            if ($reg->code != $request->code) {
                $validateUnique = Validator::make($request->all(), [
                    'code' => 'required|unique:sites,code',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            $reg->update($request->except(['site_id']));
            activity()
                ->causedBy($req_user)
                ->performedOn($reg)
                ->withProperties($reg)
                ->event('updated')
                ->log('updated site');
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
    public function deleteSite(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'site_id' => 'required|uuid|exists:sites,site_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Site::where('site_id', $request->site_id)->first();
            $role = $reg->customers()->first();
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
            $permission = $req_user
                ->user_permissions()
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->user_permission, 3, 1) != "d") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $reg->delete();
            activity()
                ->causedBy($req_user)
                ->performedOn($reg)
                ->withProperties($reg)
                ->event('deleted')
                ->log('deleted site');
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
}

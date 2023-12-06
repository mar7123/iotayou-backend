<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PermissionController extends Controller
{
    public function getPermissionByID(Request $request): Response
    {
        try {
            $validateRole = Validator::make($request->all(), [
                'role_id' => 'required|uuid|exists:roles,role_id',
            ]);
            if ($validateRole->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateRole->errors()
                ], 401);
            }
            $req_user = $request->user();
            $req_role = $req_user
                ->role()
                ->first();
            $role = Role::where('role_id', $request->role_id)->first();
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
            $result = $role->load(['role_permissions']);
            return Response([
                'status' => true,
                'data' => $result,
            ], 201);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

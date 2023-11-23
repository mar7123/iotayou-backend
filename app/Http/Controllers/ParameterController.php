<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Models\UserGroups;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ParameterController extends Controller
{
    private $user_group_id;
    function __construct()
    {
        $ug = UserGroups::where('name', 'Parameter')->first();
        $this->user_group_id = $ug->user_group_id;
    }
    public function getParameters(Request $request): Response
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
            $result = Parameter::get();
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
    public function getParameterByInstrument(Request $request, string $ins_id): Response
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
            $result = Parameter::where('instrument_id', $ins_id)->get();
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
    public function createParameter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "instrument_id" => "required|uuid|exists:instruments,instrument_id",
                'code' => 'required|unique:parameters,code',
                'name' => 'required',
                'status' => 'integer|between:6,7',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $req_user = $request->user();
            $req_role = $req_user->role()->first();
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
            $parameter = Parameter::create([
                "instrument_id" => $request->instrument_id,
                "code" => $request->code,
                "name" => $request->name,
                "status" => $request->status,
                "notes" => $request->notes,
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
    public function updateParameter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'parameter_id' => 'required|uuid|exists:parameters,parameter_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $req_user = $request->user();
            $req_role = $req_user
                ->role()
                ->first();
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
            $reg = Parameter::where('parameter_id', $request->parameter_id)->first();
            if ($reg->code != $request->code) {
                $validateUnique = Validator::make($request->all(), [
                    'code' => 'required|unique:parameters,code',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            $reg->update($request->except(['parameter_id']));
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
    public function deleteParameter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'parameter_id' => 'required|uuid|exists:parameters,parameter_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $req_user = $request->user();
            $req_role = $req_user
                ->role()
                ->first();
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
            $reg = Parameter::where('parameter_id', $request->parameter_id)->first();
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
}

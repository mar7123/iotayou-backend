<?php

namespace App\Http\Controllers;

use App\Models\Instrument;
use App\Models\UserGroups;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InstrumentController extends Controller
{
    private $user_group_id;
    function __construct()
    {
        $ug = UserGroups::where('name', 'Instrument')->first();
        $this->user_group_id = $ug->user_group_id;
    }
    public function getInstruments(Request $request): Response
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
            $result = Instrument::get();
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
    public function createInstrument(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'code' => 'required|unique:instruments,code',
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
            $instrument = Instrument::create([
                "code" => $request->code,
                "name" => $request->name,
                "status" => $request->status,
                "notes" => $request->notes,
            ]);
            activity()
                ->causedBy($req_user)
                ->performedOn($instrument)
                ->withProperties($instrument)
                ->event('created')
                ->log('created instrument');
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
    public function updateInstrument(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'instrument_id' => 'required|uuid|exists:instruments,instrument_id',
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
            $reg = Instrument::where('instrument_id', $request->instrument_id)->first();
            if ($reg->code != $request->code) {
                $validateUnique = Validator::make($request->all(), [
                    'code' => 'required|unique:instruments,code',
                ]);
                if ($validateUnique->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUnique->errors()
                    ], 401);
                }
            }
            $reg->update($request->except(['instrument_id']));
            activity()
                ->causedBy($req_user)
                ->performedOn($reg)
                ->withProperties($reg)
                ->event('updated')
                ->log('updated instrument');
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
    public function deleteInstrument(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'instrument_id' => 'required|uuid|exists:instruments,instrument_id',
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
            $reg = Instrument::where('instrument_id', $request->instrument_id)->first();
            $reg->delete();
            activity()
                ->causedBy($req_user)
                ->performedOn($reg)
                ->withProperties($reg)
                ->event('deleted')
                ->log('deleted instrument');
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

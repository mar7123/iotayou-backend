<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Printer;
use App\Models\UserGroups;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AlarmController extends Controller
{
    private $user_group_id;
    function __construct()
    {
        $ug = UserGroups::where('name', 'Alarm')->first();
        $this->user_group_id = $ug->user_group_id;
    }
    public function getAlarms(Request $request): Response
    {
        try {
            $req_role = $request->user()->role()->first();
            $permission = $req_role->role_permissions()->where('user_group_id', $this->user_group_id)->first();
            if ($permission == null || substr($permission->pivot->role_permission, 0, 1) != "v") {
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
            $pr = new Collection();
            foreach ($st as $site) {
                $temp = $site->printers()->get();
                $pr = $pr->concat($temp);
            }
            $alr = new Collection();
            foreach ($pr as $printer) {
                $temp = $printer->alarms()->get();
                $alr = $alr->concat($temp);
            }
            return Response([
                'status' => true,
                'data' => $alr,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function createAlarm(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "printer_id" => 'required|uuid|exists:printers,printer_id',
                "parameter_id" => 'required|uuid|exists:parameters,parameter_id',
                "name" => 'required',
                "condition" => 'required',
                "status" => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $req_role = $request->user()->role()->first();
            $permission = $req_role
                ->role_permissions()
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 1, 1) != "a") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            $temp = Printer::where('printer_id', $request->printer_id)->first()->sites()->first()->customers()->first();
            while ($temp->parent()->first() != null && $temp->role_type != $req_role->role_type) {
                $temp = $temp->parent()->first();
            }
            if ($temp->role_id != $req_role->role_id) {
                return Response([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $alarm = Alarm::create([
                "printer_id" => $request->printer_id,
                "parameter_id" => $request->parameter_id,
                "name" => $request->name,
                "condition" => $request->condition,
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
    public function updateAlarm(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'alarm_id' => 'required|uuid|exists:alarms,alarm_id',
                "name" => 'required',
                "condition" => 'required',
                "status" => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Alarm::where('alarm_id', $request->alarm_id)->first();
            $role = $reg->printers()->first()->sites()->first()->customers()->first();
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
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 2, 1) != "e") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            $reg->update($request->except(['alarm_id']));
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
    public function deleteAlarm(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'alarm_id' => 'required|uuid|exists:alarms,alarm_id',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Alarm::where('alarm_id', $request->alarm_id)->first();
            $role = $reg->printers()->first()->sites()->first()->customers()->first();
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
                ->where('user_group_id', $this->user_group_id)
                ->first();
            if ($permission == null || substr($permission->pivot->role_permission, 3, 1) != "d") {
                return Response([
                    'status' => false,
                    'data' => 'Unauthorized',
                ], 401);
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
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

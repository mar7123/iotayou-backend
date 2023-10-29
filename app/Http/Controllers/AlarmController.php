<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AlarmController extends Controller
{
    public function getAlarms(Request $request): Response
    {
        try {
            $result = Alarm::get();
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
    public function createAlarm(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "printer_id" => 'required',
                "parameter_id" => 'required',
                "code" => 'required',
                "name" => 'required',
                "condition" => 'required',
                "status" => 'required',
                "notes" => 'required',
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
            $alarm = Alarm::create([
                "printer_id" => $request->printer_id,
                "parameter_id" => $request->parameter_id,
                "code" => $request->code,
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
                'alarm_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Alarm::where('alarm_id', $request->alarm_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Alarm not found',
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
                'alarm_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Alarm::where('alarm_id', $request->alarm_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Alarm not found',
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

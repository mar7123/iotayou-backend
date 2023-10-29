<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ParameterController extends Controller
{
    public function getParameters(Request $request): Response
    {
        try {
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
    public function createParameter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "instrument_id" => 'required',
                "code" => 'required',
                "name" => 'required',
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
                'parameter_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Parameter::where('parameter_id', $request->parameter_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Parameter not found',
                ], 401);
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
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
                'parameter_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Parameter::where('parameter_id', $request->parameter_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Parameter not found',
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

<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class PrinterController extends Controller
{
    public function deviceList(Request $request): Response
    {
        try {
            $req_role = $request->user()->role()->first();
            $permission = $req_role->role_permissions()->where('user_group_id', 5)->first();
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
            return Response([
                'status' => false,
                'data' => $pr,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => true,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function createPrinter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'site_id' => 'required',
                'instrument_id' => 'required',
                "code" => 'required',
                "name" => 'required',
                "ip_addr" => 'required',
                "printer_port" => 'required',
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
            $printer = Printer::create([
                'site_id' => $request->site_id,
                'instrument_id' => $request->instrument_id,
                "code" => $request->code,
                "name" => $request->name,
                "ip_addr" => $request->ip_addr,
                "printer_port" => $request->printer_port,
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
    public function updatePrinter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'printer_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Printer::where('printer_id', $request->printer_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Printer not found',
                ], 401);
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            $reg->update($request->except(['printer_id']));
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
    public function deletePrinter(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'printer_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Printer::where('printer_id', $request->printer_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Printer not found',
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

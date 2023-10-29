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
            $usr = new Collection([$request->user()]);
            $type = $usr->first()->user_type;
            if ($type <= 2) {
                $usr = $usr->first()->children()->get();
                if ($type <= 1) {
                    $adm = new Collection();
                    foreach ($usr as $cl) {
                        $temp = $cl->children()->get();
                        $adm = $adm->concat($temp);
                    }
                    $usr = $adm;
                }
            }
            $st = new Collection();
            foreach ($usr as $cu) {
                $temp = $cu->sites()->get();
                $st = $st->concat($temp);
            }
            $pr = new Collection();
            foreach ($st as $site) {
                $temp = $site->printers()->get();
                $pr = $pr->concat($temp);
            }
            activity()
                ->causedBy($request->user())
                ->performedOn($pr->first())
                ->withProperties($pr)
                ->event('retrieved')
                ->log('listed devices');
            return Response([
                'status' => true,
                'data' => $pr,
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
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

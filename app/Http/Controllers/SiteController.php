<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SiteController extends Controller
{
    public function getSites(Request $request): Response
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
                "code" => 'required',
                "name" => 'required',
                "address" => 'required',
                "location" => 'required',
                "status" => 'required',
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
            $parent = $request->user();
            if ($parent->user_type != 3) {
                $validateParent = Validator::make($request->all(), [
                    'customer_id' => 'required'
                ]);
                if ($validateParent->fails()) {
                    return Response([
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateParent->errors()
                    ], 401);
                }
                $parent = User::where('user_id', $request->customer_id)->first();
                if ($parent->user_type != 3) {
                    return Response([
                        'status' => false,
                        'message' => 'invalid customer id',
                    ], 401);
                }
            }
            $user = Site::create([
                "customer_id" => $parent->user_id,
                "code" => $request->code,
                "name" => $request->name,
                "address" => $request->address,
                "location" => $request->location,
                "status" => $request->status,
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
    public function updateSite(Request $request): Response
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'site_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Site::where('site_id', $request->site_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Site not found',
                ], 401);
            }
            // if ($request->user()->user_type >= $reg->user_type) {
            //     return Response([
            //         'status' => false,
            //         'data' => 'Unauthorized',
            //     ], 401);
            // }
            $reg->update($request->except(['site_id']));
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
                'site_id' => 'required',
            ]);
            if ($validateUser->fails()) {
                return Response([
                    'status' => false,
                    'message' => 'validation_error',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            $reg = Site::where('site_id', $request->site_id)->first();
            if ($reg == null) {
                return Response([
                    'status' => false,
                    'data' => 'Site not found',
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

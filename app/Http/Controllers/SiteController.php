<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                'data' => count($st),
            ], 200);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

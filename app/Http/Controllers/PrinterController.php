<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
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

            // $pr = new Collection();
            // foreach($st as $site){

            // }
            // if ($request->user()->user_type == 1) {
            //     $parent = $request->user()->load(['children', 'children.children', 'children.children.sites', 'children.children.sites.printers']);
            //     $result = collect([]);
            //     foreach ($parent->children as $cl) {
            //         foreach ($cl->children as $cu) {
            //             foreach ($cu->sites as $si) {
            //                 foreach ($si->printers as $pr) {
            //                     $result->push($pr);
            //                 }
            //             }
            //         }
            //     }
            //     activity()
            //         ->causedBy($parent)
            //         ->performedOn($result->first())
            //         ->withProperties($result)
            //         ->event('retrieved')
            //         ->log('listed devices');
            //     return Response([
            //         'status' => true,
            //         'data' => $result,
            //     ], 200);
            // } else if ($request->user()->user_type == 2) {
            //     $parent = $request->user()->load(['children', 'children.sites', 'children.sites.printers']);
            //     $result = collect([]);
            //     foreach ($parent->children as $cu) {
            //         foreach ($cu->sites as $si) {
            //             foreach ($si->printers as $pr) {
            //                 $result->push($pr);
            //             }
            //         }
            //     }
            //     activity()
            //         ->causedBy($parent)
            //         ->performedOn($result->first())
            //         ->withProperties($result)
            //         ->event('retrieved')
            //         ->log('listed devices');
            //     return Response([
            //         'status' => true,
            //         'data' => $result,
            //     ], 200);
            // } else if ($request->user()->user_type == 3) {
            //     $parent = $request->user()->load(['sites', 'sites.printers']);
            //     $result = collect([]);
            //     foreach ($parent->sites as $si) {
            //         foreach ($si->printers as $pr) {
            //             $result->push($pr);
            //         }
            //     }
            //     activity()
            //         ->causedBy($parent)
            //         ->performedOn($result->first())
            //         ->withProperties($result)
            //         ->event('retrieved')
            //         ->log('listed devices');
            //     return Response([
            //         'status' => true,
            //         'data' => $result,
            //     ], 200);
            // }
            return Response([
                'status' => false,
                'data' => $pr,
            ], 401);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

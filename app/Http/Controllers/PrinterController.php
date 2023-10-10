<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class PrinterController extends Controller
{
    public function deviceList(Request $request): Response
    {
        try {
            if ($request->user()->user_type == 1) {
                $parent = $request->user()->load(['children', 'children.children', 'children.children.sites', 'children.children.sites.printers']);
                $result = collect([]);
                foreach ($parent->children as $cl) {
                    foreach ($cl->children as $cu) {
                        foreach ($cu->sites as $si) {
                            foreach ($si->printers as $pr) {
                                $result->push($pr);
                            }
                        }
                    }
                }
                return Response([
                    'status' => true,
                    'data' => $result,
                ], 200);
            } else if ($request->user()->user_type == 2) {
                $parent = $request->user()->load(['children', 'children.sites', 'children.sites.printers']);
                $result = collect([]);
                foreach ($parent->children as $cu) {
                    foreach ($cu->sites as $si) {
                        foreach ($si->printers as $pr) {
                            $result->push($pr);
                        }
                    }
                }
                return Response([
                    'status' => true,
                    'data' => $result,
                ], 200);
            } else if ($request->user()->user_type == 3) {
                $parent = $request->user()->load(['sites', 'sites.printers']);
                $result = collect([]);
                foreach ($parent->sites as $si) {
                    foreach ($si->printers as $pr) {
                        $result->push($pr);
                    }
                }
                return Response([
                    'status' => true,
                    'data' => $result,
                ], 200);
            }
            return Response([
                'status' => false,
                'data' => 'Unauthorized',
            ], 401);
        } catch (Throwable $th) {
            return Response([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

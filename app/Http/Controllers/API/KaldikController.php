<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaldikController extends Controller
{
    public function dashboardKaldik()
    {
        try {
            $kaldik = DB::table('kaldiks')
            ->where('tahun',Carbon::now()->format('Y'))->get();
            return response()->json([
                'status' => 200,
                'all_kaldik' => $kaldik
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], $th->getCode());
        }
    }

    
}

<?php

namespace App\Http\Controllers;

use App\Intel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function active() {
        $active = Intel::active()->orderBy('created_at', 'desc')->get(["country","state","confirmed","deaths","recovered","created_at"]);
        return response()->json($active);
    }

    public function byCountry($country) {
        $active = Intel::where('country','=',$country)->orderBy('created_at', 'desc')->get(["country","state","confirmed","deaths","recovered","created_at"]);
        return response()->json($active);
    }

    public function all() {
        $active = Intel::orderBy('created_at', 'desc')->get(["country","state","confirmed","deaths","recovered","created_at"]);
        return response()->json($active);
    }
}

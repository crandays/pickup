<?php


namespace App\Http\Controllers;

use App\PromocodeUsage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class PromocodeController extends Controller
{
    public function promocode_usage()
    {
        $promocodes = PromocodeUsage::orderBy('created_at' , 'desc')->with('user','promocode')->get();

        return view('admin.promocode.usage', compact('promocodes'));
    }
}

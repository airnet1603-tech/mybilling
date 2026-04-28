<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class QrisController extends Controller
{
    public function index()
    {
        $settings = DB::table('setting')->pluck('value', 'key')->toArray();
        return view('qris', compact('settings'));
    }
}

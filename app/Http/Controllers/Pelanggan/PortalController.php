<?php
namespace App\Http\Controllers\Pelanggan;
use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class PortalController extends Controller
{
    public function showLogin() {
        if (session('pelanggan_id')) return redirect('/pelanggan/dashboard');
        return view('pelanggan.login');
    }
    public function login(Request $request) {
        $request->validate(['username'=>'required','password'=>'required']);
        $pelanggan = Pelanggan::where('username', $request->username)->first();
        if (!$pelanggan) return back()->withErrors(['username'=>'Username tidak ditemukan.'])->withInput();
        $passwordValid = $pelanggan->portal_password
            ? Hash::check($request->password, $pelanggan->portal_password)
            : ($request->password === $pelanggan->password_pppoe);
        if (!$passwordValid) return back()->withErrors(['password'=>'Password salah.'])->withInput();
        session(['pelanggan_id'=>$pelanggan->id,'pelanggan_nama'=>$pelanggan->nama,'pelanggan_user'=>$pelanggan->username]);
        return redirect('/pelanggan/dashboard');
    }
    public function logout() {
        session()->forget(['pelanggan_id','pelanggan_nama','pelanggan_user']);
        return redirect('/pelanggan/login');
    }
    public function dashboard() {
        $pelanggan = Pelanggan::with(['paket','router'])->findOrFail(session('pelanggan_id'));
        $tagihanUnpaid = Tagihan::where('pelanggan_id',$pelanggan->id)->whereIn('status',['unpaid','overdue'])->orderBy('tgl_jatuh_tempo','asc')->get();
        $tagihanPaid = Tagihan::where('pelanggan_id',$pelanggan->id)->where('status','paid')->orderBy('tgl_bayar','desc')->take(5)->get();
        return view('pelanggan.dashboard', compact('pelanggan','tagihanUnpaid','tagihanPaid'));
    }
    public function tagihan() {
        $pelanggan = Pelanggan::findOrFail(session('pelanggan_id'));
        $tagihans = Tagihan::where('pelanggan_id',$pelanggan->id)->orderBy('tgl_tagihan','desc')->paginate(10);
        return view('pelanggan.tagihan', compact('pelanggan','tagihans'));
    }
    public function profil() {
        $pelanggan = Pelanggan::with(['paket','router'])->findOrFail(session('pelanggan_id'));
        return view('pelanggan.profil', compact('pelanggan'));
    }
    public function updatePassword(Request $request) {
        $request->validate(['password_lama'=>'required','password'=>'required|min:6|confirmed']);
        $pelanggan = Pelanggan::findOrFail(session('pelanggan_id'));
        $valid = $pelanggan->portal_password
            ? Hash::check($request->password_lama, $pelanggan->portal_password)
            : ($request->password_lama === $pelanggan->password_pppoe);
        if (!$valid) return back()->withErrors(['password_lama'=>'Password lama salah.']);
        $pelanggan->update(['portal_password'=>Hash::make($request->password)]);
        return back()->with('success','Password berhasil diubah.');
    }
}

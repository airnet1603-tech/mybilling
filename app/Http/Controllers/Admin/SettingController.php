<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $settings = DB::table('setting')->pluck('value', 'key');
        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method', 'qris_file']);

        // Handle checkbox — jika tidak terkirim, set ke 0
        $checkboxes = [
            'wa_jadwal_tagihan', 'wa_jadwal_reminder', 'wa_jadwal_isolir',
            'wa_jadwal_konfirmasi',
        ];
        foreach ($checkboxes as $cb) {
            if (!isset($data[$cb])) $data[$cb] = '0';
        }

        // Handle upload QRIS
        if ($request->hasFile('qris_file') && $request->file('qris_file')->isValid()) {
            $path = public_path('images/payment');
            if (!file_exists($path)) mkdir($path, 0755, true);
            $request->file('qris_file')->move($path, 'qris.jpg');
            $data['wa_qris_url'] = url('images/payment/qris.jpg');
        }

        foreach ($data as $key => $value) {
            DB::table('setting')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // Sync wa_norek dengan info_pembayaran
        if (isset($data['info_pembayaran'])) {
            DB::table('setting')->updateOrInsert(
                ['key' => 'wa_norek'],
                ['value' => $data['info_pembayaran'], 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function deleteQris()
    {
        $path = public_path("images/payment/qris.jpg");
        if (file_exists($path)) unlink($path);
        DB::table("setting")->where("key", "wa_qris_url")->update(["value" => null]);
        return back()->with("success", "QRIS berhasil dihapus!");
    }
}
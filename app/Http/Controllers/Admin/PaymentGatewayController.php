<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    protected array $gateways = ['duitku', 'midtrans', 'xendit', 'tripay', 'manual'];

    public function index()
    {
        $settings = [];
        foreach ($this->gateways as $gw) {
            $settings[$gw] = PaymentSetting::getGateway($gw);
        }
        return view('admin.setting.payment-gateway', compact('settings'));
    }

    public function update(Request $request, string $gateway)
    {
        if (!in_array($gateway, $this->gateways)) abort(404);

        $data = $request->except(['_token', '_method']);
        $data['is_active'] = $request->input('is_active', '0');

        PaymentSetting::setGateway($gateway, $data);

        $nama = [
            'duitku'   => 'Duitku',
            'midtrans' => 'Midtrans',
            'xendit'   => 'Xendit',
            'tripay'   => 'Tripay',
            'manual'   => 'Transfer Manual',
        ];

        return back()->with('success', 'Pengaturan ' . ($nama[$gateway] ?? $gateway) . ' berhasil disimpan!');
    }
}

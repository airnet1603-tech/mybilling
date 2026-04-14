<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Olt;
use App\Models\Odp;
use App\Models\Onu;
use App\Models\Sfp;
use Illuminate\Http\Request;

class TopologiController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────
    public function index()
    {
        $olts = Olt::withCount(['odps', 'onus'])->get();
        $odcs = Odp::where('type', 'ODC')->get();
        $odps = Odp::where('type', 'ODP')->get();
        $sfps = Sfp::with('olt')->get();
        return view('admin.topologi.index', compact('olts', 'odcs', 'odps', 'sfps'));
    }

    // ─── OLT CRUD ─────────────────────────────────────────
    public function editOlt($id)
    {
        $olt = Olt::findOrFail($id);
        return view('admin.topologi.olt-edit', compact('olt'));
    }

    public function updateOlt(Request $request, $id)
    {
        $olt = Olt::findOrFail($id);
        $request->validate([
            'name'       => 'required',
            'ip_address' => 'required',
            'username'   => 'required',
            'password'   => 'required',
        ]);
        $olt->update($request->only(['name','ip_address','username','password','snmp_community','hsgq_key','api_endpoint','sync_interval','lat','lng','model','olt_color','olt_icon','odc_color','odc_icon','odp_color','odp_icon','line_olt_odc','line_odc_odp','line_odp_odp']));
        return redirect('/admin/topologi')->with('success', 'OLT berhasil diupdate!');
    }

    public function destroyOlt($id)
    {
        Olt::findOrFail($id)->delete();
        return redirect('/admin/topologi')->with('success', 'OLT berhasil dihapus!');
    }

    public function createOlt()
    {
        return view('admin.topologi.olt-create');
    }

    public function storeOlt(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'ip_address' => 'required',
            'username'   => 'required',
            'password'   => 'required',
        ]);
        Olt::create($request->only(['name','ip_address','username','password','snmp_community','hsgq_key','api_endpoint','sync_interval','lat','lng','model']));
        return redirect('/admin/topologi')->with('success', 'OLT berhasil ditambahkan!');
    }

    public function showOlt($id)
    {
        $olt  = Olt::findOrFail($id);
        $odps = Odp::where('olt_id', $id)->get();
        $onus = Onu::where('olt_id', $id)->with('pelanggan')->get();
        return view('admin.topologi.show', compact('olt', 'odps', 'onus'));
    }

    // ─── ODC CRUD ─────────────────────────────────────────
    public function createOdc()
    {
        $olts = Olt::all();
        $sfps = Sfp::with('olt')->get();
        return view('admin.topologi.odc-create', compact('olts', 'sfps'));
    }

    public function storeOdc(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
        ]);
        Odp::create([
            'name'       => $request->name,
            'type'       => 'ODC',
            'olt_id'     => $request->olt_id,
            'sfp_id'     => $request->sfp_id ?: null,
            'lat'        => $request->lat,
            'lng'        => $request->lng,
            'kapasitas'  => $request->kapasitas ?? 16,
            'keterangan' => $request->keterangan,
        ]);
        return redirect('/admin/topologi')->with('success', 'ODC berhasil ditambahkan!');
    }

    public function editOdc($id)
    {
        $odc  = Odp::where('type', 'ODC')->findOrFail($id);
        $olts = Olt::all();
        $sfps = Sfp::with('olt')->get();
        return view('admin.topologi.odc-edit', compact('odc', 'olts', 'sfps'));
    }

    public function updateOdc(Request $request, $id)
    {
        $odc = Odp::where('type', 'ODC')->findOrFail($id);
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
        ]);
        $odc->update([
            'name'       => $request->name,
            'olt_id'     => $request->olt_id,
            'sfp_id'     => $request->sfp_id ?: null,
            'lat'        => $request->lat,
            'lng'        => $request->lng,
            'kapasitas'  => $request->kapasitas ?? 16,
            'keterangan' => $request->keterangan,
        ]);
        return redirect('/admin/topologi')->with('success', 'ODC berhasil diupdate!');
    }

    public function destroyOdc($id)
    {
        Odp::where('type', 'ODC')->findOrFail($id)->delete();
        return redirect('/admin/topologi')->with('success', 'ODC berhasil dihapus!');
    }

    // ─── API & PETA ───────────────────────────────────────
    public function apiNodes()
    {
        $olts = Olt::all()->map(fn($o) => [
            'id'   => 'olt-'.$o->id,
            'type' => 'OLT',
            'name' => $o->name,
            'lat'  => $o->lat,
            'lng'  => $o->lng,
            'ip'   => $o->ip_address,
        ]);

        $allOdps = Odp::all();

        $oltMap = \App\Models\Olt::all()->keyBy('id');
        $odcs = $allOdps->where('type', 'ODC')->map(function($o) use ($oltMap) {
            $olt = $oltMap->get($o->olt_id);
            return [
                'id'     => 'odc-'.$o->id,
                'type'   => 'ODC',
                'name'   => $o->name,
                'lat'    => $o->lat,
                'lng'    => $o->lng,
                'olt_id' => 'olt-'.$o->olt_id,
                'color'  => $olt ? ($olt->odc_color ?? '#6f42c1') : '#6f42c1',
                'icon'   => $olt ? ($olt->odc_icon  ?? 'dot')     : 'dot',
                'line_color' => $olt ? ($olt->line_olt_odc ?? '#6f42c1') : '#6f42c1',
            ];
        });

        $odps = $allOdps->where('type', 'ODP')->map(function($o) use ($oltMap) {
            $olt = $oltMap->get($o->olt_id);
            return [
                'id'            => 'odp-'.$o->id,
                'type'          => 'ODP',
                'name'          => $o->name,
                'lat'           => $o->lat,
                'lng'           => $o->lng,
                'kapasitas'     => $o->kapasitas,
                'keterangan'    => $o->keterangan,
                'olt_id'        => 'olt-'.$o->olt_id,
                'odc_id'        => $o->odc_id ? 'odc-'.$o->odc_id : null,
                'parent_odp_id' => $o->parent_odp_id ? 'odp-'.$o->parent_odp_id : null,
                'color'         => $olt ? ($olt->odp_color ?? '#fd7e14') : '#fd7e14',
                'icon'          => $olt ? ($olt->odp_icon  ?? 'dot')     : 'dot',
                'line_color'    => $olt ? ($olt->line_odc_odp ?? '#fd7e14') : '#fd7e14',
                'line_color_odp'=> $olt ? ($olt->line_odp_odp ?? '#28a745') : '#28a745',
            ];
        });

        $onus = Onu::with('pelanggan')->get()->map(fn($o) => [
            'id'        => 'onu-'.$o->id,
            'type'      => 'ONT',
            'name'      => $o->name ?? $o->onu_id,
            'mac'       => $o->mac_address,
            'status'    => $o->status,
            'odp_id'    => $o->odp_id ? 'odp-'.$o->odp_id : null,
            'pelanggan' => $o->pelanggan?->nama ?? null,
            'lat'       => $o->pelanggan?->lat ?? null,
            'lng'       => $o->pelanggan?->lng ?? null,
        ]);

        return response()->json([
            'olts' => $olts->values(),
            'odcs' => $odcs->values(),
            'odps' => $odps->values(),
            'onus' => $onus->values(),
        ]);
    }

    public function petaTopologi()
    {
        $olts = \App\Models\Olt::withCount([
            'onus',
            'onus as onus_up_count'   => fn($q) => $q->where('status', 'Up'),
            'onus as onus_down_count' => fn($q) => $q->where('status', 'Down'),
            'odps',
        ])->get()->map(fn($o) => [
            'id'         => $o->id,
            'name'       => $o->name,
            'ip_address' => $o->ip_address,
            'model'      => $o->model,
            'lat'        => $o->lat,
            'lng'        => $o->lng,
            'odp_count'  => $o->odps_count,
            'onu_total'  => $o->onus_count,
            'onu_up'     => $o->onus_up_count,
            'onu_down'   => $o->onus_down_count,
        ]);

        $allOdps = \App\Models\Odp::with('olt')->get();

        $odcData = $allOdps->where('type', 'ODC')->map(fn($o) => [
            'id'         => $o->id,
            'name'       => $o->name,
            'type'       => 'ODC',
            'kapasitas'  => $o->kapasitas,
            'keterangan' => $o->keterangan,
            'olt_id'     => $o->olt_id,
            'lat'        => $o->lat,
            'lng'        => $o->lng,
        ])->values();

        $odpData = $allOdps->where('type', 'ODP')->map(fn($o) => [
            'id'         => $o->id,
            'name'       => $o->name,
            'type'       => 'ODP',
            'kapasitas'  => $o->kapasitas,
            'keterangan' => $o->keterangan,
            'olt_id'     => $o->olt_id,
            'odc_id'     => $o->odc_id,
            'lat'        => $o->lat,
            'lng'        => $o->lng,
        ])->values();

        return view('admin.topologi.peta', [
            'oltData' => $olts,
            'odcData' => $odcData,
            'odpData' => $odpData,
        ]);
    }

    public function fetchHsgqKey($id)
    {
        $olt = Olt::findOrFail($id);
        if (strtolower($olt->model) !== 'hsgq') {
            return response()->json(['success' => false, 'error' => 'Hanya untuk model HSGQ']);
        }
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => 'http://'.$olt->ip_address, 'timeout' => 10, 'verify' => false]);
            $loginRes = $client->post('/userlogin?form=login', [
                'headers' => ['Content-Type' => 'application/json', 'X-Token' => 'null'],
                'json'    => ['method' => 'set', 'param' => ['captcha_f' => '', 'captcha_v' => '', 'key' => md5(md5($olt->password)), 'name' => $olt->username, 'value' => base64_encode($olt->password)]],
            ]);
            $xtoken    = $loginRes->getHeader('X-Token')[0] ?? null;
            $loginData = json_decode((string) $loginRes->getBody(), true);
            if (($loginData['code'] ?? 0) == 1 && $xtoken) {
                $olt->hsgq_key = md5(md5($olt->password));
                $olt->save();
                return response()->json(['success' => true, 'key' => $olt->hsgq_key, 'message' => 'Key berhasil disimpan!']);
            }
            return response()->json(['success' => false, 'error' => 'Login gagal.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function syncOnu($olt_id)
    {
        $olt = Olt::findOrFail($olt_id);
        try {
            $baseUri = 'http://'.$olt->ip_address;
            $model   = strtolower($olt->model ?? '');
            $synced  = 0;
            if ($model === 'hsgq') {
                $client   = new \GuzzleHttp\Client(['base_uri' => $baseUri, 'timeout' => 15, 'verify' => false]);
                $loginRes = $client->post('/userlogin?form=login', [
                    'headers' => ['Content-Type' => 'application/json', 'X-Token' => 'null'],
                    'json'    => ['method' => 'set', 'param' => ['captcha_f' => '', 'captcha_v' => '', 'key' => $olt->hsgq_key ?? md5(md5($olt->password)), 'name' => $olt->username, 'value' => base64_encode($olt->password)]],
                ]);
                $xtoken    = $loginRes->getHeader('X-Token')[0] ?? null;
                $loginData = json_decode((string) $loginRes->getBody(), true);
                if (($loginData['code'] ?? 0) != 1 || !$xtoken) {
                    return response()->json(['success' => false, 'error' => 'Login HSGQ gagal']);
                }
                for ($port = 1; $port <= 4; $port++) {
                    $res  = $client->get('/onu_allow_list', ['query' => ['port_id' => $port, 't' => round(microtime(true)*1000)], 'headers' => ['X-Token' => $xtoken]]);
                    foreach (json_decode((string)$res->getBody(), true)['data'] ?? [] as $onu) {
                        Onu::updateOrCreate(
                            ['onu_id' => ($onu['port_id'] ?? $port).'/'.(($onu['onu_id'] ?? 0)), 'olt_id' => $olt_id],
                            ['name' => $onu['onu_name'] ?? '', 'mac_address' => $onu['macaddr'] ?? '', 'status' => ($onu['status'] ?? '') === 'Online' ? 'Up' : 'Down']
                        );
                        $synced++;
                    }
                }
            } else {
                $client = new \GuzzleHttp\Client(['base_uri' => $baseUri, 'timeout' => 15, 'verify' => false, 'auth' => [$olt->username, $olt->password]]);
                $html   = (string) $client->get($olt->api_endpoint ?? '/onuAllPonOnuList.asp')->getBody();
                preg_match_all("/'([\d\/\:]+)','([^']*)','([0-9a-fA-F:]+)','(Up|Down)'/", $html, $m);
                foreach ($m[1] as $i => $onuId) {
                    Onu::updateOrCreate(['onu_id' => $onuId, 'olt_id' => $olt_id], ['name' => $m[2][$i], 'mac_address' => $m[3][$i], 'status' => $m[4][$i]]);
                    $synced++;
                }
            }
            return response()->json(['success' => true, 'synced' => $synced]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // ─── ODP CRUD ─────────────────────────────────────────
    public function createOdp()
    {
        $olts = Olt::all();
        $odcs = Odp::where('type', 'ODC')->get();
        $odps = Odp::where('type', 'ODP')->get();
        $sfps = Sfp::with('olt')->get();
        return view('admin.topologi.odp-create', compact('olts', 'odcs', 'odps', 'sfps'));
    }
    public function storeOdp(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
        ]);
        Odp::create([
            'name'       => $request->name,
            'type'       => 'ODP',
            'olt_id'     => $request->olt_id,
            'sfp_id'        => $request->sfp_id ?: null,
            'odc_id'        => $request->odc_id ?: null,
            'parent_odp_id' => $request->parent_odp_id ?: null,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'kapasitas'     => $request->kapasitas ?? 8,
            'keterangan'    => $request->keterangan,
        ]);
        return redirect('/admin/topologi')->with('success', 'ODP berhasil ditambahkan!');
    }
    public function editOdp($id)
    {
        $odp  = Odp::where('type', 'ODP')->findOrFail($id);
        $olts = Olt::all();
        $odcs = Odp::where('type', 'ODC')->get();
        $sfps = Sfp::with('olt')->get();
        return view('admin.topologi.odp-edit', compact('odp', 'olts', 'odcs', 'sfps'));
    }
    public function updateOdp(Request $request, $id)
    {
        $odp = Odp::where('type', 'ODP')->findOrFail($id);
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
        ]);
        $odp->update([
            'name'       => $request->name,
            'olt_id'     => $request->olt_id,
            'sfp_id'        => $request->sfp_id ?: null,
            'odc_id'        => $request->odc_id ?: null,
            'parent_odp_id' => $request->parent_odp_id ?: null,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'kapasitas'     => $request->kapasitas ?? 8,
            'keterangan'    => $request->keterangan,
        ]);
        return redirect('/admin/topologi')->with('success', 'ODP berhasil diupdate!');
    }
    public function destroyOdp($id)
    {
        Odp::where('type', 'ODP')->findOrFail($id)->delete();
        return redirect('/admin/topologi')->with('success', 'ODP berhasil dihapus!');
    }

    // ─── SFP CRUD ─────────────────────────────────────────
    public function createSfp()
    {
        $olts = Olt::all();
        return view('admin.topologi.sfp-create', compact('olts'));
    }

    public function storeSfp(Request $request)
    {
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
        ]);
        Sfp::create($request->only(['name','olt_id','port','keterangan','lat','lng','color','icon']));
        return redirect('/admin/topologi')->with('success', 'SFP berhasil ditambahkan!');
    }

    public function editSfp($id)
    {
        $sfp  = Sfp::findOrFail($id);
        $olts = Olt::all();
        return view('admin.topologi.sfp-edit', compact('sfp', 'olts'));
    }

    public function updateSfp(Request $request, $id)
    {
        $sfp = Sfp::findOrFail($id);
        $request->validate([
            'name'   => 'required',
            'olt_id' => 'required|exists:olts,id',
        ]);
        $sfp->update($request->only(['name','olt_id','port','keterangan','lat','lng','color','icon']));
        return redirect('/admin/topologi')->with('success', 'SFP berhasil diupdate!');
    }

    public function destroySfp($id)
    {
        Sfp::findOrFail($id)->delete();
        return redirect('/admin/topologi')->with('success', 'SFP berhasil dihapus!');
    }

    public function apiSfpByOlt($olt_id)
    {
        $sfps = Sfp::where('olt_id', $olt_id)->get();
        return response()->json($sfps);
    }

}
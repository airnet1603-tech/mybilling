<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Olt;
use App\Models\Odp;
use App\Models\Onu;
use Illuminate\Http\Request;

class TopologiController extends Controller
{
    public function index()
    {
        $olts = Olt::withCount(['odps', 'onus'])->get();
        return view('admin.topologi.index', compact('olts'));
    }



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
        $olt->update($request->only(['name','ip_address','username','password','snmp_community','api_endpoint','sync_interval','lat','lng','model']));
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

        Olt::create($request->only(['name','ip_address','username','password','snmp_community','api_endpoint','sync_interval','lat','lng','model']));

        return redirect('/admin/topologi')->with('success', 'OLT berhasil ditambahkan!');
    }
    public function showOlt($id)
    {
        $olt  = Olt::findOrFail($id);
        $odps = Odp::where('olt_id', $id)->get();
        $onus = Onu::where('olt_id', $id)->with('pelanggan')->get();
        return view('admin.topologi.show', compact('olt', 'odps', 'onus'));
    }

    // API untuk Google Maps
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

        $odps = Odp::all()->map(fn($o) => [
            'id'      => 'odp-'.$o->id,
            'type'    => $o->type,
            'name'    => $o->name,
            'lat'     => $o->lat,
            'lng'     => $o->lng,
            'olt_id'  => 'olt-'.$o->olt_id,
        ]);

        $onus = Onu::with('pelanggan')->get()->map(fn($o) => [
            'id'       => 'onu-'.$o->id,
            'type'     => 'ONT',
            'name'     => $o->name ?? $o->onu_id,
            'mac'      => $o->mac_address,
            'status'   => $o->status,
            'odp_id'   => $o->odp_id ? 'odp-'.$o->odp_id : null,
            'pelanggan'=> $o->pelanggan?->nama ?? null,
            'lat'      => $o->pelanggan?->lat ?? null,
            'lng'      => $o->pelanggan?->lng ?? null,
        ]);

        return response()->json([
            'olts' => $olts,
            'odps' => $odps,
            'onus' => $onus,
        ]);
    }

    // Sync ONU dari HisFocus via HTTP scraping
    public function syncOnu($olt_id)
    {
        $olt = Olt::findOrFail($olt_id);

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => 'http://'.$olt->ip_address,
                'timeout'  => 15,
                'verify'   => false,
                'auth'     => [$olt->username, $olt->password],
            ]);

            // Ambil data ONU dari onuOverview.asp
            // Gunakan api_endpoint dari setting OLT
            $endpoint = $olt->api_endpoint ?? '/onuAllPonOnuList.asp';
            $res  = $client->get($endpoint);
            $html = (string) $res->getBody();

            // Parse array JS: '0/1/1:1','name','mac','Up','fw','chip','port'
            preg_match_all("/'([\d\/\:]+)','([^']*)','([0-9a-fA-F:]+)','(Up|Down)'/", $html, $m);
            $synced = 0;
            foreach ($m[1] as $i => $onuId) {
                Onu::updateOrCreate(
                    ['onu_id' => $onuId, 'olt_id' => $olt_id],
                    [
                        'name'        => $m[2][$i],
                        'mac_address' => $m[3][$i],
                        'status'      => $m[4][$i],
                    ]
                );
                $synced++;
            }

            return response()->json(['success' => true, 'synced' => $synced]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

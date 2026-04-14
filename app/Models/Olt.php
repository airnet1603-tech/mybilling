<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Olt extends Model {
    protected $fillable = [
        'name','ip_address','username','password','snmp_community',
        'hsgq_key','api_endpoint','sync_interval','lat','lng','model','is_active',
        'olt_color','olt_icon',
        'sfp_color','sfp_icon',
        'odc_color','odc_icon',
        'odp_color','odp_icon',
        'line_olt_odc','line_odc_odp','line_odp_odp',
    ];
    public function odps() { return $this->hasMany(Odp::class); }
    public function sfps() { return $this->hasMany(Sfp::class); }
    public function onus() { return $this->hasMany(Onu::class); }
}

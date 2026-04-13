<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Odp extends Model {
    protected $fillable = ['name','type','lat','lng','olt_id','odc_id', 'parent_odp_id','kapasitas','keterangan'];

    public function olt()  { return $this->belongsTo(Olt::class); }
    public function sfp()  { return $this->belongsTo(Sfp::class); }
    public function onus() { return $this->hasMany(Onu::class); }

    // ODP belongsTo ODC (self-referential)
    public function odc()  { return $this->belongsTo(Odp::class, 'odc_id'); }

    // ODC hasMany ODP (self-referential)
    public function odps() { return $this->hasMany(Odp::class, 'odc_id'); }
}

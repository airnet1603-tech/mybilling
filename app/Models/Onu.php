<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Onu extends Model {
    protected $fillable = ['onu_id','name','mac_address','status','distance','port','olt_id','odp_id','pelanggan_id'];
    public function olt()       { return $this->belongsTo(Olt::class); }
    public function odp()       { return $this->belongsTo(Odp::class); }
    public function pelanggan() { return $this->belongsTo(Pelanggan::class); }
}

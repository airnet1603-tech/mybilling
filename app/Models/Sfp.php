<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Sfp extends Model {
    protected $fillable = ['olt_id','name','port','keterangan','lat','lng'];
    public function olt()  { return $this->belongsTo(Olt::class); }
    public function odcs() { return $this->hasMany(Odp::class)->where('type','ODC'); }
    public function odps() { return $this->hasMany(Odp::class)->where('type','ODP'); }
}

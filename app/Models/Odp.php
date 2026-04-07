<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Odp extends Model {
    protected $fillable = ['name','type','lat','lng','olt_id','kapasitas','keterangan'];
    public function olt() { return $this->belongsTo(Olt::class); }
    public function onus() { return $this->hasMany(Onu::class); }
}

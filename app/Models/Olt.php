<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Olt extends Model {
    protected $fillable = ['name','ip_address','username','password','lat','lng','model','is_active'];
    public function odps() { return $this->hasMany(Odp::class); }
    public function onus() { return $this->hasMany(Onu::class); }
}

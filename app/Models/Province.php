<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    
    protected $fillable = [
        'region_code',
        'province_code',
        'province',
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'province_code', 'province_code');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'province_code');
    }
}

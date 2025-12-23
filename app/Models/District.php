<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'province_code',
        'city_code',
        'district_code',
        'district',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'city_code');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }
}

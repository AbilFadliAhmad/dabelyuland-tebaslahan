<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyHighlight extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'property_id';
    public $incrementing = false;

    // Untuk memproses data Spatial Point
    protected $casts = [
        'pushed_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        // Gunakan belongsTo karena foreign key (user_id) ada di tabel ini
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function mainImage()
    {
        /**
         * Parameter 1: Nama Model tujuan.
         * Parameter 2: Foreign Key di tabel tujuan (gallery_properties).
         * Parameter 3: Local Key di tabel saat ini (property_highlights).
         */
        return $this->hasOne(GalleryProperty::class, 'property_id', 'property_id')
                    ->where('sort', 1);
    }

    // Scope untuk mencari properti terdekat berdasarkan spasial Point
    public function scopeTerdekat($query, $lat, $lng, $radius)
    {
        // MySQL ST_Distance_Sphere menggunakan POINT(longitude, latitude)
        return $query->whereRaw("ST_Distance_Sphere(location, POINT(?, ?)) <= ?", [$lng, $lat, $radius])
                     ->select('*')
                     ->selectRaw("ST_Distance_Sphere(location, POINT(?, ?)) as distance", [$lng, $lat])
                     ->orderBy('distance', 'asc');
    }
}

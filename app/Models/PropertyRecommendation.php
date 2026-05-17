<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyRecommendation extends Model
{
    protected $guarded = [];

    // Beritahu Laravel bahwa Primary Key-nya adalah property_id
    protected $primaryKey = 'property_id';

    // Karena property_id bukan auto-increment (diambil dari tabel properties)
    public $incrementing = false;

    // Relasi ke User (Agen)
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
}
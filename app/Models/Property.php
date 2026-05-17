<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'harga',
        'tipe',
        'kategori',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_kamar_tidur',
        'jumlah_kamar_mandi',
        'kota',
        'alamat_detail',
        'latitude',
        'longitude',
        'user_id',
        'session_id',
        'status',
        'tipe',
        'kategori',
        'transaksi',
        'is_sewa',
        'is_terjual',
        'is_tersedia',
        'legalitas',
    ];

    // Baris ini bertugas menjadi cadangan konversi jika seandainya library database mengembalikan string
    // Baris casts sendiri berfungsi untuk mengubah tipe data dari database
    protected $casts = [
        'harga' => 'integer',
        'luas_tanah' => 'integer',
        'luas_bangunan' => 'integer',
        'jumlah_kamar_tidur' => 'integer',
        'jumlah_kamar_mandi' => 'integer',
        'is_sewa' => 'boolean',
        'is_terjual' => 'boolean',
        'user_id' => 'integer',
        // 'latitude' => 'decimal:8',
        // 'longitude' => 'decimal:8',
    ];

    protected $attributes = [
        'latitude' => -7.5461,
        'longitude' => 112.2331,
    ];

    protected $hidden = [
        'location'
    ];

    // Kita gunakan boot method untuk memastikan 'location' terisi otomatis saat saving
    // protected static function booted()
    // {
    //     static::creating(function ($property) {
    //         if (is_null($property->location)) {
    //             $lat = $property->latitude ?? -7.5461;
    //             $lng = $property->longitude ?? 112.2331;
                
    //             $property->location = DB::raw("ST_GeomFromText('POINT($lng $lat)')");
    //         }
    //     });
    // }

    public function scopeTerdekat(Builder $query, $latitude, $longitude, $radiusInMeters = 1000000)
    {
        // Urutan POINT di MySQL: Longitude dulu baru Latitude
        $point = "POINT($longitude $latitude)";

        return $query->select('*')
            // Hitung jarak dalam meter menggunakan ST_Distance_Sphere
            ->selectRaw("ST_Distance_Sphere(location, ST_GeomFromText(?)) as jarak_meter", [$point])
            // Filter berdasarkan radius
            ->whereRaw("ST_Distance_Sphere(location, ST_GeomFromText(?)) <= ?", [$point, $radiusInMeters])
            // Urutkan dari yang paling dekat
            ->orderBy('jarak_meter', 'asc');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke GaleriProperti
    public function galleries()
    {
        return $this->hasMany(GalleryProperty::class, 'property_id');
    }

    // Relasi ke MainImage
    public function mainImage()
    {
        return $this->hasOne(GalleryProperty::class, 'property_id')->where('sort', 1);
    }

    // // Relasi ke Kota
    // public function kota()
    // {
    //     return $this->belongsTo(City::class, 'kota_id');
    // }

    // // Relasi ke Kecamatan
    // public function kecamatan()
    // {
    //     return $this->belongsTo(Subdistrict::class, 'kecamatan_id');
    // }
}

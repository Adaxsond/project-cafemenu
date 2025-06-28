<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Pastikan 'category_id' ada di sini agar bisa diisi dari form admin.
     */
    protected $fillable = [
        'category_id', 
        'name', 
        'description', 
        'price', 
        'image_path'
    ];

    /**
     * Mendefinisikan bahwa satu Produk dimiliki oleh satu Kategori.
     * Nama fungsi 'category' ini harus sama persis.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
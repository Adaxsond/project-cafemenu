<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Mendefinisikan bahwa satu Kategori memiliki banyak Produk.
     * Nama fungsi 'products' ini harus sama persis.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
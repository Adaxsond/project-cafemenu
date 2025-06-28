<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- Pastikan ini ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['table_number', 'total_price', 'status'];
    protected $casts = ['status' => OrderStatus::class];

    // Tambahkan $appends agar accessor bisa diakses di JSON / event
    protected $appends = ['status_label', 'status_color'];

    public function items() { return $this->hasMany(OrderItem::class); }

    // Accessor untuk label status
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }
    
    // Accessor untuk warna status
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
}
<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk Dashboard Admin
Broadcast::channel('admin-channel', function ($user) {
    return $user != null;
});

// Channel BARU untuk status order per pesanan
Broadcast::channel('order.{order}', function ($user, Order $order) {
    // Untuk demo, kita izinkan siapa saja.
    // Di aplikasi nyata, ini butuh token atau session pelanggan.
    return true;
});
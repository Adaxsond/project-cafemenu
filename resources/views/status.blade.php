<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Pesanan #{{ $order->id }} - PMO</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f4f9; color: #333; }
        .container { max-width: 600px; margin: 2em auto; background: white; padding: 2em; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { text-align: center; }
        .success-icon { font-size: 4em; color: #28a745; line-height: 1; }
        h1 { margin-top: 0.2em; }
        
        .order-details { display: grid; grid-template-columns: 1fr 1fr; gap: 1em; text-align: left; padding: 1.5em; background: #fafafa; border-radius: 8px; margin: 2em 0; }
        .detail-item strong { display: block; color: #555; font-size: 0.9em; }
        .detail-item span { font-size: 1.1em; font-weight: bold; }
        
        .status-box { margin-bottom: 2em; padding: 1em; border-radius: 8px; font-size: 1.5em; font-weight: bold; transition: all 0.3s; text-align: center; }
        
        .summary h4 { text-align: left; margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 0.5em; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 0.8em; }
        .summary-total { display: flex; justify-content: space-between; font-size: 1.2em; font-weight: bold; margin-top: 1em; border-top: 2px solid #333; padding-top: 1em; }
        
        .btn-back { display: inline-block; padding: 0.8em 2em; background: #555; color: white; text-decoration: none; border-radius: 8px; margin-top: 2em; }
        
        .status { padding: 0.2em 0.6em; border-radius: 12px; font-size: 0.8em; color: #333; }
        .bg-yellow-200 { background-color: #fef08a; }
        .bg-blue-200 { background-color: #bfdbfe; }
        .bg-green-200 { background-color: #bbf7d0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Pesanan Diterima!</h1>
        </div>

        <div class="order-details">
            <div class="detail-item">
                <strong>ID Pesanan</strong>
                <span>#{{ $order->id }}</span>
            </div>
            <div class="detail-item">
                <strong>Nomor Meja</strong>
                <span>{{ $order->table_number }}</span>
            </div>
            <div class="detail-item">
                <strong>Waktu Pesan</strong>
                <span>{{ $order->created_at->format('H:i') }}</span>
            </div>
             <div class="detail-item">
                <strong>Tanggal</strong>
                <span>{{ $order->created_at->format('d M Y') }}</span>
            </div>
        </div>

        <div id="status-container" class="status-box {{ $order->status->color() }}">
            Status: <span id="order-status-text">{{ $order->status->label() }}</span>
        </div>

        <div class="summary">
            <h4>Ringkasan Pesanan:</h4>
            @foreach($order->items as $item)
                <div class="summary-item">
                    <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                    <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="summary-total">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
             <a href="{{ route('order.index', ['tableNumber' => $order->table_number]) }}" class="btn-back">Kembali ke Menu</a>
        </div>
    </div>
    
    <script type="module">
        // JavaScript untuk real-time tidak perlu diubah
        if (window.Echo) {
            window.Echo.private('order.{{ $order->id }}')
                .listen('OrderStatusUpdated', (e) => {
                    console.log('Status pesanan diperbarui:', e.order);
                    
                    const statusText = document.getElementById('order-status-text');
                    const statusContainer = document.getElementById('status-container');
                    
                    statusText.innerText = e.order.status_label;
                    statusContainer.className = `status-box ${e.order.status_color}`;
                });
        }
    </script>
</body>
</html>
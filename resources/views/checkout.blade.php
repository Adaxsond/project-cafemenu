<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran - Meja {{ $tableNumber }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f4f9; color: #333; }
        .container { max-width: 600px; margin: 2em auto; background: white; padding: 2em; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 0.5em; }
        .order-summary { border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 1.5em 0; margin: 1.5em 0; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 1em; }
        .item-details strong { font-size: 1.1em; }
        .item-details small { color: #777; font-style: italic; }
        .item-price { font-weight: bold; }
        .summary-total { display: flex; justify-content: space-between; font-size: 1.2em; font-weight: bold; margin-top: 1.5em; }
        .btn-confirm { display: block; width: 100%; padding: 1em; background: orange; color: white; text-align: center; border: none; border-radius: 8px; font-size: 1.2em; margin-top: 2em; cursor: pointer; }
        .btn-confirm:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Konfirmasi & Pembayaran</h1>
        <p style="text-align: center; margin-top: -1em; color: #666;">Pesanan untuk Meja {{ $tableNumber }}</p>

        <div class="order-summary">
            <div id="summary-items"></div>
            <div class="summary-total">
                <span>Total Bayar:</span>
                <span id="total-price">Rp 0</span>
            </div>
        </div>
        
        <button id="confirm-order-btn" class="btn-confirm">Konfirmasi & Buat Pesanan</button>
    </div>

    <script type="module">
        const cart = {!! json_encode($cart) !!};

        function renderSummary() {
            const container = document.getElementById('summary-items');
            const totalEl = document.getElementById('total-price');
            let totalPrice = 0;
            container.innerHTML = '';
            cart.forEach(item => {
                const itemTotalPrice = item.price * item.quantity;
                totalPrice += itemTotalPrice;
                container.insertAdjacentHTML('beforeend', `<div class="summary-item"><div class="item-details"><strong>${item.name} (x${item.quantity})</strong>${item.notes ? `<small>Catatan: ${item.notes}</small>` : ''}</div><span class="item-price">Rp ${itemTotalPrice.toLocaleString('id-ID')}</span></div>`);
            });
            totalEl.innerText = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        }
        
        document.getElementById('confirm-order-btn').addEventListener('click', function() {
            this.disabled = true; this.innerText = 'Memproses...';
            fetch('{{ route("order.store") }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                body: JSON.stringify({table_number: '{{ $tableNumber }}', cart: JSON.stringify(cart)})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.order_id) {
                    localStorage.removeItem(`cart_meja_{{ $tableNumber }}`);
                    // Arahkan ke halaman status baru
                    window.location.href = `/order/${data.order_id}/status`;
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan.'));
                    this.disabled = false; this.innerText = 'Konfirmasi & Buat Pesanan';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                console.error(err);
                this.disabled = false; this.innerText = 'Konfirmasi & Buat Pesanan';
            });
        });
        
        document.addEventListener('DOMContentLoaded', renderSummary);
    </script>
</body>
</html>
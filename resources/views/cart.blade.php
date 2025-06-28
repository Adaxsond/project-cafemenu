<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Meja {{ $tableNumber }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f4f9; }
        .container { max-width: 800px; margin: 2em auto; background: white; padding: 1.5em; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .cart-item { display: flex; align-items: center; margin-bottom: 1.5em; border-bottom: 1px solid #eee; padding-bottom: 1.5em; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 1em; }
        .cart-item-details { flex-grow: 1; }
        .cart-item-details strong { font-size: 1.1em; }
        .cart-item-notes { font-size: 0.9em; color: #666; margin-top: 0.5em; }
        .item-actions { display: flex; align-items: center; }
        .qty-control { display: flex; align-items: center; margin-right: 1.5em; }
        .qty-btn { border: 1px solid #ccc; background: #fafafa; cursor: pointer; padding: 0.3em 0.8em; font-weight: bold; }
        .remove-btn { color: red; cursor: pointer; font-weight: bold; }
        .cart-footer { margin-top: 2em; border-top: 2px solid #333; padding-top: 1em; text-align: right; }
        .btn-checkout { display: inline-block; padding: 1em 2em; background: orange; color: white; text-align: center; border: none; border-radius: 8px; font-size: 1.1em; text-decoration: none; margin-top: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Keranjang Anda (Meja {{ $tableNumber }})</h1>
        <div id="cart-items-container">
            <p>Memuat keranjang...</p>
        </div>
        <div class="cart-footer">
            <h2>Total: <span id="total-price">Rp 0</span></h2>
            <a href="#" id="checkout-link" class="btn-checkout" style="display:none;">Lanjut ke Pembayaran</a>
        </div>
        <a href="{{ route('order.index', ['tableNumber' => $tableNumber]) }}" style="display: block; text-align: center; margin-top: 1em;">&larr; Kembali ke Menu</a>
    </div>

    <script type="module">
        const cartKey = `cart_meja_{{ $tableNumber }}`;
        let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const checkoutLink = document.getElementById('checkout-link');
            
            if (cart.length === 0) {
                container.innerHTML = '<p>Keranjang Anda kosong.</p>';
                checkoutLink.style.display = 'none';
                document.getElementById('total-price').innerText = 'Rp 0';
                return;
            }

            container.innerHTML = '';
            let totalPrice = 0;

            cart.forEach((item, index) => {
                totalPrice += item.price * item.quantity;
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                itemElement.innerHTML = `
                    <img src="${item.image_path ? '/storage/' + item.image_path : 'https://via.placeholder.com/80'}" alt="${item.name}">
                    <div class="cart-item-details">
                        <strong>${item.name}</strong>
                        <p>Rp ${Number(item.price).toLocaleString('id-ID')}</p>
                        ${item.notes ? `<p class="cart-item-notes">Catatan: ${item.notes}</p>` : ''}
                    </div>
                    <div class="item-actions">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                            <span style="padding: 0 1em;">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                        <span class="remove-btn" onclick="removeItem(${index})">Hapus</span>
                    </div>
                `;
                container.appendChild(itemElement);
            });

            document.getElementById('total-price').innerText = `Rp ${totalPrice.toLocaleString('id-ID')}`;
            checkoutLink.style.display = 'inline-block';
            checkoutLink.href = `{{ route('order.checkout') }}?tableNumber={{ $tableNumber }}&cart=${encodeURIComponent(JSON.stringify(cart))}`;
        }
        
        window.updateQuantity = function(index, amount) {
            cart[index].quantity += amount;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart();
            renderCart();
        }

        window.removeItem = function(index) {
            if(confirm('Anda yakin ingin menghapus item ini dari keranjang?')) {
                cart.splice(index, 1);
                saveCart();
                renderCart();
            }
        }

        function saveCart() {
            localStorage.setItem(cartKey, JSON.stringify(cart));
        }

        document.addEventListener('DOMContentLoaded', renderCart);
    </script>
</body>
</html>
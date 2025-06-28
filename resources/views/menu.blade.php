<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu PMO - Meja {{ $tableNumber }}</title>
    <style>
        :root {
            --primary-color: #f97316;
            --light-gray: #f3f4f6;
            --medium-gray: #d1d5db;
            --dark-gray: #4b5563;
        }
        html { scroll-behavior: smooth; }
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; margin: 0; background: #fff; }
        
        .site-header {
            position: sticky;
            top: 0;
            z-index: 950;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: padding 0.3s ease-in-out;
        }
        .header-top {
            transition: all 0.3s ease-in-out;
            max-height: 500px; 
            overflow: hidden;
        }
        .site-header.scrolled .header-top {
            max-height: 0;
            opacity: 0;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            visibility: hidden;
        }
        
        .header-banner { width: 100%; height: 150px; object-fit: cover; background-color: var(--light-gray); }
        .main-content { padding: 1em; }
        .store-card-wrapper {
             transform: translateY(-40px);
             padding: 0 1em;
             margin-bottom: -30px;
        }
        .store-card {
            background: white;
            border-radius: 12px;
            padding: 1em;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .store-card h1 { margin: 0; font-size: 1.5em; }
        .store-status { font-size: 0.9em; font-weight: bold; color: #16a34a; }
        .table-number { background: var(--light-gray); text-align: center; padding: 0.8em; border-radius: 8px; font-weight: bold; margin-top: 1em; }
        .category-nav { background: white; padding: 0.8em 0; }
        .site-header.scrolled .category-nav { border-top: 1px solid var(--light-gray); }
        .category-nav-scroll { display: flex; overflow-x: auto; white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none; padding: 0 1em; }
        .category-nav-scroll::-webkit-scrollbar { display: none; }
        .category-nav-item { display: inline-block; padding: 0.5em 1.2em; margin: 0 0.5em; border-radius: 20px; border: 2px solid transparent; text-decoration: none; color: var(--dark-gray); font-weight: bold; transition: all 0.2s; }
        .category-nav-item.active { border-color: var(--primary-color); color: var(--primary-color); }
        .menu-section { padding-top: 80px; margin-top: -80px; }
        .menu-section h2 { font-size: 1.4em; padding-top: 1em; }
        .product-grid { display: flex; overflow-x: auto; gap: 1em; padding: 0.5em 0 1.5em 0; -ms-overflow-style: none; scrollbar-width: none; }
        .product-grid::-webkit-scrollbar { display: none; }
        .product-card { flex-shrink: 0; width: 160px; display: flex; flex-direction: column; background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: pointer; overflow: hidden; border: 1px solid var(--light-gray); }
        .product-card img { width: 100%; height: 120px; object-fit: cover; }
        .product-info { padding: 0.8em; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; text-align: center; }
        .product-info strong { font-size: 1em; margin-bottom: 0.5em; min-height: 2.4em; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 25px; border-radius: 12px; max-width: 500px; width: 90%; text-align: center; position: relative; }
        .modal-close { position: absolute; top: 10px; right: 15px; font-size: 28px; font-weight: bold; cursor: pointer; }
        .qty-control { display: flex; align-items: center; justify-content: center; margin: 1em 0; }
        .qty-btn { font-size: 1.5em; padding: 0 15px; cursor: pointer; user-select: none; }
        #modalQty { font-size: 1.5em; padding: 0 20px; }
        #addToCartBtn { background: orange; color: white; padding: 1em; border: none; border-radius: 5px; width: 100%; font-size: 1em; font-weight: bold; cursor: pointer; }
        .cart-fab { position: fixed; bottom: 20px; right: 20px; background: var(--primary-color); color: white; min-width: 60px; height: 60px; border-radius: 30px; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); text-decoration: none; z-index: 999; padding: 0 20px; }
        .cart-count { margin-left: 8px; background: #dc2626; padding: 2px 8px; border-radius: 10px; font-size: 14px; }
        #scrollTopBtn { display: flex; align-items: center; justify-content: center; position: fixed; bottom: 90px; right: 20px; width: 45px; height: 45px; border-radius: 50%; background-color: var(--dark-gray); color: white; border: none; cursor: pointer; z-index: 998; opacity: 0; visibility: hidden; transform: translateY(20px); transition: opacity 0.3s, visibility 0.3s, transform 0.3s; }
        #scrollTopBtn.visible { opacity: 1; visibility: visible; transform: translateY(0); }
        .orders-section { margin-top: 2em; padding-top: 1em; border-top: 2px dashed var(--medium-gray); }
        .order-item-link { text-decoration: none; color: inherit; display: block; }
        .order-item { background: #fff; border: 1px solid var(--medium-gray); border-radius: 8px; padding: 1em; margin-bottom: 1em; display: flex; justify-content: space-between; align-items: center; }
        .status { padding: 0.3em 0.8em; border-radius: 15px; font-size: 0.9em; color: var(--dark-gray); }
        .bg-yellow-200 { background-color: #fef08a; } .bg-blue-200 { background-color: #bfdbfe; } .bg-green-200 { background-color: #bbf7d0; }
        details > summary { cursor: pointer; font-weight: bold; margin-bottom: 1em; }
    </style>
</head>
<body>
    <div id="page-top"></div>

    <header class="site-header">
        <div class="header-top">
            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop" alt="Restaurant Banner" class="header-banner">
            <div class="store-card-wrapper">
                 <div class="store-card">
                    <h1>PMO (Pemesanan Mandiri Otanpaberdiri)</h1>
                    <span class="store-status">BUKA</span>
                    <div class="table-number">Nomor Meja: {{ $tableNumber }}</div>
                </div>
            </div>
        </div>
        <nav class="category-nav">
            <div class="category-nav-scroll">
                @foreach($categories as $category)
                    <a href="#category-{{ $category->id }}" class="category-nav-item">{{ strtoupper($category->name) }}</a>
                @endforeach
            </div>
        </nav>
    </header>

    <div class="main-content">
        @forelse($categories as $category)
            <div class="menu-section" id="category-{{ $category->id }}">
                <h2>{{ $category->name }}</h2>
                <div class="product-grid">
                    @foreach($category->products as $product)
                    <div class="product-card" onclick='openModal(@json($product))'>
                        <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : 'https://via.placeholder.com/160x120' }}" alt="{{ $product->name }}">
                        <div class="product-info">
                            <strong>{{ $product->name }}</strong>
                            <p>Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p>Belum ada menu yang tersedia.</p>
        @endforelse

        <div class="orders-section">
            <h3>Pesanan Aktif Anda</h3>
            <div id="active-orders-list">
                @forelse($activeOrders as $order)
                    <a href="{{ route('order.status', $order) }}" class="order-item-link">
                        <div class="order-item" id="order-{{ $order->id }}">
                            <span>Order #{{ $order->id }}</span>
                            <span id="status-{{ $order->id }}" class="status {{ $order->status->color() }}">{{ $order->status->label() }}</span>
                        </div>
                    </a>
                @empty
                    <p id="no-active-orders-msg">Tidak ada pesanan yang sedang diproses.</p>
                @endforelse
            </div>
        </div>
        
        <div class="orders-section">
            <details>
                <summary><h3>Riwayat Pesanan</h3></summary>
                <div id="completed-orders-list">
                    @forelse($finishedOrders as $order)
                        <a href="{{ route('order.status', $order) }}" class="order-item-link">
                            <div class="order-item" id="order-{{ $order->id }}">
                                <span>Order #{{ $order->id }}</span>
                                <span id="status-{{ $order->id }}" class="status {{ $order->status->color() }}">{{ $order->status->label() }}</span>
                            </div>
                        </a>
                    @empty
                        <p id="no-completed-orders-msg">Tidak ada riwayat pesanan.</p>
                    @endforelse
                </div>
            </details>
        </div>
    </div>
    
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" style="width:100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1em;">
            <h2 id="modalName"></h2>
            <p id="modalPrice" style="font-weight: bold;"></p>
            <p>Catatan (Opsional)</p>
            <textarea id="modalNotes" placeholder="Contoh: Jangan pakai pedas" style="width: 90%; height: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
            <div class="qty-control">
                <span class="qty-btn" onclick="changeQty(-1)">-</span>
                <span id="modalQty">1</span>
                <span class="qty-btn" onclick="changeQty(1)">+</span>
            </div>
            <button id="addToCartBtn" class="btn-pay">Tambah ke Keranjang</button>
        </div>
    </div>
    
    <a href="{{ route('order.cart', ['tableNumber' => $tableNumber]) }}" class="cart-fab">
        🛒 Keranjang <span id="cartCount" class="cart-count">0</span>
    </a>
    <button id="scrollTopBtn" title="Kembali ke atas">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
    </button>
    
    <script type="module">
        // BAGIAN 1: FUNGSI-FUNGSI UTAMA
        const cartKey = `cart_meja_{{ $tableNumber }}`;
        let cart = JSON.parse(localStorage.getItem(cartKey)) || [];
        let currentProduct = null;
        let currentQty = 1;

        window.openModal = function(product) {
            currentProduct = product;
            currentQty = 1;
            document.getElementById('modalImg').src = product.image_path ? `/storage/${product.image_path}` : 'https://via.placeholder.com/200';
            document.getElementById('modalName').innerText = product.name;
            document.getElementById('modalPrice').innerText = `Rp ${Number(product.price).toLocaleString('id-ID')}`;
            document.getElementById('modalQty').innerText = currentQty;
            document.getElementById('modalNotes').value = '';
            document.getElementById('addToCartBtn').onclick = addToCart;
            document.getElementById('productModal').style.display = 'flex';
        }
        window.closeModal = function() { document.getElementById('productModal').style.display = 'none'; }
        window.changeQty = function(amount) {
            currentQty += amount;
            if (currentQty < 1) currentQty = 1;
            document.getElementById('modalQty').innerText = currentQty;
        }
        function addToCart() {
            const notes = document.getElementById('modalNotes').value.trim();
            const existingItem = cart.find(item => item.id === currentProduct.id && item.notes === notes);
            if (existingItem) { existingItem.quantity += currentQty; }
            else { cart.push({ ...currentProduct, quantity: currentQty, notes: notes }); }
            saveCart();
            closeModal();
            alert(`${currentQty} ${currentProduct.name} berhasil ditambahkan ke keranjang.`);
        }
        function saveCart() {
            localStorage.setItem(cartKey, JSON.stringify(cart));
            updateCartCount();
        }
        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartCount').innerText = totalItems;
        }

        // BAGIAN 2: LOGIKA INTERAKTIF HALAMAN
        document.addEventListener('DOMContentLoaded', () => {
            updateCartCount();
            const header = document.querySelector('.site-header');
            const scrollTopBtn = document.getElementById('scrollTopBtn');
            
            // Logika untuk header menyusut & tombol kembali ke atas
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) { header.classList.add('scrolled'); }
                else { header.classList.remove('scrolled'); }
                if (window.scrollY > 200) { scrollTopBtn.classList.add('visible'); }
                else { scrollTopBtn.classList.remove('visible'); }
            });

            scrollTopBtn.addEventListener('click', () => { window.scrollTo({ top: 0, behavior: 'smooth' }); });

            // Logika untuk navigasi kategori aktif saat scroll
            const navLinks = document.querySelectorAll('.category-nav-item');
            const sections = document.querySelectorAll('.menu-section');
            if (sections.length > 0) {
                const headerHeight = 70; // Tinggi header setelah menyusut
                navLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        const targetElement = document.querySelector(targetId);
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                    });
                });
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const id = entry.target.id;
                            navLinks.forEach(link => { link.classList.toggle('active', link.hash === `#${id}`); });
                        }
                    });
                }, { rootMargin: `-80px 0px -55% 0px` });
                sections.forEach(section => { observer.observe(section); });
            }
        });
    </script>
</body>
</html>
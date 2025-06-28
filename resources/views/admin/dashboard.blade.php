<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('success') }}</div>
                    @endif

                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Pesanan Aktif (Pending & Sedang Dibuat)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Order ID</th>
                                    <th class="py-2 px-4 border-b text-left">Meja</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Waktu</th>
                                    <th class="py-2 px-4 border-b text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pending-orders-tbody">
                                @forelse ($activeOrders as $order)
                                    <tr class="hover:bg-gray-50 border-b" id="order-row-{{ $order->id }}">
                                        <td class="py-2 px-4 border-b">#{{ $order->id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $order->table_number }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->status->color() }}">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $order->created_at->format('H:i') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Lihat Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-pending-orders">
                                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada pesanan aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-bold mt-8 mb-4">Pesanan Selesai (Terbaru)</h3>
                    <div class="overflow-x-auto">
                         <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Order ID</th>
                                    <th class="py-2 px-4 border-b text-left">Meja</th>
                                    <th class="py-2 px-4 border-b text-left">Total Harga</th>
                                    <th class="py-2 px-4 border-b text-left">Waktu Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($completedOrders as $order)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="py-2 px-4 border-b text-gray-500">#{{ $order->id }}</td>
                                        <td class="py-2 px-4 border-b text-gray-500">{{ $order->table_number }}</td>
                                        <td class="py-2 px-4 border-b text-gray-500">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b text-gray-500">{{ $order->updated_at->format('H:i, d M') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">Belum ada pesanan yang selesai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script type="module">
        const newOrderRowTemplate = (order) => `
            <tr class="hover:bg-gray-50 border-b" id="order-row-${order.id}">
                <td class="py-2 px-4 border-b">#${order.id}</td>
                <td class="py-2 px-4 border-b">${order.table_number}</td>
                <td class="py-2 px-4 border-b">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${order.status_color}">
                        ${order.status_label}
                    </span>
                </td>
                <td class="py-2 px-4 border-b">${new Date(order.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</td>
                <td class="py-2 px-4 border-b">
                    <a href="/admin/orders/${order.id}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Lihat Detail</a>
                </td>
            </tr>
        `;
        if (window.Echo) {
            window.Echo.private('admin-channel')
                .listen('OrderPlaced', (e) => {
                    const tableBody = document.querySelector('#pending-orders-tbody');
                    const noOrdersRow = document.getElementById('no-pending-orders');
                    if(noOrdersRow) { noOrdersRow.remove(); }
                    tableBody.insertAdjacentHTML('afterbegin', newOrderRowTemplate(e.order));
                    try { new Audio('/audio/notification.mp3').play(); } catch(err) {}
                    document.title = '(!) Pesanan Baru Masuk!';
                    setTimeout(() => { document.title = 'Admin Dashboard'; }, 5000);
                });
        }
    </script>
    @endpush
</x-app-layout>
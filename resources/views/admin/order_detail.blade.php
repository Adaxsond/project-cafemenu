<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pesanan #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">&larr; Kembali ke Dashboard</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b">
                        <div>
                            <p class="text-sm text-gray-500">Order ID</p>
                            <p class="font-bold text-lg">#{{ $order->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nomor Meja</p>
                            <p class="font-bold text-lg">{{ $order->table_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Waktu Pesan</p>
                            <p class="font-bold text-lg">{{ $order->created_at->format('d M Y, H:i:s') }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-bold text-lg">
                                {{-- ====================================================== --}}
                                {{-- PERBAIKAN DI SINI: Gunakan ->label() dan ->color() --}}
                                {{-- ====================================================== --}}
                                <span class="px-3 py-1 text-sm rounded-full {{ $order->status->color() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <h4 class="text-xl font-bold mb-4">Item Pesanan</h4>
                    <table class="min-w-full bg-white border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Produk</th>
                                <th class="py-2 px-4 border-b text-left">Catatan</th>
                                <th class="py-2 px-4 border-b text-center">Jumlah</th>
                                <th class="py-2 px-4 border-b text-right">Harga Satuan</th>
                                <th class="py-2 px-4 border-b text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $item->product->name }}</td>
                                    <td class="py-2 px-4 border-b text-gray-600 italic">{{ $item->notes ?? '-' }}</td>
                                    <td class="py-2 px-4 border-b text-center">x {{ $item->quantity }}</td>
                                    <td class="py-2 px-4 border-b text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-2 px-4 border-b text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-bold bg-gray-50">
                                <td colspan="4" class="py-2 px-4 text-right">Total</td>
                                <td class="py-2 px-4 text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Tombol untuk mengubah status --}}
                    @if($order->status != \App\Enums\OrderStatus::COMPLETED && $order->status != \App\Enums\OrderStatus::CANCELLED)
                        <div class="mt-6 flex justify-end space-x-4">
                            @if($order->status == \App\Enums\OrderStatus::PENDING)
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Proses Pesanan</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Tandai Selesai</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
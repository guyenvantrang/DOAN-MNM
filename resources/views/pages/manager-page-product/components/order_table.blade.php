<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th class="px-6 py-3">Mã Đơn</th>
            <th class="px-6 py-3">Khách hàng</th>
            <th class="px-6 py-3">Ngày đặt</th>
            <th class="px-6 py-3 text-right">Tổng tiền</th>
            <th class="px-6 py-3 text-center">Trạng thái</th>
            <th class="px-6 py-3 text-center">Thanh toán</th>
            <th class="px-6 py-3 text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($donhangs as $dh)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                <td class="px-6 py-4 font-mono font-bold text-blue-600">{{ $dh->MADH }}</td>
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800 dark:text-white">{{ $dh->TEN_NGUOINHAN }}</div>
                    <div class="text-xs text-gray-500">{{ $dh->SDT_NGUOINHAN }}</div>
                </td>
                <td class="px-6 py-4 text-xs">
                    {{ \Carbon\Carbon::parse($dh->NGAYDAT)->format('d/m/Y H:i') }}
                </td>
                <td class="px-6 py-4 text-right font-bold text-orange-600">
                    {{ number_format($dh->TONGTHANHTOAN) }} ₫
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded text-xs font-bold 
                        {{ $dh->TRANGTHAI_DONHANG == 3 ? 'bg-green-100 text-green-800' : 
                          ($dh->TRANGTHAI_DONHANG == 4 ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $dh->TRANGTHAI_DONHANG == 0 ? 'Chờ duyệt' : 
                          ($dh->TRANGTHAI_DONHANG == 1 ? 'Đang xử lý' : 
                          ($dh->TRANGTHAI_DONHANG == 2 ? 'Đang giao' : 
                          ($dh->TRANGTHAI_DONHANG == 3 ? 'Hoàn thành' : 'Đã hủy'))) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                     @if($dh->TRANGTHAI_THANHTOAN == 1)
                        <span class="text-green-500 text-xs font-bold">✔ Đã TT</span>
                     @else
                        <span class="text-gray-400 text-xs">Chưa TT</span>
                     @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <button @click="openDetail('{{ $dh->MADH }}')" class="text-blue-600 hover:underline font-medium text-xs uppercase">Chi tiết</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">Chưa có đơn hàng nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="p-4">{{ $donhangs->links('pagination::tailwind') }}</div>
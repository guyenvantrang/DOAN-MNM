<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th class="px-6 py-3">Mã KH</th>
            <th class="px-6 py-3">Họ tên</th>
            <th class="px-6 py-3">Liên hệ</th>
            <th class="px-6 py-3">Ngày tham gia</th>
            <th class="px-6 py-3 text-center">Trạng thái</th>
            <th class="px-6 py-3 text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($khachhangs as $kh)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-mono font-bold text-pink-600">{{ $kh->MAKH }}</td>
                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                    {{ $kh->HOTEN }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="text-gray-800 dark:text-gray-200">{{ $kh->SDT }}</span>
                        <span class="text-xs text-gray-500">{{ $kh->EMAIL }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-xs">
                    {{ \Carbon\Carbon::parse($kh->NGAYTAO)->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if($kh->TRANGTHAI == 1)
                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-green-400">
                            Hoạt động
                        </span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-red-400">
                            🔒 Đã khóa
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <button @click="openEditModal({{ $kh }})" 
                                class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-xs font-bold shadow transition">
                            Sửa / Khóa
                        </button>

                        <form action="{{ route('kh.xoa', $kh->MAKH) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold shadow transition">
                                Xóa
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                    Không tìm thấy khách hàng nào.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="p-4 border-t dark:border-gray-700">
    {{ $khachhangs->links('pagination::tailwind') }}
</div>
<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th class="px-6 py-3">Mã NV</th>
            <th class="px-6 py-3">Họ tên</th>
            <th class="px-6 py-3">Email & SĐT</th>
            <th class="px-6 py-3">Chức vụ</th>
            <th class="px-6 py-3 text-center">Trạng thái</th>
            <th class="px-6 py-3 text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($nhanviens as $nv)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-mono">{{ $nv->MANV }}</td>
                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                    {{ $nv->HO }} {{ $nv->TEN }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span>{{ $nv->EMAIL }}</span>
                        <span class="text-xs text-gray-400">{{ $nv->SDT }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                        {{ $nv->chucVu->TENCV ?? 'Chưa phân' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($nv->TRANGTHAI == 1)
                        <span class="text-green-500 font-bold">Hoạt động</span>
                    @else
                        <span class="text-red-500 font-bold">Đã nghỉ</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        {{-- Nút Sửa: Truyền dữ liệu row vào hàm Alpine openEditModal --}}
                        <button @click="openEditModal({{ $nv }})" 
                                class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs transition">
                            Sửa
                        </button>

                        {{-- Nút Xóa --}}
                        <form action="{{ route('nv.xoa', $nv->MANV) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition">
                                Xóa
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    Không tìm thấy nhân viên nào.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Phân trang --}}
<div class="p-4">
    {{ $nhanviens->links('pagination::tailwind') }}
</div>
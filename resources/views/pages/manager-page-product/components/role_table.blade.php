<div class="relative overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Mã CV</th>
                <th scope="col" class="px-6 py-3">Tên chức vụ</th>
                <th scope="col" class="px-6 py-3">Quyền hạn</th>
                <th scope="col" class="px-6 py-3">Mô tả</th>
                <th scope="col" class="px-6 py-3 text-center">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chucvus as $cv)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-white">
                        {{ $cv->MACV }}
                    </td>
                    <td class="px-6 py-4 font-medium text-blue-600 dark:text-blue-400">
                        {{ $cv->TENCV }}
                    </td>
                    <td class="px-6 py-4 truncate max-w-xs" title="{{ $cv->QUYENHAN }}">
                        {{ Str::limit($cv->QUYENHAN, 50) }}
                    </td>
                    <td class="px-6 py-4 text-gray-500">
                        {{ Str::limit($cv->MOTA, 50) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            {{-- Nút Sửa --}}
                            <button @click="openEditModal({{ $cv }})" 
                                    class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs transition shadow-sm">
                                Sửa
                            </button>

                            {{-- Nút Xóa --}}
                            <form action="{{ route('cv.xoa', $cv->MACV) }}" method="POST" 
                                  onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa chức vụ [{{ $cv->TENCV }}] không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition shadow-sm">
                                    Xóa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                        Chưa có chức vụ nào được tạo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Phân trang --}}
    <div class="p-4 border-t dark:border-gray-700">
        {{ $chucvus->links('pagination::tailwind') }}
    </div>
</div>
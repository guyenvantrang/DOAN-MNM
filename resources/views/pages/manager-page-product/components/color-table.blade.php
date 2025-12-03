@forelse($mausac as $ms)
<tr class="hover:bg-gray-700 transition-colors">
    <td class="px-4 py-2 font-medium">{{ $ms->MMS }}</td>
    <td class="px-4 py-2">{{ $ms->TENMAU }}</td>
    <td class="px-4 py-2">{{ $ms->MOTA ?? '-' }}</td>
    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($ms->NGAYTAO)->format('d/m/Y H:i') }}</td>
    <td class="px-4 py-2 flex gap-2">
        <!-- Nút Sửa -->
        <div x-data="{ modalOpen: false }">
            <button @click="modalOpen = true;"
                class="flex items-center gap-1 px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm transition">
                <!-- Icon bút -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.83a2 2 0 01-.828.497l-4 1a1 1 0 01-1.213-1.213l1-4a2 2 0 01.497-.828z" />
                </svg>
                Sửa
            </button>

            @include('components.message-box.color-fix', ['mausac' => $ms, 'modalOpen' => 'modalOpen'])
        </div>

        <!-- Nút Xóa -->
        <form action="{{ route('ms.xoa', $ms->MMS) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition"
                onclick="return confirm('Bạn có chắc muốn xóa màu sắc này không?')">
                <!-- Icon thùng rác -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Xóa
            </button>
        </form>
    </td>

</tr>
@empty
<tr>
    <td colspan="5" class="px-4 py-3 text-center text-gray-400">Không có dữ liệu</td>
</tr>
@endforelse

<!-- Modal Sửa Chức Năng -->
<div >
    <!-- Modal -->
    <div  x-show="{{ $modalOpen }}" x-cloak x-transition.opacity style="background-color: rgba(0,0,0,0.6);"
        class="fixed inset-0 z-50 flex items-center justify-center pointer-events-auto">

        <div @click.away="modalOpen = false" x-transition.scale
            class="bg-gray-900 rounded-xl p-6 w-full max-w-lg shadow-2xl border border-gray-700">

            <!-- Header -->
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-2xl font-bold text-gray-100">Sửa chức năng</h2>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('cn.sua_', $chucnang->MCNANG) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Tên chức năng -->
                <div>
                    <label for="TENCHUCNANG" class="block text-gray-200 mb-1">Tên chức năng:</label>
                    <input type="text" id="TENCHUCNANG" name="TENCHUCNANG"
                        value="{{ old('TENCHUCNANG', $chucnang->TENCHUCNANG) }}"
                        class="w-full px-3 py-2 rounded bg-gray-800 text-gray-100 border border-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <!-- Mô tả -->
                <div>
                    <label for="MOTA" class="block text-gray-200 mb-1">Mô tả:</label>
                    <textarea id="MOTA" name="MOTA" rows="3"
                        class="w-full px-3 py-2 rounded bg-gray-800 text-gray-100 border border-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('MOTA', $chucnang->MOTA) }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="modalOpen = false"
                        class="flex items-center gap-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition">
                        Đóng
                    </button>
                    <button type="submit"
                        class="flex items-center gap-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition">
                        Sửa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

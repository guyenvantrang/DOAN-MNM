<!-- Modal thêm chức năng -->
<div>
    <div x-show="modalOpen" x-cloak x-transition.opacity style="background-color: rgba(0,0,0,0.6);"
        class="fixed inset-0 z-50 flex items-center justify-center pointer-events-auto">

        <div @click.away="modalOpen = false" x-transition.scale
            class="bg-gray-900 rounded-xl p-6 w-full max-w-lg shadow-2xl border border-gray-700">

            <!-- Header -->
            <div class="flex items-center justify-between mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-100"> Thêm chức năng</h2>
                <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('cn.them_') }}" method="POST" id="addFunctionForm" class="space-y-4">
                @csrf
                <!-- Tên chức năng -->
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5l3 3-12 12H6v-3l12-12z" />
                        </svg>
                        <label for="TENCHUCNANG" class="block text-gray-200 mb-1">Tên chức năng:</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.5 2.5l3 3-12 12H6v-3l12-12z" />
                            </svg>
                        </span>
                        <input type="text" id="TENCHUCNANG" name="TENCHUCNANG"
                            class="w-full pl-10 px-3 py-2 rounded bg-gray-800 text-gray-100 border border-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <!-- Mô tả -->
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                        </svg>
                        <label for="MOTA" class="block text-gray-200 mb-1">Mô tả:</label>
                    </div>
                    <div class="relative">
                        <span class="absolute top-2 left-3 flex items-start text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <textarea id="MOTA" name="MOTA" rows="3"
                            class="w-full pl-10 px-3 py-2 rounded bg-gray-800 text-gray-100 border border-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="modalOpen=false"
                        class="flex items-center gap-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Đóng
                    </button>

                    <button type="submit"
                        class="flex items-center gap-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Thêm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

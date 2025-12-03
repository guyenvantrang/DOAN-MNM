<div x-show="modalOpen" x-transition.opacity x-cloak style="background-color: rgba(69, 69, 69, 0.67);"
    class="fixed inset-0 z-40 flex items-center justify-center pointer-events-auto">


    <!-- Modal container -->
    <div @click.away="modalOpen=false" x-transition.scale
        class="bg-gray-900 rounded-xl p-6 w-full max-w-lg mx-4 shadow-lg border border-gray-700 pointer-events-auto z-50">
        <h2 class="text-xl font-bold mb-5 text-gray-100 text-center">Chọn bảng cần quản lý</h2>

        <!-- Grid các bảng -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <template x-for="tbl in [
                            {name:'Loại sản phẩm', link:'loaisanpham', icon:'M19 11H5m14 0a8 8 0 11-16 0 8 8 0 0116 0z'},
                            {name:'Thương hiệu', link:'thuonghieu', icon:'M12 2l1.09 3.26L16 6l-2.5 2.16L15.18 11 12 9.27 8.82 11 9.5 8.16 7 6l2.91-.74L12 2z'},
                            {name:'Đường kính', link:'duongkinh', icon:'M12 4v16m8-8H4'},
                            {name:'Chiều dài dây', link:'chieudaiday', icon:'M4 4h16v16H4z'},
                            {name:'Độ dày', link:'doday', icon:'M4 12h16'},
                            {name:'Chiều rộng dây', link:'chieurongday', icon:'M12 4h0v16h0'},
                            {name:'Khối lượng', link:'khoiluong', icon:'M6 12h12'},
                            {name:'Công nghệ chống nước', link:'congnghe', icon:'M12 3v18'},
                            {name:'Màu sắc', link:'mausac', icon:'M3 12a9 9 0 1118 0 9 9 0 01-18 0z'},
                            {name:'Chức năng', link:'chucnang', icon:'M5 12h14'} 
                        ]" :key="tbl.link">
                <a :href="tbl.link"
                    class="flex flex-col items-center justify-center bg-gray-800 rounded-lg p-4 hover:bg-blue-600 transition text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mb-2" width="30" height="30" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path :d="tbl.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="text-gray-100 font-medium text-sm" x-text="tbl.name"></span>
                </a>
            </template>
        </div>

        <!-- Close button -->
        <div class="mt-6 flex justify-center">
            <button @click="modalOpen=false"
                class="px-6 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 transition text-white">
                Đóng
            </button>
        </div>
    </div>
</div>
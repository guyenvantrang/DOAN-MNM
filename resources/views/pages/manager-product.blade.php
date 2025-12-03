@extends('layouts.admin')

@section('content')
    {{-- Hiển thị thông báo lỗi/thành công --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce" role="alert">
            <strong class="font-bold">Có lỗi xảy ra!</strong>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif

    {{-- 
        X-DATA CHÍNH:
        - showFilterDrawer: Quản lý đóng mở bộ lọc bên trái
        - addModalOpen: Quản lý modal thêm mới
    --}}
    <div x-data="{ showFilterDrawer: false, addModalOpen: false }" class="p-6 w-full relative min-h-screen bg-gray-50 dark:bg-gray-900">

        {{-- HEADER & TOOLBAR --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            {{-- Tiêu đề --}}
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg shadow-lg shadow-blue-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                Quản lý hàng hóa
            </h1>

            {{-- Toolbar Phải --}}
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                {{-- Ô tìm kiếm --}}
                <div class="relative flex-1 md:w-72 group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition shadow-sm">
                </div>

                {{-- Nút Bật Bộ Lọc (Drawer Trigger) --}}
                <button @click="showFilterDrawer = true" 
                        class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2H3V4zm0 6h18v2H3v-2zm0 6h18v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" />
                    </svg>
                    Bộ lọc
                </button>

                {{-- Nút Thêm Mới --}}
                <button @click="addModalOpen = true" 
                        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl transition shadow-lg shadow-blue-500/30 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm mới
                </button>

                {{-- Nút Quản lý chi tiết --}}
                <div x-data="{ modalOpen:false }">
                    <button @click="modalOpen=true" class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl transition shadow-md font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                        Quản lý chi tiết
                    </button>
                    @include('components.message-box.product-manager-detail')
                </div>
            </div>
        </div>

        {{-- BẢNG SẢN PHẨM (FULL WIDTH) --}}
        <div id="table-sanpham" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300">
            @include('pages.manager-page-product.components.product-table')
        </div>

        {{-- 
            ================================================================
            DRAWER BỘ LỌC (OFF-CANVAS - LEFT SIDE - 40% WIDTH)
            ================================================================
        --}}
        
        {{-- Backdrop mờ --}}
        <div x-show="showFilterDrawer" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showFilterDrawer = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40"
             style="display: none;"></div>

        {{-- Drawer Panel (Trượt từ TRÁI sang) --}}
        <div x-show="showFilterDrawer"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full" 
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-full md:w-[40%] bg-white dark:bg-gray-900 shadow-2xl z-50 flex flex-col border-r border-gray-200 dark:border-gray-800"
             style="display: none;">

            {{-- Drawer Header --}}
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                    <span class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2H3V4zm0 6h18v2H3v-2zm0 6h18v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" />
                        </svg>
                    </span>
                    Bộ lọc tìm kiếm
                </h2>
                <button @click="showFilterDrawer = false" class="text-gray-400 hover:text-red-500 transition p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Drawer Body (Chứa Form Lọc - Cuộn dọc) --}}
            <div class="flex-1 overflow-y-auto p-6 custom-scroll">
                {{-- Form giữ nguyên ID để JS hoạt động --}}
                <form id="filterForm" @submit.prevent="submitFilter(); showFilterDrawer = false;">
                    
                    <div class="space-y-8">
                        {{-- Nhóm 1: Cơ bản --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 dark:border-gray-700 pb-2">Phân loại & Thương hiệu</h4>
                            @include('components.filter.filter_products.category')
                            @include('components.filter.filter_products.brand')
                        </div>

                        {{-- Nhóm 2: Kho & Giá --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 dark:border-gray-700 pb-2">Kho hàng & Giá cả</h4>
                            @include('components.filter.filter_products.inventory_quantity')
                            @include('components.filter.filter_products.price')
                        </div>

                        {{-- Nhóm 3: Khác --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-200 dark:border-gray-700 pb-2">Thông tin khác</h4>
                            @include('components.filter.filter_products.warehouse_time')
                            
                            {{-- Accordion cho Thông số kỹ thuật --}}
                            <div x-data="{ expanded: false }" class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                <button type="button" @click="expanded = !expanded" class="w-full px-5 py-3 bg-gray-50 dark:bg-gray-800 flex justify-between items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <span>Thông số kỹ thuật chi tiết</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="expanded" x-collapse class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 space-y-4">
                                    @include('components.filter.filter_products.specifications')
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Drawer Footer (Actions) --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 flex gap-4">
                <button type="button" @click="window.location.reload()" 
                        class="flex-1 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition font-bold text-sm">
                    Xóa lọc
                </button>
                <button type="button" @click="submitFilter(); showFilterDrawer = false;" 
                        class="flex-[2] px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-blue-500/30 flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2H3V4zm0 6h18v2H3v-2zm0 6h18v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" />
                    </svg>
                    Áp dụng bộ lọc
                </button>
            </div>
        </div>

        {{-- 
            ================================================================
            MODAL THÊM SẢN PHẨM MỚI (Code cũ giữ nguyên)
            ================================================================
        --}}
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div x-show="addModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

            <div x-data="{ activeTab: 'basic' }" x-show="addModalOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="bg-gray-900 rounded-2xl w-full max-w-6xl shadow-2xl border border-gray-700 overflow-hidden flex flex-col max-h-[90vh] relative z-10 mx-4">

                {{-- Header Modal --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900">
                    <h2 class="text-2xl font-bold text-gray-100 flex items-center gap-3">
                        <div class="p-2 bg-blue-600/20 rounded-lg text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        Thêm sản phẩm mới
                    </h2>
                    <button type="button" @click="addModalOpen = false" class="text-gray-400 hover:text-red-400 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Tabs Navigation --}}
                <div class="flex gap-1 p-4 border-b border-gray-700 bg-gray-800 overflow-x-auto">
                    <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap">Thông tin cơ bản</button>
                    <button @click="activeTab = 'specs'" :class="activeTab === 'specs' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap">Thông số kỹ thuật</button>
                    <button @click="activeTab = 'images'" :class="activeTab === 'images' ? 'bg-pink-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap">Hình ảnh</button>
                    <button @click="activeTab = 'description'" :class="activeTab === 'description' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition whitespace-nowrap">Mô tả</button>
                </div>

                {{-- Form Content (Giữ nguyên form chuẩn đã validate trước đó) --}}
                <form id="addProductForm" action="{{ route('sp.them') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto custom-scroll">
                    @csrf
                    
                    {{-- TAB 1: CƠ BẢN --}}
                    <div x-show="activeTab === 'basic'" class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Mã sản phẩm</label>
                                <input type="text" disabled placeholder="Tự động tạo (SP000001)" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-400 border border-gray-600 cursor-not-allowed" />
                            </div>
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                                <input type="text" name="TENSP" value="{{ old('TENSP') }}" required placeholder="VD: Đồng hồ Casio..." class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Thương hiệu</label>
                                <select name="MATHUONGHIEU" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 transition">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($thuonghieus as $th)
                                        <option value="{{ $th->MATHUONGHIEU }}">{{ $th->TENTHUONGHIEU }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Loại sản phẩm</label>
                                <select name="MALOAI" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 transition">
                                    <option value="">-- Chọn loại --</option>
                                    @foreach($loaisps as $loai)
                                        <option value="{{ $loai->MALOAI }}">{{ $loai->TENLOAI }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Số lượng tồn</label>
                                <input type="number" name="SOLUONGTON" value="{{ old('SOLUONGTON', 0) }}" min="0" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Giá bán (VNĐ)</label>
                                <input type="number" name="GIABAN" value="{{ old('GIABAN') }}" step="1000" min="0" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 transition" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Giá nhập (VNĐ)</label>
                                <input type="number" name="GIANHAP" value="{{ old('GIANHAP') }}" step="1000" min="0" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 transition" />
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: THÔNG SỐ (Copy nguyên logic vòng lặp php) --}}
                    <div x-show="activeTab === 'specs'" class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $specs = [
                                    ['label' => 'Đường kính', 'name' => 'MADK', 'data' => $duongkinhs, 'key' => 'MADK', 'display' => 'CHISO', 'unit' => 'DONVIDO'],
                                    ['label' => 'Chiều dài dây', 'name' => 'MADD', 'data' => $chieudadays, 'key' => 'MADD', 'display' => 'CHISO', 'unit' => 'DONVIDO'],
                                    ['label' => 'Độ dày', 'name' => 'MADDY', 'data' => $dodays, 'key' => 'MADDY', 'display' => 'CHISO', 'unit' => 'DONVIDO'],
                                    ['label' => 'Rộng dây', 'name' => 'MCRD', 'data' => $chieurongdays, 'key' => 'MCRD', 'display' => 'CHISO', 'unit' => 'DONVIDO'],
                                    ['label' => 'Khối lượng', 'name' => 'MKL', 'data' => $khoiluongs, 'key' => 'MKL', 'display' => 'CHISO', 'unit' => 'DONVIDO'],
                                    ['label' => 'Chống nước', 'name' => 'MCN', 'data' => $chongnuocs, 'key' => 'MCN', 'display' => 'TEN', 'unit' => ''],
                                    ['label' => 'Màu sắc', 'name' => 'MMS', 'data' => $mausacs, 'key' => 'MMS', 'display' => 'TENMAU', 'unit' => ''],
                                    ['label' => 'Chức năng', 'name' => 'MCNANG', 'data' => $chucnangs, 'key' => 'MCNANG', 'display' => 'TENCHUCNANG', 'unit' => ''],
                                ];
                            @endphp
                            @foreach($specs as $spec)
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ $spec['label'] }}</label>
                                    <select name="{{ $spec['name'] }}" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 transition">
                                        <option value="">-- Chọn --</option>
                                        @foreach($spec['data'] as $item)
                                            <option value="{{ $item->{$spec['key']} }}">{{ $item->{$spec['display']} }} {{ $item->{$spec['unit']} ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- TAB 3: HÌNH ẢNH (Copy nguyên form ảnh chuẩn) --}}
                    <div x-show="activeTab === 'images'" class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Hình ảnh chính</label>
                                <div class="border-2 border-dashed border-gray-600 rounded-lg p-8 text-center hover:border-pink-500 transition cursor-pointer bg-gray-800/50">
                                    <input type="file" name="HINHANHCHINH" class="hidden" id="addMainImage" accept="image/*" onchange="document.getElementById('mainImageText').innerText = this.files[0] ? this.files[0].name : 'Tải ảnh chính lên'">
                                    <label for="addMainImage" class="cursor-pointer block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p id="mainImageText" class="text-gray-400 font-medium">Tải ảnh chính lên</p>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-3">Hình ảnh chi tiết</label>
                                <div class="border-2 border-dashed border-gray-600 rounded-lg p-8 text-center hover:border-pink-500 transition cursor-pointer bg-gray-800/50">
                                    <input type="file" name="CHITIETHINHANH[]" multiple class="hidden" id="addDetailImage" accept="image/*" onchange="document.getElementById('detailImageText').innerText = this.files.length + ' ảnh đã được chọn'">
                                    <label for="addDetailImage" class="cursor-pointer block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        <p id="detailImageText" class="text-gray-400 font-medium">Tải nhiều ảnh chi tiết</p>
                                        <p class="text-xs text-gray-500 mt-2">(Giữ phím Ctrl để chọn nhiều)</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: MÔ TẢ --}}
                    <div x-show="activeTab === 'description'" class="p-8 h-full">
                        <textarea name="MOTA" rows="8" placeholder="Nhập mô tả chi tiết sản phẩm..." class="w-full px-4 py-3 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-purple-500 transition resize-none h-full">{{ old('MOTA') }}</textarea>
                    </div>
                </form>

                {{-- Footer Modal --}}
                <div class="flex justify-end gap-3 p-6 border-t border-gray-700 bg-gray-800 sticky bottom-0">
                    <button type="button" @click="addModalOpen = false" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition font-medium">Hủy bỏ</button>
                    <button type="submit" form="addProductForm" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-lg shadow-blue-900/50 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Thêm mới
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT AJAX LỌC (Giữ nguyên) --}}
    <script>
        function submitFilter() {
            const form = document.getElementById('filterForm');
            if(!form) return;

            const formData = new FormData(form);
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value) {
                formData.append('search', searchInput.value);
            }

            const params = new URLSearchParams(formData).toString();
            const tableContainer = document.getElementById('table-sanpham');
            
            tableContainer.style.opacity = '0.5';
            tableContainer.style.pointerEvents = 'none';

            fetch(`{{ route('ql-sanpham') }}?` + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
                if (typeof Alpine !== 'undefined') {
                    // Alpine.initTree(tableContainer); // Re-init nếu cần
                }
            })
            .catch(error => {
                console.error('Lỗi lọc:', error);
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            });
        }

        let typingTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(submitFilter, 500);
        });
    </script>
@endsection
@extends('layouts.admin')

@section('content')
    {{-- Thông báo --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce">
            <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif
    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="promotionManager()" class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-yellow-500 rounded-lg text-white shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                </span>
                Quản lý Khuyến Mãi
            </h1>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <form method="GET" action="{{ route('km.index') }}" class="relative flex-1 md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã code..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-yellow-500 outline-none">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </form>
                <button @click="openAddModal()" class="flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition shadow-md font-bold whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tạo chương trình mới
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Loại</th>
                        <th class="px-6 py-3">Tên chương trình</th>
                        <th class="px-6 py-3">Mã Code</th>
                        <th class="px-6 py-3">Giảm giá</th>
                        <th class="px-6 py-3">Thời gian</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($khuyenmais as $km)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 text-center">
                                @if($km->MA_CODE)
                                    <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded border border-purple-200">Coupon</span>
                                @else
                                    <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded border border-orange-200">Flash Sale</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $km->TENKM }}</td>
                            <td class="px-6 py-4">
                                @if($km->MA_CODE)
                                    <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $km->MA_CODE }}</span>
                                    <div class="text-xs text-gray-400">Còn: {{ $km->SOLUONG_MA }}</div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Tự động áp dụng</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-green-600">
                                @if($km->LOAIKM == 'PHAN_TRAM')
                                    {{ $km->GIATRI }}% <br> <span class="text-xs text-gray-400 font-normal">(Max {{ number_format($km->GIAM_TOI_DA) }})</span>
                                @else
                                    {{ number_format($km->GIATRI) }} đ
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                {{ \Carbon\Carbon::parse($km->NGAYBATDAU)->format('d/m/y H:i') }} <br> ↓ <br>
                                {{ \Carbon\Carbon::parse($km->NGAYKETTHUC)->format('d/m/y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $now = now();
                                    if ($km->TRANGTHAI == 0) $status = ['bg-gray-200 text-gray-800', 'Đã tắt'];
                                    elseif ($now < $km->NGAYBATDAU) $status = ['bg-blue-100 text-blue-800', 'Sắp diễn ra'];
                                    elseif ($now > $km->NGAYKETTHUC) $status = ['bg-red-100 text-red-800', 'Đã kết thúc'];
                                    else $status = ['bg-green-100 text-green-800', 'Đang chạy'];
                                @endphp
                                <span class="{{ $status[0] }} text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    {{ $status[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    {{-- Chỉ hiện nút SP nếu là loại Giảm Sản Phẩm (MA_CODE null) --}}
                                    @if(!$km->MA_CODE)
                                    <button @click="openProductModal({{ $km }})" class="text-green-600 hover:text-green-800" title="Chọn sản phẩm áp dụng">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </button>
                                    @endif

                                    <button @click="openEditModal({{ $km }})" class="text-blue-600 hover:text-blue-800"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.83a2 2 0 01-.828.497l-4 1a1 1 0 01-1.213-1.213l1-4a2 2 0 01.497-.828z" /></svg></button>
                                    
                                    <form action="{{ route('km.xoa', $km->MAKM) }}" method="POST" onsubmit="return confirm('Xóa khuyến mãi này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">Chưa có dữ liệu.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Phân trang --}}
            <div class="p-4">
                {{ $khuyenmais->links() }}
            </div>
        </div>

        {{-- MODAL THÊM / SỬA --}}
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="isModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-2xl z-10 shadow-2xl overflow-hidden transform transition-all"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="isEditMode ? 'Cập nhật khuyến mãi' : 'Tạo chương trình mới'"></h3>
                    <button @click="isModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                <form :action="formAction" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto custom-scroll">
                    @csrf
                    <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'">

                    {{-- CHỌN CHẾ ĐỘ --}}
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800 mb-4">
                        <label class="block text-sm font-bold mb-2 text-blue-800 dark:text-blue-300">Chọn loại chương trình:</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="MODE" value="COUPON" x-model="mode" :disabled="isEditMode" class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium text-sm">Mã giảm giá (Coupon)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="MODE" value="PRODUCT" x-model="mode" :disabled="isEditMode" class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium text-sm">Giảm giá sản phẩm (Flash Sale)</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2" x-text="mode == 'COUPON' ? 'Khách hàng nhập mã code khi thanh toán để được giảm.' : 'Giảm trực tiếp trên giá sản phẩm, không cần nhập mã.'"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Tên chương trình <span class="text-red-500">*</span></label>
                            <input type="text" name="TENKM" x-model="form.TENKM" required class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500" placeholder="VD: Sale Hè 2025">
                        </div>

                        {{-- Mã Code (Chỉ hiện khi là Coupon) --}}
                        <div x-show="mode == 'COUPON'" x-transition>
                            <label class="block text-sm font-medium mb-1">Mã Code <span class="text-red-500">*</span></label>
                            <input type="text" name="MA_CODE" x-model="form.MA_CODE" :required="mode == 'COUPON'" class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500 font-mono uppercase" placeholder="SALE50">
                        </div>

                        {{-- Số lượng (Chỉ hiện khi là Coupon) --}}
                        <div x-show="mode == 'COUPON'" x-transition>
                            <label class="block text-sm font-medium mb-1">Số lượng mã</label>
                            <input type="number" name="SOLUONG_MA" x-model="form.SOLUONG_MA" class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                        </div>
                    </div>

                    {{-- Loại & Giá trị --}}
                    <div class="grid grid-cols-2 gap-4 bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-lg border border-yellow-100 dark:border-yellow-800">
                        <div>
                            <label class="block text-sm font-medium mb-1">Hình thức giảm</label>
                            <select name="LOAIKM" x-model="form.LOAIKM" class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                                <option value="PHAN_TRAM">Theo Phần trăm (%)</option>
                                <option value="TIEN_MAT">Theo Tiền mặt (VNĐ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Giá trị giảm</label>
                            <div class="relative">
                                <input type="number" name="GIATRI" x-model="form.GIATRI" required class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500 font-bold text-green-600">
                                <span class="absolute right-3 top-2.5 text-gray-400 font-bold" x-text="form.LOAIKM == 'PHAN_TRAM' ? '%' : 'đ'"></span>
                            </div>
                        </div>
                        
                        <div class="col-span-2" x-show="form.LOAIKM == 'PHAN_TRAM'" x-transition>
                            <label class="block text-sm font-medium mb-1">Giảm tối đa (VNĐ)</label>
                            <input type="number" name="GIAM_TOI_DA" x-model="form.GIAM_TOI_DA" placeholder="VD: 50000" class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                        </div>

                        <div class="col-span-2" x-show="mode == 'COUPON'">
                            <label class="block text-sm font-medium mb-1">Đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" name="DON_TOI_THIEU" x-model="form.DON_TOI_THIEU" value="0" class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                        </div>
                    </div>

                    {{-- Thời gian --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Bắt đầu</label>
                            <input type="datetime-local" name="NGAYBATDAU" x-model="form.NGAYBATDAU" required class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kết thúc</label>
                            <input type="datetime-local" name="NGAYKETTHUC" x-model="form.NGAYKETTHUC" required class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-yellow-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <button type="button" @click="isModalOpen = false" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 font-medium">Hủy</button>
                        <button type="submit" class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-bold shadow-lg">
                            <span x-text="isEditMode ? 'Lưu thay đổi' : 'Tạo mới'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL CẤU HÌNH SẢN PHẨM --}}
        <div x-show="isProductModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="isProductModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-4xl z-10 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Sản phẩm áp dụng</h3>
                        <p class="text-sm text-blue-600 font-bold" x-text="currentPromo?.TENKM"></p>
                    </div>
                    <button @click="isProductModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
                    {{-- CỘT TRÁI (TÌM KIẾM) --}}
                    <div class="w-full md:w-1/2 p-4 border-r dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-800/50">
                        <h4 class="font-bold text-sm mb-2 text-gray-700 dark:text-gray-300">🔍 Tìm sản phẩm</h4>
                        <div class="relative mb-3">
                            <input type="text" x-model="productSearch" @input.debounce.300ms="searchProducts()" 
                                   placeholder="Nhập tên sản phẩm..." class="w-full pl-3 pr-10 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-green-500 outline-none">
                            <div x-show="isSearching" class="absolute right-3 top-2.5 text-gray-400 animate-spin">⟳</div>
                        </div>
                        <div class="flex-1 overflow-y-auto custom-scroll space-y-2">
                            <template x-for="prod in searchResults" :key="prod.MASP">
                                <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border dark:border-gray-700 hover:border-green-500 transition group cursor-pointer" @click="addProduct(prod.MASP)">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        {{-- Hiển thị ảnh an toàn --}}
                                        <img :src="prod.HINHANHCHINH ? (prod.HINHANHCHINH.includes('http') ? prod.HINHANHCHINH : '/' + prod.HINHANHCHINH) : 'https://placehold.co/50x50?text=No+Img'" 
                                             class="w-10 h-10 object-cover rounded border bg-gray-100" 
                                             onerror="this.src='https://placehold.co/50x50?text=Error'">
                                        
                                        <div class="truncate">
                                            <div class="text-sm font-bold truncate" x-text="prod.TENSP"></div>
                                            <div class="text-xs text-gray-500 flex items-center gap-2">
                                                <span x-text="prod.MASP"></span>
                                                <span class="text-green-600 font-bold" x-show="prod.GIABAN" x-text="formatMoney(prod.GIABAN)"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="text-green-600 hover:bg-green-100 p-1 rounded font-bold px-3 shadow-sm border border-green-100">+</button>
                                </div>
                            </template>
                            <div x-show="searchResults.length === 0 && !isSearching" class="text-center text-gray-500 text-sm italic mt-4">Không tìm thấy sản phẩm nào</div>
                        </div>
                    </div>
                    
                    {{-- CỘT PHẢI (ĐÃ CHỌN) --}}
                    <div class="w-full md:w-1/2 p-4 flex flex-col">
                        <h4 class="font-bold text-sm mb-2 text-gray-700 dark:text-gray-300">✅ Đã áp dụng (<span x-text="selectedProducts.length"></span>)</h4>
                        <div class="flex-1 overflow-y-auto custom-scroll space-y-2">
                            <template x-for="prod in selectedProducts" :key="prod.MASP">
                                <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-100 dark:border-blue-800 group">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <img :src="prod.HINHANHCHINH ? (prod.HINHANHCHINH.includes('http') ? prod.HINHANHCHINH : '/' + prod.HINHANHCHINH) : 'https://placehold.co/50x50?text=No+Img'" 
                                             class="w-10 h-10 object-cover rounded border bg-gray-100">
                                        
                                        <div class="truncate">
                                            <div class="text-sm font-bold truncate text-blue-700 dark:text-blue-300" x-text="prod.TENSP"></div>
                                        </div>
                                    </div>
                                    <button @click="removeProduct(prod.MASP)" class="text-red-400 hover:text-red-600 hover:bg-red-100 p-1 rounded font-bold px-3">✕</button>
                                </div>
                            </template>
                            <div x-show="selectedProducts.length === 0" class="text-center text-gray-500 text-sm italic mt-10">Chưa có sản phẩm nào được áp dụng.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function promotionManager() {
            return {
                isModalOpen: false,
                isProductModalOpen: false,
                isEditMode: false,
                
                mode: 'COUPON', 
                
                currentPromo: null,
                productSearch: '',
                isSearching: false,
                searchResults: [],
                selectedProducts: [],
                formAction: '',
                form: { 
                    TENKM: '', MA_CODE: '', LOAIKM: 'PHAN_TRAM', GIATRI: '', GIAM_TOI_DA: '', 
                    DON_TOI_THIEU: 0, SOLUONG_MA: 100, NGAYBATDAU: '', NGAYKETTHUC: '', MOTA: '', TRANGTHAI: 1 
                },

                openAddModal() {
                    this.isEditMode = false;
                    this.mode = 'COUPON'; 
                    this.form = { 
                        TENKM: '', MA_CODE: '', LOAIKM: 'PHAN_TRAM', GIATRI: '', GIAM_TOI_DA: '', 
                        DON_TOI_THIEU: 0, SOLUONG_MA: 100, 
                        NGAYBATDAU: new Date().toISOString().slice(0, 16), 
                        NGAYKETTHUC: '', MOTA: '', TRANGTHAI: 1 
                    };
                    this.formAction = "{{ route('km.them') }}";
                    this.isModalOpen = true;
                },

                openEditModal(km) {
                    this.isEditMode = true;
                    this.form = { ...km };
                    this.mode = km.MA_CODE ? 'COUPON' : 'PRODUCT';
                    this.form.NGAYBATDAU = km.NGAYBATDAU.replace(' ', 'T').slice(0, 16);
                    this.form.NGAYKETTHUC = km.NGAYKETTHUC.replace(' ', 'T').slice(0, 16);
                    this.formAction = "{{ route('km.index') }}/sua/" + km.MAKM;
                    this.isModalOpen = true;
                },

                // --- QUAN TRỌNG: CÁC HÀM XỬ LÝ API SẢN PHẨM ---
                openProductModal(km) {
                    this.currentPromo = km;
                    this.productSearch = '';
                    this.searchResults = [];
                    this.selectedProducts = []; // Reset list cũ
                    this.isProductModalOpen = true;

                    // Gọi cả 2 hàm tải dữ liệu ngay khi mở Modal
                    this.fetchSelectedProducts(); 
                    this.searchProducts();        
                },

                fetchSelectedProducts() {
                    fetch(`/khuyen-mai/${this.currentPromo.MAKM}/san-pham`)
                        .then(res => {
                            if (!res.ok) throw new Error("Lỗi tải SP đã chọn");
                            return res.json();
                        })
                        .then(data => { this.selectedProducts = data; })
                        .catch(err => console.error(err));
                },

                searchProducts() {
                    this.isSearching = true;

                    // URL: Nếu không có keyword thì không gửi tham số (Controller sẽ trả về 20 SP mới nhất)
                    let url = `/khuyen-mai/${this.currentPromo.MAKM}/tim-san-pham`;
                    if(this.productSearch.trim() !== '') {
                        url += `?keyword=${this.productSearch}`;
                    }

                    fetch(url)
                        .then(res => {
                            if (!res.ok) throw new Error("Lỗi tìm kiếm: " + res.status);
                            return res.json();
                        })
                        .then(data => { 
                            this.searchResults = data; 
                            this.isSearching = false; 
                        })
                        .catch(err => {
                            console.error(err);
                            this.isSearching = false;
                        });
                },

                addProduct(masp) {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch(`/khuyen-mai/${this.currentPromo.MAKM}/them-san-pham`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ masp: masp })
                    })
                    .then(res => {
                        if(!res.ok) throw new Error("Lỗi thêm SP");
                        return res.json();
                    })
                    .then(() => { 
                        this.fetchSelectedProducts(); 
                        this.searchProducts(); // Load lại để ẩn SP vừa thêm
                    })
                    .catch(err => alert("Không thể thêm sản phẩm."));
                },

                removeProduct(masp) {
                    if(!confirm('Gỡ sản phẩm này?')) return;
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch(`/khuyen-mai/${this.currentPromo.MAKM}/xoa-san-pham`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ masp: masp })
                    })
                    .then(res => {
                        if(!res.ok) throw new Error("Lỗi xóa SP");
                        return res.json();
                    })
                    .then(() => { this.fetchSelectedProducts(); })
                    .catch(err => alert("Lỗi khi xóa sản phẩm!"));
                },

                formatMoney(amount) { 
                    return new Intl.NumberFormat('vi-VN').format(amount) + ' đ'; 
                }
            }
        }
    </script>
@endsection
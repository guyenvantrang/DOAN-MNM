{{-- FILE: resources/views/components/message-box/edit-product-modal.blade.php --}}

@php
    // Chuẩn bị dữ liệu ảnh để nạp vào Alpine.js
    $currentImages = is_array($chitietsanpham->CHITIETHINHANH) ? $chitietsanpham->CHITIETHINHANH : [];
@endphp

{{-- 
    CẬP NHẬT x-data:
    1. existingImages: Chứa danh sách ảnh lấy từ DB.
    2. removeImage(index): Hàm xóa ảnh khỏi danh sách giao diện.
--}}
<div x-data="{ 
        modalOpen: true, 
        activeTab: 'basic',
        existingImages: {{ Js::from($currentImages) }}, 
        removeImage(index) {
            // Xóa ảnh khỏi mảng, giao diện tự cập nhật
            this.existingImages.splice(index, 1);
        }
     }" 
     x-show="modalOpen" style="display: none;"
     x-init="$watch('modalOpen', value => { if(!value) { setTimeout(() => { $el.remove(); }, 300) } })"
     class="modal-edit-container fixed inset-0 z-50 flex items-center justify-center pointer-events-auto">

    {{-- Backdrop --}}
    <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="modalOpen = false"></div>

    {{-- Modal Content --}}
    <div x-show="modalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="bg-gray-900 rounded-2xl w-full max-w-6xl shadow-2xl border border-gray-700 overflow-hidden flex flex-col max-h-[92vh] relative z-10 mx-4">

        <div class="flex items-center justify-between p-6 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900 sticky top-0 z-10">
            <h2 class="text-2xl font-bold text-gray-100 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Cập nhật sản phẩm: <span class="text-yellow-400 ml-2">{{ $chitietsanpham->TENSP }}</span>
            </h2>
            <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-red-400 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex gap-1 p-4 border-b border-gray-700 bg-gray-800 overflow-x-auto">
            <button @click="activeTab = 'basic'" :class="activeTab === 'basic' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Thông tin cơ bản
            </button>
            <button @click="activeTab = 'specs'" :class="activeTab === 'specs' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                Thông số kỹ thuật
            </button>
            <button @click="activeTab = 'images'" :class="activeTab === 'images' ? 'bg-pink-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Hình ảnh
            </button>
            <button @click="activeTab = 'description'" :class="activeTab === 'description' ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'" class="px-4 py-2 rounded-lg font-medium transition flex items-center gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Mô tả sản phẩm
            </button>
        </div>

        <form id="editProductForm" action="{{ route('sp.sua', $chitietsanpham->MASP) }}" method="POST"
            enctype="multipart/form-data" class="flex-1 overflow-y-auto overflow-x-hidden custom-scroll">
            @csrf
            @method('PUT')

            {{-- 
                QUAN TRỌNG: Input ẩn chứa danh sách ảnh cũ được giữ lại.
                Khi user xóa ảnh trên giao diện, ảnh đó mất khỏi mảng existingImages -> input này cũng mất theo.
                Controller sẽ dựa vào danh sách này để biết cần giữ lại ảnh nào.
            --}}
            <template x-for="img in existingImages">
                <input type="hidden" name="KEEP_IMAGES[]" :value="img">
            </template>

            <template x-if="activeTab === 'basic'">
                <div class="p-8 space-y-6 animate-fadeIn">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Mã sản phẩm (Không thể sửa)</label>
                            <input type="text" name="MASP" value="{{ $chitietsanpham->MASP }}" readonly class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-400 border border-gray-600 cursor-not-allowed focus:outline-none" />
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                            <input type="text" name="TENSP" value="{{ old('TENSP', $chitietsanpham->TENSP) }}" required class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Thương hiệu</label>
                            <select name="MATHUONGHIEU" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($thuonghieus as $th)
                                    <option value="{{ $th->MATHUONGHIEU }}" {{ $chitietsanpham->MATHUONGHIEU == $th->MATHUONGHIEU ? 'selected' : '' }}>{{ $th->TENTHUONGHIEU }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Loại sản phẩm</label>
                            <select name="MALOAI" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                                <option value="">-- Chọn loại sản phẩm --</option>
                                @foreach($loaisps as $loai)
                                    <option value="{{ $loai->MALOAI }}" {{ $chitietsanpham->MALOAI == $loai->MALOAI ? 'selected' : '' }}>{{ $loai->TENLOAI }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Số lượng tồn</label>
                            <input type="number" name="SOLUONGTON" value="{{ old('SOLUONGTON', $chitietsanpham->SOLUONGTON) }}" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Giá bán (VNĐ)</label>
                            <input type="number" name="GIABAN" value="{{ old('GIABAN', $chitietsanpham->GIABAN) }}" step="1000" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition font-mono" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Giá nhập (VNĐ)</label>
                            <input type="number" name="GIANHAP" value="{{ old('GIANHAP', $chitietsanpham->GIANHAP) }}" step="1000" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition font-mono" />
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="activeTab === 'specs'">
                <div class="p-8 space-y-6 animate-fadeIn">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Đường kính</label>
                            <select name="MADK" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($duongkinhs as $dk)
                                    <option value="{{ $dk->MADK }}" {{ $chitietsanpham->MADK == $dk->MADK ? 'selected' : '' }}>{{ $dk->CHISO }} {{ $dk->DONVIDO }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Chiều dài dây</label>
                            <select name="MADD" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($chieudadays as $cd)
                                    <option value="{{ $cd->MADD }}" {{ $chitietsanpham->MADD == $cd->MADD ? 'selected' : '' }}>{{ $cd->CHISO }} {{ $cd->DONVIDO }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Độ dày</label>
                            <select name="MADDY" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($dodays as $ddy)
                                    <option value="{{ $ddy->MADDY }}" {{ $chitietsanpham->MADDY == $ddy->MADDY ? 'selected' : '' }}>{{ $ddy->CHISO }} {{ $ddy->DONVIDO }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Chiều rộng dây</label>
                            <select name="MCRD" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($chieurongdays as $crd)
                                    <option value="{{ $crd->MCRD }}" {{ $chitietsanpham->MCRD == $crd->MCRD ? 'selected' : '' }}>{{ $crd->CHISO }} {{ $crd->DONVIDO }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Khối lượng</label>
                            <select name="MKL" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($khoiluongs as $kl)
                                    <option value="{{ $kl->MKL }}" {{ $chitietsanpham->MKL == $kl->MKL ? 'selected' : '' }}>{{ $kl->CHISO }} {{ $kl->DONVIDO }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Chống nước</label>
                            <select name="MCN" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($chongnuocs as $cn)
                                    <option value="{{ $cn->MCN }}" {{ $chitietsanpham->MCN == $cn->MCN ? 'selected' : '' }}>{{ $cn->TEN }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Màu sắc</label>
                            <select name="MMS" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($mausacs as $ms)
                                    <option value="{{ $ms->MMS }}" {{ $chitietsanpham->MMS == $ms->MMS ? 'selected' : '' }}>{{ $ms->TENMAU }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Chức năng</label>
                            <select name="MCNANG" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition">
                                <option value="">-- Chọn --</option>
                                @foreach($chucnangs as $cnang)
                                    <option value="{{ $cnang->MCNANG }}" {{ $chitietsanpham->MCNANG == $cnang->MCNANG ? 'selected' : '' }}>{{ $cnang->TENCHUCNANG }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="activeTab === 'images'">
                <div class="p-8 space-y-8 animate-fadeIn">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Hình ảnh chính</label>
                            @if($chitietsanpham->HINHANHCHINH)
                                <div class="mb-3 relative w-full h-48 bg-gray-800 rounded-lg overflow-hidden border border-gray-700 group">
                                    <img src="{{ asset($chitietsanpham->HINHANHCHINH) }}" alt="Main Image" class="w-full h-full object-contain">
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                        <span class="text-white text-sm font-medium">Ảnh hiện tại</span>
                                    </div>
                                </div>
                            @endif
                            <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-pink-500 transition cursor-pointer bg-gray-800/50">
                                <input type="file" name="HINHANHCHINH" class="hidden" id="mainImage" accept="image/*">
                                <label for="mainImage" class="cursor-pointer block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-gray-400 text-sm">Chọn ảnh mới để thay thế</p>
                                    <p class="text-gray-600 text-xs mt-1">Hỗ trợ JPG, PNG, JPEG</p>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Hình ảnh chi tiết</label>

                            {{-- Vùng hiển thị ảnh cũ (Render bằng Alpine để có tương tác Xóa) --}}
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                {{-- x-for loop qua mảng existingImages --}}
                                <template x-for="(img, index) in existingImages" :key="index">
                                    <div class="relative h-20 border border-gray-700 rounded overflow-hidden group">
                                        <img :src="'{{ asset('') }}' + img" class="w-full h-full object-cover">
                                        
                                        {{-- Nút xóa overlay khi hover --}}
                                        <button type="button" @click="removeImage(index)" 
                                                class="absolute inset-0 bg-black/70 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200 cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            {{-- Input thêm ảnh mới --}}
                            <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-pink-500 transition cursor-pointer bg-gray-800/50">
                                {{-- Thêm multiple và sửa name thành mảng --}}
                                <input type="file" name="CHITIETHINHANH[]" multiple class="hidden" id="detailImages" accept="image/*"
                                       onchange="alert(this.files.length + ' ảnh mới đã được chọn.')">
                                <label for="detailImages" class="cursor-pointer block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    <p class="text-gray-400 text-sm">Chọn thêm ảnh chi tiết</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="activeTab === 'description'">
                <div class="p-8 space-y-4 animate-fadeIn h-full">
                    <textarea name="MOTA" rows="10" placeholder="Nhập mô tả sản phẩm..." class="w-full px-4 py-3 rounded-lg bg-gray-800 text-gray-100 border border-gray-600 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition resize-none h-full">{{ old('MOTA', $chitietsanpham->MOTA) }}</textarea>
                    <p class="text-gray-500 text-xs">Mô tả sản phẩm sẽ hiển thị chi tiết ở trang người dùng.</p>
                </div>
            </template>
        </form>

        <div class="flex justify-end gap-3 p-6 border-t border-gray-700 bg-gray-800 sticky bottom-0">
            <button type="button" @click="modalOpen = false"
                class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition font-medium flex items-center gap-2">
                Đóng
            </button>

            <button type="submit" form="editProductForm"
                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition font-medium flex items-center gap-2 shadow-lg shadow-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Lưu thay đổi
            </button>
        </div>
    </div>
</div>
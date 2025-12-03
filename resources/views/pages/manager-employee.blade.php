@extends('layouts.admin')
@section('content')
    {{-- Hiển thị thông báo --}}
    @if ($errors->any())
        <div
            class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- X-DATA: Quản lý trạng thái Modal và Dữ liệu nhân viên đang sửa --}}
    <div x-data="{ 
                    addModalOpen: false, 
                    editModalOpen: false,
                    editingEmp: {}, // Biến lưu thông tin nhân viên đang sửa

                    // Hàm mở modal sửa và điền dữ liệu
                    openEditModal(emp) {
                        this.editingEmp = emp;
                        this.editModalOpen = true;
                    }
                 }" class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-purple-600 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                Quản lý Nhân viên
            </h1>

            <div class="flex flex-wrap gap-3">
                {{-- Ô tìm kiếm AJAX --}}
                <div class="relative">
                    <input type="text" id="searchEmp" placeholder="Tìm tên, email..."
                        class="pl-10 pr-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-purple-500 outline-none">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                {{-- Nút Chuyển trang Quản lý Chức vụ (MỚI THÊM) --}}
                <a href="{{ route('cv.index') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    QL Chức vụ
                </a>

                {{-- Nút Chuyển trang Lập lịch --}}
                <a href="{{ route('nv.lichlam') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Lập lịch làm
                </a>

                {{-- Nút Thêm Nhân viên --}}
                <button @click="addModalOpen = true"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm nhân viên
                </button>
            </div>
        </div>

        {{-- BẢNG NHÂN VIÊN (Load Partial) --}}
        <div id="table-nhanvien-container"
            class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            @include('pages.manager-page-product.components.employee_table')
        </div>

        {{-- MODAL THÊM MỚI --}}
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="addModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-2xl z-10 shadow-2xl border dark:border-gray-700 overflow-hidden"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <div
                    class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold">Thêm nhân viên mới</h3>
                    <button @click="addModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                <form action="{{ route('nv.them') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Họ</label>
                            <input type="text" name="HO" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tên</label>
                            <input type="text" name="TEN" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="EMAIL" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Số điện thoại</label>
                            <input type="text" name="SDT" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Chức vụ</label>

                            <select name="MACV" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                                <option value="">-- Chọn chức vụ --</option>

                                {{-- Kiểm tra kỹ biến $chucvus và thuộc tính viết HOA --}}
                                @if(isset($chucvus) && count($chucvus) > 0)
                                    @foreach($chucvus as $cv)
                                        <option value="{{ $cv->MACV }}">{{ $cv->TENCV }}</option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Chưa có dữ liệu chức vụ</option>
                                @endif

                            </select>
                        </div>

                        {{-- ... các input khác ... --}}
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                        <input type="password" name="MATKHAU" required
                            class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="addModalOpen = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Lưu nhân
                            viên</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL CẬP NHẬT --}}
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="editModalOpen = false"></div>
            <div
                class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-2xl z-10 shadow-2xl border dark:border-gray-700 overflow-hidden">

                <div
                    class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold">Cập nhật nhân viên: <span x-text="editingEmp.HO + ' ' + editingEmp.TEN"
                            class="text-blue-500"></span></h3>
                    <button @click="editModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                {{-- Form action động theo ID --}}
                <form :action="`{{ route('nv.index') }}/sua/${editingEmp.MANV}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Họ</label>
                            <input type="text" name="HO" x-model="editingEmp.HO" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tên</label>
                            <input type="text" name="TEN" x-model="editingEmp.TEN" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Email (Readonly)</label>
                            <input type="email" :value="editingEmp.EMAIL" readonly
                                class="w-full p-2 border rounded bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Chức vụ</label>
                            <select name="MACV" x-model="editingEmp.MACV" required
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                                @foreach($chucvus as $cv)
                                    <option value="{{ $cv->MACV }}">{{ $cv->TENCV }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mật khẩu mới (Bỏ trống nếu không đổi)</label>
                        <input type="password" name="MATKHAU" placeholder="******"
                            class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium">Trạng thái:</label>
                        <select name="TRANGTHAI" x-model="editingEmp.TRANGTHAI"
                            class="p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                            <option value="1">Hoạt động</option>
                            <option value="0">Đã nghỉ/Khóa</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cập
                            nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Script AJAX Tìm kiếm --}}
    <script>
        let timeout = null;
        document.getElementById('searchEmp').addEventListener('input', function () {
            clearTimeout(timeout);
            let query = this.value;
            timeout = setTimeout(() => {
                fetch(`{{ route('nv.timkiem') }}?search=${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('table-nhanvien-container').innerHTML = html;
                    });
            }, 300);
        });
    </script>
@endsection
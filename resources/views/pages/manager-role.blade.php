@extends('layouts.admin')

@section('content')
    {{-- Hiển thị thông báo --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce">
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
    @if (session('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- X-DATA: Quản lý Modal --}}
    <div x-data="{ 
            addModalOpen: false, 
            editModalOpen: false,
            editingRole: {}, 
            
            openEditModal(role) {
                this.editingRole = role;
                this.editModalOpen = true;
            }
         }" 
         class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-blue-600 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                Quản lý Chức Vụ
            </h1>

            <div class="flex flex-wrap gap-3">
                {{-- Ô tìm kiếm AJAX --}}
                <div class="relative">
                    <input type="text" id="searchRole" placeholder="Tìm mã hoặc tên CV..." 
                           class="pl-10 pr-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                {{-- Nút Quay lại NV --}}
                <a href="{{ route('nv.index') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition font-medium shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                    Về QL Nhân viên
                </a>

                {{-- Nút Thêm Chức Vụ --}}
                <button @click="addModalOpen = true" class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm chức vụ
                </button>
            </div>
        </div>

        {{-- BẢNG DỮ LIỆU (Load Partial) --}}
        <div id="table-chucvu-container" class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            @include('pages.manager-page-product.components.role_table')
        </div>

        {{-- MODAL THÊM MỚI --}}
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="addModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-lg z-10 shadow-2xl border dark:border-gray-700 overflow-hidden" 
                 x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold">Thêm chức vụ mới</h3>
                    <button @click="addModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                <form action="{{ route('cv.them') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên chức vụ <span class="text-red-500">*</span></label>
                        <input type="text" name="TENCV" required placeholder="Ví dụ: Quản lý kho" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Quyền hạn</label>
                        <textarea name="QUYENHAN" rows="2" placeholder="Mô tả quyền hạn..." class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mô tả thêm</label>
                        <textarea name="MOTA" rows="3" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Lưu chức vụ</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL CẬP NHẬT --}}
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="editModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-lg z-10 shadow-2xl border dark:border-gray-700 overflow-hidden">
                
                <div class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold">Cập nhật chức vụ: <span x-text="editingRole.MACV" class="text-blue-500 font-mono"></span></h3>
                    <button @click="editModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                {{-- Form action động theo ID --}}
                <form :action="`{{ route('cv.index') }}/sua/${editingRole.MACV}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên chức vụ <span class="text-red-500">*</span></label>
                        <input type="text" name="TENCV" x-model="editingRole.TENCV" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Quyền hạn</label>
                        <textarea name="QUYENHAN" x-model="editingRole.QUYENHAN" rows="2" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mô tả thêm</label>
                        <textarea name="MOTA" x-model="editingRole.MOTA" rows="3" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Script AJAX Tìm kiếm --}}
    <script>
        let roleTimeout = null;
        document.getElementById('searchRole').addEventListener('input', function() {
            clearTimeout(roleTimeout);
            let query = this.value;
            roleTimeout = setTimeout(() => {
                fetch(`{{ route('cv.timkiem') }}?search=${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('table-chucvu-container').innerHTML = html;
                });
            }, 300);
        });
    </script>
@endsection
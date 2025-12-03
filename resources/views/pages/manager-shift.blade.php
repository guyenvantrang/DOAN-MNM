@extends('layouts.admin')

@section('content')
    {{-- Thông báo --}}
    @if ($errors->any())
        <div class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{ 
            addModalOpen: false, 
            editModalOpen: false,
            editingShift: {}, // Lưu thông tin ca đang sửa
            openEditModal(shift) {
                this.editingShift = shift;
                this.editModalOpen = true;
            }
         }" 
         class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-orange-500 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                Quản lý Ca Làm Việc
            </h1>

            <div class="flex gap-3">
                {{-- Nút Quay lại Lập Lịch --}}
                <a href="{{ route('nv.lichlam') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition font-medium shadow-md flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Về Lập Lịch
                </a>

                {{-- Nút Thêm Ca --}}
                <button @click="addModalOpen = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow-md flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm ca mới
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Mã Ca</th>
                        <th class="px-6 py-3">Tên Ca</th>
                        <th class="px-6 py-3">Giờ Bắt Đầu</th>
                        <th class="px-6 py-3">Giờ Kết Thúc</th>
                        <th class="px-6 py-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calamviecs as $ca)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-mono font-bold">{{ $ca->MACA }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $ca->TENCA }}</td>
                            <td class="px-6 py-4">{{ $ca->GIOBATDAU }}</td>
                            <td class="px-6 py-4">{{ $ca->GIOKETTHUC }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button @click="openEditModal({{ $ca }})" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">Sửa</button>
                                    
                                    <form action="{{ route('ca.xoa', $ca->MACA) }}" method="POST" onsubmit="return confirm('Xóa ca này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Chưa có ca làm việc nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL THÊM --}}
        <div x-show="addModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="addModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-md z-10 shadow-2xl overflow-hidden p-6 space-y-4"
                 x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <h3 class="text-lg font-bold">Thêm Ca Làm Việc</h3>
                <form action="{{ route('ca.them') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên Ca</label>
                        <input type="text" name="TENCA" required placeholder="VD: Ca Sáng" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Giờ bắt đầu</label>
                            <input type="time" name="GIOBATDAU" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Giờ kết thúc</label>
                            <input type="time" name="GIOKETTHUC" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Lưu</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL SỬA --}}
        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="editModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-md z-10 shadow-2xl overflow-hidden p-6 space-y-4"
                 x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <h3 class="text-lg font-bold">Cập nhật Ca: <span x-text="editingShift.MACA" class="text-blue-500"></span></h3>
                
                <form :action="`{{ route('ca.index') }}/sua/${editingShift.MACA}`" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên Ca</label>
                        <input type="text" name="TENCA" x-model="editingShift.TENCA" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Giờ bắt đầu</label>
                            <input type="time" name="GIOBATDAU" x-model="editingShift.GIOBATDAU" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Giờ kết thúc</label>
                            <input type="time" name="GIOKETTHUC" x-model="editingShift.GIOKETTHUC" required class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
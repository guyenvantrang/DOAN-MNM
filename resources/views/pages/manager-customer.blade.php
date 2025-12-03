@extends('layouts.admin')
@if ($errors->any())
    <div
        class="fixed top-4 right-4 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg animate-bounce">
        <strong class="font-bold">Có lỗi xảy ra!</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@section('content')
    {{-- Thông báo --}}
    @if (session('success'))
        <div
            class="fixed top-4 right-4 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg animate-fade-in-down">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div
            class="fixed top-4 right-4 z-50 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg animate-fade-in-down">
            {{ session('warning') }}
        </div>
    @endif

    <div x-data="customerManager()"
        class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-pink-600 rounded-lg text-white shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                Quản lý Khách Hàng
            </h1>

            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Tìm kiếm --}}
                <div class="relative flex-1 md:w-64">
                    <input type="text" id="searchInput" placeholder="Tìm tên, sđt, email..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-pink-500 outline-none">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                {{-- Nút Thêm --}}
                <button @click="openAddModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition shadow-md font-bold whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Thêm mới
                </button>
            </div>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div id="table-customer-container"
            class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            @include('pages.manager-page-product.components.customer_table')
        </div>

        {{-- MODAL CHUNG (THÊM / SỬA) --}}
        {{-- MODAL CHUNG (THÊM / SỬA) --}}
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="isModalOpen = false"></div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-2xl z-10 shadow-2xl overflow-hidden transform transition-all"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                <div
                    class="px-6 py-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"
                        x-text="isEditMode ? 'Cập nhật thông tin' : 'Thêm khách hàng mới'"></h3>
                    <button @click="isModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
                </div>

                {{-- Form AJAX --}}
                <form @submit.prevent="submitData" class="p-6 space-y-5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">Họ và Tên <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.HOTEN"
                                class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500"
                                :class="errors.HOTEN ? 'border-red-500' : ''">
                            {{-- Hiển thị lỗi ngay dưới ô input --}}
                            <template x-if="errors.HOTEN">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.HOTEN[0]"></p>
                            </template>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Số điện thoại <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="form.SDT"
                                class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500"
                                :class="errors.SDT ? 'border-red-500' : ''">
                            <template x-if="errors.SDT">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.SDT[0]"></p>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" x-model="form.EMAIL"
                            class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500"
                            :class="errors.EMAIL ? 'border-red-500' : ''">
                        <template x-if="errors.EMAIL">
                            <p class="text-red-500 text-xs mt-1" x-text="errors.EMAIL[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Địa chỉ</label>
                        <input type="text" x-model="form.DIACHI"
                            class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                            <input type="password" x-model="form.MATKHAU" placeholder="******"
                                class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500"
                                :class="errors.MATKHAU ? 'border-red-500' : ''">

                            <template x-if="errors.MATKHAU">
                                <p class="text-red-500 text-xs mt-1" x-text="errors.MATKHAU[0]"></p>
                            </template>
                            <p x-show="isEditMode" class="text-xs text-gray-500 mt-1">Để trống nếu không muốn đổi.</p>
                        </div>

                        <div x-show="isEditMode">
                            <label class="block text-sm font-medium mb-1">Trạng thái tài khoản</label>
                            <select x-model="form.TRANGTHAI"
                                class="w-full p-2.5 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-pink-500">
                                <option value="1">✅ Hoạt động bình thường</option>
                                <option value="0">🔒 Khóa tài khoản</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
                        <button type="button" @click="isModalOpen = false"
                            class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 font-medium">Hủy</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-bold shadow-lg flex items-center gap-2">
                            <span x-show="isLoading"
                                class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                            <span x-text="isEditMode ? 'Lưu thay đổi' : 'Thêm khách hàng'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function customerManager() {
            return {
                isModalOpen: false,
                isEditMode: false,
                isLoading: false,
                actionUrl: '',
                // Object chứa dữ liệu form
                form: { HOTEN: '', EMAIL: '', SDT: '', DIACHI: '', MATKHAU: '', TRANGTHAI: 1 },
                // Object chứa lỗi trả về từ Laravel
                errors: {},

                openAddModal() {
                    this.isEditMode = false;
                    this.errors = {}; // Xóa lỗi cũ
                    this.form = { HOTEN: '', EMAIL: '', SDT: '', DIACHI: '', MATKHAU: '', TRANGTHAI: 1 };
                    this.actionUrl = "{{ route('kh.them') }}";
                    this.isModalOpen = true;
                },

                openEditModal(customer) {
                    this.isEditMode = true;
                    this.errors = {}; // Xóa lỗi cũ
                    this.form = {
                        HOTEN: customer.HOTEN,
                        EMAIL: customer.EMAIL,
                        SDT: customer.SDT,
                        DIACHI: customer.DIACHI,
                        TRANGTHAI: customer.TRANGTHAI,
                        MATKHAU: '' // Reset mật khẩu khi mở form sửa
                    };
                    this.actionUrl = "{{ route('kh.index') }}/sua/" + customer.MAKH;
                    this.isModalOpen = true;
                },

                submitData() {
                    this.isLoading = true;
                    this.errors = {}; // Reset lỗi trước khi gửi

                    let method = this.isEditMode ? 'PUT' : 'POST';

                    fetch(this.actionUrl, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', // Bắt buộc để nhận lỗi JSON 422
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.form)
                    })
                        .then(async response => {
                            const data = await response.json();

                            // Nếu lỗi Validation (422)
                            if (response.status === 422) {
                                this.errors = data.errors; // Gán lỗi vào biến Alpine để hiển thị
                                throw new Error('Dữ liệu không hợp lệ');
                            }

                            // Nếu lỗi Server khác (500)
                            if (!response.ok) {
                                throw new Error(data.message || 'Lỗi hệ thống');
                            }

                            return data;
                        })
                        .then(data => {
                            // Thành công
                            alert(data.message);
                            window.location.reload(); // Tải lại trang để cập nhật bảng
                        })
                        .catch(error => {
                            console.log(error);
                            // Không làm gì cả, lỗi đã được hiển thị lên form nhờ biến `errors`
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }

        // Giữ nguyên phần tìm kiếm AJAX cũ của bạn
        let timeout = null;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(timeout);
            let query = this.value;
            timeout = setTimeout(() => {
                fetch(`{{ route('kh.index') }}?search=${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('table-customer-container').innerHTML = html;
                    });
            }, 300);
        });
    </script>

    </div>

    <script>
        function customerManager() {
            return {
                isModalOpen: false,
                isEditMode: false,
                formAction: '',
                form: { hoten: '', email: '', sdt: '', diachi: '', trangthai: 1 },

                openAddModal() {
                    this.isEditMode = false;
                    this.form = { hoten: '', email: '', sdt: '', diachi: '', trangthai: 1 };
                    this.formAction = "{{ route('kh.them') }}";
                    this.isModalOpen = true;
                },

                openEditModal(customer) {
                    this.isEditMode = true;
                    this.form = {
                        hoten: customer.HOTEN,
                        email: customer.EMAIL,
                        sdt: customer.SDT,
                        diachi: customer.DIACHI,
                        trangthai: customer.TRANGTHAI
                    };
                    this.formAction = "{{ route('kh.index') }}/sua/" + customer.MAKH;
                    this.isModalOpen = true;
                }
            }
        }

        // Search AJAX
        let timeout = null;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(timeout);
            let query = this.value;
            timeout = setTimeout(() => {
                fetch(`{{ route('kh.index') }}?search=${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('table-customer-container').innerHTML = html;
                    });
            }, 300);
        });
    </script>
@endsection
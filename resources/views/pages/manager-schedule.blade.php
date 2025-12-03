@extends('layouts.admin')

@section('content')
    <div x-data="scheduler()" x-init="initScheduler()"
        class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

        {{-- HEADER & TOOLBAR --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <span class="p-2 bg-indigo-600 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
                Lập Lịch Chuyên Nghiệp
            </h1>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Filter Nhân viên --}}
                <div class="relative">
                    <select x-model="filterEmployee" @change="fetchSchedule()"
                        class="pl-3 pr-8 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none shadow-sm font-medium text-sm">
                        <option value="all">Toàn bộ nhân viên</option>
                        @foreach ($nhanviens as $nv)
                            <option value="{{ $nv->MANV }}">{{ $nv->HO }} {{ $nv->TEN }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tuần --}}
                <div class="flex items-center bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow-sm">
                    <button @click="prevWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-l-lg"><svg
                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg></button>
                    <span class="px-4 font-bold w-40 text-center text-sm" x-text="dateRangeLabel"></span>
                    <button @click="nextWeek()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-r-lg"><svg
                            class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg></button>
                </div>

                <button @click="goToToday()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md text-sm font-bold">Hôm
                    nay</button>
            </div>
        </div>

        {{-- LỊCH GRID --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Header Ngày --}}
            <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <template x-for="(day, index) in weekDays" :key="index">
                    <div class="py-4 text-center border-r border-gray-200 dark:border-gray-700 last:border-r-0"
                        :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600': isToday(day.dateObj) }">
                        <div class="text-xs font-bold uppercase tracking-wider opacity-70" x-text="day.name"></div>
                        <div class="text-2xl font-extrabold mt-1" x-text="day.dayNum"></div>
                    </div>
                </template>
            </div>

            {{-- Body Lịch (Phân chia Sáng/Chiều/Tối) --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700">

                {{-- Helper component cho từng dòng Buổi --}}
                <template x-for="session in ['morning', 'afternoon', 'evening']">
                    <div class="grid grid-cols-7 min-h-[180px]">
                        <template x-for="(day, dIndex) in weekDays" :key="dIndex">
                            <div
                                class="border-r border-gray-200 dark:border-gray-700 p-2 relative group transition hover:bg-gray-50 dark:hover:bg-gray-700/30">

                                {{-- Label Buổi mờ mờ --}}
                                <div x-show="dIndex === 0"
                                    class="absolute top-2 left-2 text-[10px] font-bold uppercase text-gray-400 dark:text-gray-500 tracking-widest pointer-events-none"
                                    x-text="getSessionName(session)"></div>

                                {{-- Nút Thêm (+) khi hover --}}
                                <button @click="openModal(day.fullDate, session)"
                                    class="absolute inset-0 z-0 w-full h-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 flex items-center justify-center shadow-sm hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                </button>

                                {{-- Danh sách thẻ Lịch --}}
                                <div class="space-y-2 relative z-10 mt-4">
                                    <template x-for="shift in getShifts(day.fullDate, session)" :key="shift.id">
                                        <div @click="openEditModal(shift)"
                                            class="bg-white dark:bg-gray-800 border border-l-4 border-gray-200 dark:border-gray-600 rounded-lg p-2 shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition duration-200 group/card relative overflow-hidden"
                                            :class="{
                                                    'border-l-green-500': session === 'morning',
                                                    'border-l-orange-500': session === 'afternoon',
                                                    'border-l-purple-500': session === 'evening'
                                                }">

                                            {{-- Card Header: Giờ --}}
                                            <div class="flex justify-between items-center mb-1">
                                                <span
                                                    class="text-[10px] font-mono font-bold bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-300">
                                                    <span x-text="shift.gio_bat_dau"></span>-<span
                                                        x-text="shift.gio_ket_thuc"></span>
                                                </span>
                                                <span class="text-[10px] font-bold text-indigo-500"
                                                    x-text="shift.tenca"></span>
                                            </div>

                                            {{-- Card Body: Nhân viên --}}
                                            <div class="flex items-center gap-2">
                                                {{-- Avatar giả lập --}}
                                                <div
                                                    class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 text-white flex items-center justify-center text-[10px] font-bold shadow-sm">
                                                    <span x-text="getInitials(shift.ten_nv)"></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-xs font-bold text-gray-800 dark:text-white truncate"
                                                        x-text="shift.ten_nv"></div>

                                                    {{-- Hiển thị lỗi nếu có --}}
                                                    <template x-if="shift.tong_tien_phat > 0">
                                                        <div
                                                            class="text-[10px] text-red-600 font-bold truncate flex items-center gap-1">
                                                            <span
                                                                class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                            Phạt: -<span x-text="formatMoney(shift.tong_tien_phat)"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!shift.tong_tien_phat || shift.tong_tien_phat == 0">
                                                        <div class="text-[10px] text-gray-500 truncate"
                                                            x-text="shift.chuc_vu"></div>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>
                                    </template>
                                </div>

                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- MODAL CHUNG (THÊM / SỬA / ĐỔI) --}}
        {{-- MODAL CHUNG (THÊM / SỬA / ĐỔI) --}}
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="isModalOpen = false">
            </div>

            {{-- Modal Content: Rộng 70% (md:w-[70%]) --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl w-[95%] md:w-[70%] max-w-7xl z-10 shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Modal Header --}}
                <div
                    class="px-8 py-5 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <span x-show="!isEditMode">Phân ca làm việc mới</span>
                            <span x-show="isEditMode">Chi tiết / Cập nhật lịch</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Quản lý thông tin ca làm, nhân sự và ghi nhận vi phạm.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span x-show="isEditMode"
                            class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full font-bold border border-yellow-200">Chế
                            độ chỉnh sửa</span>
                        <button @click="isModalOpen = false"
                            class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-200 rounded-full transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body: Chia 2 cột (Grid) --}}
                <div class="p-8 overflow-y-auto custom-scroll flex-1">

                    {{-- Thông tin Ngày & Buổi (Full width) --}}
                    <div
                        class="flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider">
                                    Ngày làm việc</div>
                                <div class="font-extrabold text-blue-700 dark:text-blue-300 text-xl"
                                    x-text="formatDateVN(form.date)"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider">Buổi
                            </div>
                            <div class="font-extrabold text-gray-800 dark:text-white text-xl uppercase"
                                x-text="getSessionName(form.session)"></div>
                        </div>
                    </div>

                    {{-- GRID LAYOUT: Chia 2 cột --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        {{-- CỘT TRÁI: NHÂN VIÊN & CA LÀM --}}
                        <div class="space-y-6">
                            {{-- Chọn Nhân viên --}}
                            <div>
                                <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">1. Chọn Nhân
                                    viên</label>
                                <select x-model="form.manv"
                                    class="w-full p-3 border border-gray-300 rounded-xl bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 transition shadow-sm">
                                    <option value="">-- Vui lòng chọn nhân viên --</option>
                                    @if(isset($nhanviens))
                                        @foreach($nhanviens as $nv)
                                            <option value="{{ $nv->MANV }}">
                                                {{ $nv->HO }} {{ $nv->TEN }} ({{ $nv->chucVu->TENCV ?? 'NV' }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <p x-show="isEditMode" class="text-xs text-orange-600 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                    </svg>
                                    Bạn có thể chọn nhân viên khác để đổi lịch này cho họ.
                                </p>
                            </div>

                            {{-- Chọn Ca --}}
                            <div>
                                <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">2. Chọn Ca làm
                                    việc</label>
                                <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto custom-scroll pr-1">
                                    @if(isset($calamviecs) && count($calamviecs) > 0)
                                        @foreach($calamviecs as $ca)
                                            <label
                                                class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition group relative overflow-hidden"
                                                :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 ring-1 ring-indigo-500 shadow-md': form.maca == '{{ $ca->MACA }}', 'border-gray-200 dark:border-gray-700': form.maca != '{{ $ca->MACA }}'}">

                                                <div class="flex items-center gap-4 relative z-10">
                                                    <div class="flex items-center justify-center w-5 h-5 rounded-full border border-gray-400"
                                                        :class="{'border-indigo-600 bg-indigo-600': form.maca == '{{ $ca->MACA }}'}">
                                                        <div x-show="form.maca == '{{ $ca->MACA }}'"
                                                            class="w-2 h-2 bg-white rounded-full"></div>
                                                    </div>
                                                    <input type="radio" name="maca" value="{{ $ca->MACA }}" x-model="form.maca"
                                                        class="hidden">

                                                    <div>
                                                        <div class="font-bold text-base text-gray-800 dark:text-gray-200">
                                                            {{ $ca->TENCA }}</div>
                                                        <div
                                                            class="text-xs font-mono text-gray-500 dark:text-gray-400 group-hover:text-gray-700 mt-0.5 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            {{ \Carbon\Carbon::parse($ca->GIOBATDAU)->format('H:i') }} -
                                                            {{ \Carbon\Carbon::parse($ca->GIOKETTHUC)->format('H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    @else
                                        <div class="text-sm text-gray-500 text-center py-4 border border-dashed rounded-xl">Chưa
                                            cấu hình ca làm việc.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- CỘT PHẢI: GHI CHÚ & VI PHẠM --}}
                        <div class="space-y-6">

                            {{-- Ghi chú --}}
                            <div>
                                <label class="block text-sm font-bold mb-2 text-gray-700 dark:text-gray-300">Ghi chú công
                                    việc</label>
                                <textarea x-model="form.ghichu"
                                    class="w-full p-3 border border-gray-300 rounded-xl dark:bg-gray-800 dark:border-gray-700 text-sm focus:ring-2 focus:ring-indigo-500 transition"
                                    rows="3" placeholder="Nhập ghi chú, dặn dò cho ca làm này..."></textarea>
                            </div>

                            {{-- Khu vực Vi Phạm --}}
                            <div
                                class="bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-800/30 overflow-hidden">
                                <div
                                    class="px-5 py-3 bg-red-100/50 dark:bg-red-900/20 border-b border-red-100 dark:border-red-800/30 flex justify-between items-center">
                                    <label class="text-sm font-bold text-red-700 dark:text-red-400 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Ghi nhận lỗi / Phạt
                                    </label>
                                    <button @click="addViolation()" type="button"
                                        class="text-xs flex items-center gap-1 bg-white dark:bg-gray-800 text-red-600 px-3 py-1.5 rounded-lg shadow-sm hover:shadow transition border border-red-200 font-bold">
                                        + Thêm lỗi
                                    </button>
                                </div>

                                <div class="p-4 space-y-3">
                                    <template x-for="(v, index) in form.violations" :key="index">
                                        <div class="flex gap-2 items-start animate-fade-in-down">
                                            <div class="flex-1">
                                                <input type="text" x-model="v.noidung" placeholder="Lý do (VD: Đi trễ 15p)"
                                                    class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder-gray-400">
                                            </div>
                                            <div class="w-32 relative">
                                                <input type="number" x-model="v.sotien" placeholder="Tiền phạt"
                                                    class="w-full p-2 pr-6 border rounded-lg dark:bg-gray-800 dark:border-gray-600 text-sm font-bold text-red-600 text-right focus:border-red-500 placeholder-gray-400">
                                                <span
                                                    class="absolute right-2 top-2 text-xs text-gray-400 font-bold">đ</span>
                                            </div>
                                            <button @click="removeViolation(index)" type="button"
                                                class="p-2 text-gray-400 hover:text-red-500 transition bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    <div x-show="form.violations.length > 0"
                                        class="flex justify-between items-center pt-3 mt-2 border-t border-red-200 dark:border-red-800/30">
                                        <span class="text-xs text-gray-500">Tổng tiền phạt:</span>
                                        <span class="text-red-600 font-extrabold text-base">
                                            -<span x-text="formatMoney(calculateTotalPenalty())"></span> VNĐ
                                        </span>
                                    </div>

                                    <div x-show="form.violations.length === 0" class="text-center py-4">
                                        <p class="text-xs text-gray-400 italic">Chưa có vi phạm nào được ghi nhận.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div
                    class="px-8 py-5 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center shrink-0">
                    {{-- Nút Xóa --}}
                    <div>
                        <button x-show="isEditMode" @click="deleteShift()"
                            class="text-red-600 hover:text-white hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition border border-transparent hover:border-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Xóa lịch
                        </button>
                    </div>

                    <div class="flex gap-3">
                        <button @click="isModalOpen = false"
                            class="px-5 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-100 transition font-medium text-sm">
                            Hủy bỏ
                        </button>
                        <button @click="submitForm()"
                            class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl hover:from-indigo-700 hover:to-blue-700 transition font-bold text-sm shadow-lg shadow-indigo-500/30 flex items-center gap-2">
                            <svg x-show="!isEditMode" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <svg x-show="isEditMode" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="isEditMode ? 'Lưu thay đổi' : 'Tạo lịch làm việc'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- SCRIPT --}}
    <script>
        const employees = @json($nhanviens ?? []);

        function scheduler() {
            return {
                currentDate: new Date(),
                weekDays: [],
                dateRangeLabel: '',
                filterEmployee: 'all',
                schedules: [],

                isModalOpen: false,
                isEditMode: false,

                // Form object (Thêm mảng violations)
                form: {
                    id: null, manv: '', maca: '', date: '', session: '', ghichu: '',
                    violations: []
                },

                initScheduler() {
                    this.generateWeek();
                    this.fetchSchedule();
                },

                generateWeek() {
                    const curr = new Date(this.currentDate);
                    const first = curr.getDate() - curr.getDay() + 1;
                    const monday = new Date(curr.setDate(first));

                    this.weekDays = [];
                    for (let i = 0; i < 7; i++) {
                        let day = new Date(monday);
                        day.setDate(monday.getDate() + i);
                        this.weekDays.push({
                            name: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'][day.getDay()],
                            dayNum: day.getDate(),
                            fullDate: day.toISOString().split('T')[0],
                            dateObj: day
                        });
                    }
                    const s = this.weekDays[0].dateObj;
                    const e = this.weekDays[6].dateObj;
                    this.dateRangeLabel = `Tuần: ${s.getDate()}/${s.getMonth() + 1} - ${e.getDate()}/${e.getMonth() + 1}/${e.getFullYear()}`;
                },

                // Fetch API
                fetchSchedule() {
                    const start = this.weekDays[0].fullDate;
                    const end = this.weekDays[6].fullDate;
                    fetch(`/nhan-vien/lap-lich/get-data?start=${start}&end=${end}&manv=${this.filterEmployee}`)
                        .then(res => res.json())
                        .then(data => { this.schedules = data; });
                },

                // Lọc hiển thị
                getShifts(dateStr, session) {
                    return this.schedules.filter(s => s.date === dateStr && s.session === session);
                },

                // Mở Modal THÊM MỚI
                openModal(dateStr, session) {
                    this.isEditMode = false;
                    this.form = {
                        id: null,
                        manv: this.filterEmployee !== 'all' ? this.filterEmployee : '',
                        maca: '',
                        date: dateStr,
                        session: session,
                        ghichu: '',
                        violations: [] // Reset
                    };
                    this.isModalOpen = true;
                },

                // Mở Modal SỬA / ĐỔI
                openEditModal(shift) {
                    this.isEditMode = true;
                    this.form = {
                        id: shift.id,
                        manv: shift.manv,
                        maca: shift.maca,
                        date: shift.date,
                        session: shift.session,
                        ghichu: shift.ghichu || '',
                        // Map dữ liệu lỗi từ API vào form
                        violations: shift.ds_loi ? shift.ds_loi.map(l => ({
                            noidung: l.NOIDUNG,
                            sotien: l.SOTIEN
                        })) : []
                    };
                    this.isModalOpen = true;
                },

                // --- CÁC HÀM MỚI CHO VI PHẠM ---
                addViolation() {
                    this.form.violations.push({ noidung: '', sotien: '' });
                },
                removeViolation(index) {
                    this.form.violations.splice(index, 1);
                },
                calculateTotalPenalty() {
                    return this.form.violations.reduce((sum, v) => sum + Number(v.sotien), 0);
                },

                // Submit Form (Cập nhật body gửi đi)
                submitForm() {
                    if (!this.form.manv || !this.form.maca) {
                        alert('Vui lòng chọn đầy đủ thông tin!'); return;
                    }

                    fetch('/nhan-vien/lap-lich/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id: this.form.id,
                            MANV: this.form.manv,
                            MACA: this.form.maca,
                            NGAYLAM: this.form.date,
                            GHICHU: this.form.ghichu,

                            // Gửi danh sách lỗi lên server (Controller đã viết logic nhận cái này)
                            DANH_SACH_LOI: this.form.violations
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                this.isModalOpen = false;
                                this.fetchSchedule(); // Reload lại bảng
                            } else {
                                alert(data.message);
                            }
                        });
                },

                // Xóa
                deleteShift() {
                    if (!confirm('Xác nhận xóa lịch làm việc này?')) return;
                    fetch(`/nhan-vien/lap-lich/delete/${this.form.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.isModalOpen = false;
                            this.fetchSchedule();
                        });
                },

                // Helpers
                prevWeek() { this.currentDate.setDate(this.currentDate.getDate() - 7); this.initScheduler(); },
                nextWeek() { this.currentDate.setDate(this.currentDate.getDate() + 7); this.initScheduler(); },
                goToToday() { this.currentDate = new Date(); this.initScheduler(); },
                isToday(d) { const t = new Date(); return d.toDateString() === t.toDateString(); },

                getSessionName(s) {
                    return { 'morning': 'Buổi Sáng', 'afternoon': 'Buổi Chiều', 'evening': 'Buổi Tối' }[s] || s;
                },
                formatDateVN(s) { if (!s) return ''; const [y, m, d] = s.split('-'); return `${d}/${m}/${y}`; },
                getInitials(name) {
                    if (!name) return '';
                    const parts = name.split(' ');
                    return (parts.length > 1) ? parts[parts.length - 2][0] + parts[parts.length - 1][0] : name[0];
                },
                formatMoney(amount) {
                    return new Intl.NumberFormat('vi-VN').format(amount);
                }
            }
        }
    </script>
@endsection
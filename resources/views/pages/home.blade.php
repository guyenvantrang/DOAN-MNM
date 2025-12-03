@extends('layouts.admin')

@section('title', 'Trang chủ quản lý cửa hàng')

@section('content')
  <div class="bg-gray-50 dark:bg-gray-900 min-h-screen p-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">
      Trang chủ quản lý cửa hàng
    </h1>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 max-w-6xl mx-auto">

      {{-- Quản lý hàng hóa --}}
      <a href="{{ route('ql_sanpham') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path
              d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.73z" />
            <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
            <line x1="12" y1="22.08" x2="12" y2="12" />
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Quản lý hàng hóa</h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Quản lý nhập hàng và kiểm tra hàng hóa từ nhà cung cấp.
        </p>
      </a>

      {{-- Quản lý nhân viên --}}
      <a href="{{ route('nv.index') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 dark:border-gray-700 group">

        <div class="flex items-center mb-4">
          {{-- Nền icon màu xanh nhạt cho nổi bật --}}
          <div
            class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl group-hover:bg-green-100 dark:group-hover:bg-green-900/40 transition-colors">
            {{-- Icon Users (Nhóm người) --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-green-600 transition-colors">
          Quản lý nhân viên
        </h2>

        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
          Quản lý hồ sơ nhân sự, phân quyền chức vụ và sắp xếp lịch làm việc.
        </p>
      </a>

      {{-- Quản lý Khách hàng (MỚI THÊM) --}}
      <a href="{{ route('kh.index') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 dark:border-gray-700 group">

        <div class="flex items-center mb-4">
          {{-- Nền icon màu hồng --}}
          <div
            class="p-3 bg-pink-50 dark:bg-pink-900/20 rounded-xl group-hover:bg-pink-100 dark:group-hover:bg-pink-900/40 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-pink-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-pink-600 transition-colors">
          Quản lý Khách hàng
        </h2>

        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
          Tra cứu thông tin, xem lịch sử mua hàng và quản lý tài khoản khách hàng.
        </p>
      </a>
      {{-- Quản lý Đơn hàng (MỚI THÊM) --}}
      <a href="{{ route('dh.index') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 dark:border-gray-700 group">

        <div class="flex items-center mb-4">
          {{-- Nền icon màu cam --}}
          <div
            class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl group-hover:bg-orange-100 dark:group-hover:bg-orange-900/40 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 transition-colors">
          Quản lý Đơn hàng
        </h2>

        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
          Xử lý đơn đặt hàng online, xác nhận, giao vận và cập nhật trạng thái.
        </p>
      </a>
      {{-- Quản lý Khuyến mãi (MỚI THÊM) --}}
      <a href="{{ route('km.index') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 dark:border-gray-700 group">

        <div class="flex items-center mb-4">
          {{-- Nền icon màu vàng --}}
          <div
            class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/40 transition-colors">
            {{-- Icon Ticket/Coupon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
          </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-yellow-600 transition-colors">
          Quản lý Khuyến mãi
        </h2>

        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
          Tạo mã giảm giá, chương trình sale và voucher quà tặng.
        </p>
      </a>

      {{-- Thống kê doanh thu --}}
      <a href="{{ route('ql_thong_ke') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <line x1="12" y1="20" x2="12" y2="10" />
            <line x1="18" y1="20" x2="18" y2="4" />
            <line x1="6" y1="20" x2="6" y2="16" />
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Thống kê doanh thu</h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Xem biểu đồ doanh thu theo ngày, tháng, năm.
        </p>
      </a>

      {{-- Phân quyền truy cập --}}
      <a href="{{ route('ql_phan_quyen') }}"
        class="block bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6">
        <div class="flex items-center mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Phân quyền truy cập</h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Cấp quyền, quản lý tài khoản và phân vai trò cho nhân viên.
        </p>
      </a>

    </div>
  </div>
@endsection
@extends('layouts.admin')

@section('content')
<div x-data="orderManager()" x-init="init()" class="p-6 w-full min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <span class="p-2 bg-orange-500 rounded-lg text-white shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </span>
            Quản lý Đơn hàng
        </h1>
        {{-- Search --}}
        <div class="relative">
            <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchOrders()" placeholder="Tìm mã đơn, SĐT..." class="pl-10 pr-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-orange-500 outline-none shadow-sm w-64">
            <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    {{-- TABS TRẠNG THÁI --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
        @php
            $tabs = [
                'all' => 'Tất cả',
                '0' => 'Chờ xác nhận',
                '1' => 'Đang chuẩn bị',
                '2' => 'Đang giao hàng',
                '3' => 'Hoàn thành',
                '4' => 'Đã hủy',
                '5' => 'Trả hàng/Lỗi'
            ];
        @endphp
        @foreach($tabs as $key => $label)
            <button @click="currentTab = '{{ $key }}'; fetchOrders()" 
                    :class="currentTab === '{{ $key }}' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- DANH SÁCH ĐƠN HÀNG (Load Partial) --}}
    <div id="order-table-container" class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[300px]">
        @include('pages.manager-page-product.components.order_table')
    </div>

    {{-- MODAL CHI TIẾT ĐƠN HÀNG --}}
    @include('components.message-box.order-detail-modal')

</div>

<script>
    function orderManager() {
        return {
            currentTab: 'all',
            searchQuery: '',
            
            // Modal logic
            isModalOpen: false,
            selectedOrder: null,
            
            // Form xử lý (ship, cancel)
            shipperId: '',
            trackingCode: '',
            cancelReason: '',

            init() {
                // Init logic if needed
            },

            fetchOrders() {
                const container = document.getElementById('order-table-container');
                container.style.opacity = '0.5';
                
                fetch(`{{ route('dh.index') }}?status=${this.currentTab}&search=${this.searchQuery}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                });
            },

            openDetail(id) {
                // Reset form data
                this.shipperId = '';
                this.trackingCode = '';
                this.cancelReason = '';

                fetch(`/quan-ly-don-hang/chi-tiet/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        this.selectedOrder = data;
                        this.isModalOpen = true;
                    });
            },

            // Hàm xử lý hành động (Duyệt, Giao, Hủy, Hoàn thành)
            updateStatus(action) {
                if (!confirm('Bạn có chắc chắn muốn thực hiện thao tác này?')) return;

                const payload = {
                    action: action,
                    _token: '{{ csrf_token() }}' // CSRF Token trực tiếp hoặc lấy từ meta
                };

                // Nếu giao hàng thì cần thêm ID Đơn vị vận chuyển
                if (action === 'ship') {
                    if (!this.shipperId) { alert('Vui lòng chọn đơn vị vận chuyển!'); return; }
                    payload.MADVVC = this.shipperId;
                    payload.TRACKING_CODE = this.trackingCode;
                }

                // Nếu hủy hoặc lỗi thì cần lý do
                if (action === 'cancel' || action === 'fail') {
                    if (!this.cancelReason) { 
                        // Nếu chưa nhập ở form thì prompt
                        const reason = prompt("Nhập lý do:");
                        if(!reason) return;
                        payload.LYDO = reason;
                    } else {
                        payload.LYDO = this.cancelReason;
                    }
                }

                fetch(`/quan-ly-don-hang/cap-nhat/${this.selectedOrder.MADH}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        this.isModalOpen = false;
                        this.fetchOrders(); // Reload list
                    } else {
                        alert(data.message);
                    }
                });
            },
            
            formatMoney(amount) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
            },
            formatDate(dateString) {
                if(!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleString('vi-VN');
            }
        }
    }
</script>
@endsection
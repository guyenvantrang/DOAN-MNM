@extends('layouts.admin')

@section('content')
    <div class="p-6 w-full">

        <h1 class="text-2xl font-semibold mb-6">Quản lý khối lượng đồng hồ</h1>

        {{-- Tìm kiếm + lọc ngày + reset --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6 items-start sm:items-center">
            <div class="flex-1 flex gap-3">
                <div class="relative w-full sm:w-1/3">
                    <input type="text" id="search" data-url-search="{{ url('khoiluong/kl/timkiemtheomavaten') }}"
                        data-url-date="{{ url('khoiluong/kl/timkiemtheongay') }}"
                        data-url-all="{{ url('khoiluong/kl/hienthitatca') }}" placeholder="Tìm kiếm khối lượng..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-800 text-gray-100 placeholder-gray-400 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex gap-2 items-center">
                    <label class="text-gray-200 text-sm">Từ:</label>
                    <input type="date" id="date_from"
                        class="px-2 py-1 border border-gray-700 rounded bg-gray-800 text-gray-100" />
                    <label class="text-gray-200 text-sm">Đến:</label>
                    <input type="date" id="date_to"
                        class="px-2 py-1 border border-gray-700 rounded bg-gray-800 text-gray-100" />
                    <button id="resetBtn"
                        class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-500 transition">Reset</button>
                </div>
            </div>

            {{-- Nút mở modal thêm khối lượng --}}
            <div x-data="{ modalOpen: false }">
                <button @click="modalOpen=true"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm khối lượng</span>
                </button>

                {{-- Modal thêm khối lượng --}}
                @include('components.message-box.parameters-weight-add')
            </div>
        </div>

        {{-- Bảng khối lượng --}}
        <div x-data="categoryTable()" class="overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left text-gray-100">
                <thead class="text-xs uppercase bg-gray-700 text-gray-200">
                    <tr>
                        <th class="px-4 py-3 cursor-pointer select-none" data-column="MKL" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Mã KL</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="CHISO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Chỉ số</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="DONVIDO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Đơn vị đo</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" data-column="MOTA" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Mô tả</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 cursor-pointer select-none" data-column="NGAYTAO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Ngày tạo</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 flex-row inline-flex items-center justify-center gap-2 whitespace-nowrap">
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody id="category-table" class="bg-gray-800">
                    @include('pages.manager-page-product.components.parameters-weight-table', ['khoiluong' => $khoiluong])
                </tbody>
            </table>
        </div>

    </div>
@endsection

<script>
    function sortColumn(el) {
        let column = el.dataset.column;
        let direction = el.dataset.direction;

        fetch(`{{ route('kl.sapxep') }}?column=${column}&direction=${direction}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('category-table').innerHTML = html;

                // Toggle hướng sắp xếp cho lần click tiếp theo
                el.dataset.direction = direction === 'asc' ? 'desc' : 'asc';

                // Update arrow
                document.querySelectorAll('.sort-arrow').forEach(a => a.textContent = '');
                el.querySelector('.sort-arrow').textContent = direction === 'asc' ? '↑' : '↓';
            });
    }
</script>

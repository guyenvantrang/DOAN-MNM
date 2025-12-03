@extends('layouts.admin')

@section('content')
    <div class="p-6 w-full">

        <h1 class="text-2xl font-semibold mb-6">Quản lý độ dày đồng hồ</h1>

        {{-- Tìm kiếm + lọc ngày + reset --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-6 items-start sm:items-center">
            <div class="flex-1 flex gap-3">
                <div class="relative w-full sm:w-1/3">
                    <input type="text" id="search"
                        data-url-search="{{ url('doday/doday_sp/timkiemtheomavaten') }}"
                        data-url-date="{{ url('doday/doday_sp/timkiemtheongay') }}"
                        data-url-all="{{ url('doday/doday_sp/hienthitatca') }}"
                        placeholder="Tìm kiếm độ dày theo mã hoặc mô tả..."
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

            {{-- Nút mở modal thêm độ dày --}}
            <div x-data="{ modalOpen: false }">
                <button @click="modalOpen=true"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Thêm độ dày</span>
                </button>

                {{-- Modal thêm độ dày --}}
                @include('components.message-box.parameters-thickness-add')
            </div>
        </div>

        {{-- Bảng độ dày --}}
        <div x-data="categoryTable()" class="overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left text-gray-100">
                <thead class="text-xs uppercase bg-gray-700 text-gray-200">
                    <tr>
                        <th class="px-4 py-3 cursor-pointer select-none" data-column="MADDY" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h18v18H3V3z" />
                                </svg>
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Mã độ dày</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="CHISO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Chỉ số</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="DONVIDO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Đơn vị đo</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="MOTA" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Mô tả</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 cursor-pointer select-none" data-column="NGAYTAO" data-direction="asc"
                            onclick="sortColumn(this)">
                            <div class="inline-flex items-center gap-2 group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-gray-200 group-hover:text-blue-400 transition-colors">Ngày tạo</span>
                                <span class="sort-arrow inline-block transform transition-transform duration-300 text-gray-400">↑</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 flex-row inline-flex items-center justify-center gap-2 whitespace-nowrap">
                            <div class="inline-flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Hành động
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody id="category-table" class="bg-gray-800">
                    @include('pages.manager-page-product.components.parameters-thickness-table', ['dodays' => $dodays])
                </tbody>
            </table>
        </div>
    </div>
@endsection

<script>
    function sortColumn(el) {
        let column = el.dataset.column;
        let direction = el.dataset.direction;

        fetch(`{{ route('doday-sp.sapxeptheoid') }}?column=${column}&direction=${direction}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('category-table').innerHTML = html;

                el.dataset.direction = direction === 'asc' ? 'desc' : 'asc';

                document.querySelectorAll('.sort-arrow').forEach(a => a.textContent = '');
                el.querySelector('.sort-arrow').textContent = direction === 'asc' ? '↑' : '↓';
            });
    }
</script>

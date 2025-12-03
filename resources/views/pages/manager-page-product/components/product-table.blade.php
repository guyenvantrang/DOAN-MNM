<div class="relative">
    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300" id="table-sanpham">
        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
            <tr>
                <th scope="col" class="px-6 py-3">Hình ảnh</th>
                <th scope="col" class="px-6 py-3">Mã & Tên</th>
                <th scope="col" class="px-6 py-3">Giá nhập</th>
                <th scope="col" class="px-6 py-3">Giá bán</th>
                <th scope="col" class="px-6 py-3 text-center">Tồn kho</th>
                <th scope="col" class="px-6 py-3">Ngày tạo</th>
                <th scope="col" class="px-6 py-3 text-center">Hành động</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sanphams as $item)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                    {{-- Hình ảnh --}}
                    <td class="px-6 py-4">
                        <div class="relative h-14 w-14">
                            <img src="{{ asset($item->HINHANHCHINH && file_exists(public_path($item->HINHANHCHINH)) ? $item->HINHANHCHINH : 'images/default-product.png') }}"
                                class="w-full h-full object-cover rounded-lg shadow-sm border border-gray-200 dark:border-gray-600"
                                alt="{{ $item->TENSP }}">
                        </div>
                    </td>

                    {{-- Tên sản phẩm --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white">{{ $item->TENSP }}</span>
                            <span class="text-xs text-gray-500 font-mono">{{ $item->MASP }}</span>
                        </div>
                    </td>

                    {{-- Giá --}}
                    <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">
                        {{ number_format($item->GIANHAP) }} ₫
                    </td>
                    <td class="px-6 py-4 font-mono font-semibold text-blue-600 dark:text-blue-400">
                        {{ number_format($item->GIABAN) }} ₫
                    </td>

                    {{-- Tồn kho (Badge màu) --}}
                    <td class="px-6 py-4 text-center">
                        @if($item->SOLUONGTON > 10)
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                {{ $item->SOLUONGTON }}
                            </span>
                        @elseif($item->SOLUONGTON > 0)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                {{ $item->SOLUONGTON }}
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                Hết hàng
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($item->NGAYTAO)->format('d/m/Y') }}
                    </td>

                    {{-- Hành động --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-2">
                            
                            {{-- Nút Sửa (Gọi AJAX) --}}
                            {{-- Lưu ý: Route này phải trỏ đến hàm trả về View Modal --}}
                            <button type="button" 
                                onclick="loadEditForm('{{ route('sp.timtheoid', $item->MASP) }}')"
                                class="flex items-center gap-1 px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-sm transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.83a2 2 0 01-.828.497l-4 1a1 1 0 01-1.213-1.213l1-4a2 2 0 01.497-.828z" />
                                </svg>
                                <span>Sửa</span>
                            </button>

                            {{-- Nút Xóa --}}
                            <form action="{{ route('sp.xoa', $item->MASP) }}" method="POST" 
                                  onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa sản phẩm {{ $item->MASP }} không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="flex items-center gap-1 px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-md text-sm transition border border-red-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Xóa</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span class="text-lg font-medium">Không tìm thấy sản phẩm nào</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        
        {{-- Phân trang --}}
        @if($sanphams->hasPages())
            <tfoot>
                <tr>
                    <td colspan="7" class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-t dark:border-gray-700 rounded-b-lg">
                        {{ $sanphams->links('pagination::tailwind') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

{{-- FILE: product-table.blade.php --}}

<script>
    function loadEditForm(url) {
        // 1. Xóa Modal cũ nếu đang tồn tại (tránh trùng lặp ID hoặc Class)
        // Tìm theo class định danh mà chúng ta sẽ thêm vào Modal ở bước 2
        const existingModal = document.querySelector('.modal-edit-container');
        if (existingModal) {
            existingModal.remove();
        }

        // 2. Gọi AJAX lấy HTML
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải dữ liệu. Lỗi: ' + response.status);
                return response.text();
            })
            .then(html => {
                // 3. Chèn Modal mới vào cuối thẻ <body>
                document.body.insertAdjacentHTML('beforeend', html);

                // 4. QUAN TRỌNG: Kích hoạt Alpine.js cho nội dung mới
                // Lấy phần tử vừa chèn (thường là phần tử cuối cùng của body)
                const newModal = document.body.lastElementChild;

                if (typeof Alpine !== 'undefined') {
                    // Lệnh này bắt buộc Alpine quét và chạy logic (x-data, x-show) cho HTML mới
                    Alpine.initTree(newModal);
                } else {
                    console.error('Lỗi: Alpine.js chưa được nạp vào trang web!');
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi tải form sửa sản phẩm. Vui lòng kiểm tra Console.');
            });
    }
</script>
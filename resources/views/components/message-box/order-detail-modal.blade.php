<div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="isModalOpen = false"></div>
    
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-4xl z-10 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        
        {{-- Header --}}
        <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                Chi tiết đơn hàng: <span class="text-blue-600" x-text="selectedOrder?.MADH"></span>
            </h3>
            <button @click="isModalOpen = false" class="text-gray-500 hover:text-red-500">✕</button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto custom-scroll flex-1" x-if="selectedOrder">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Thông tin người nhận --}}
                <div class="p-4 border rounded-lg dark:border-gray-700">
                    <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2 border-b pb-1">Thông tin giao hàng</h4>
                    <p><span class="text-gray-500">Người nhận:</span> <span class="font-medium" x-text="selectedOrder.TEN_NGUOINHAN"></span></p>
                    <p><span class="text-gray-500">SĐT:</span> <span class="font-medium" x-text="selectedOrder.SDT_NGUOINHAN"></span></p>
                    <p><span class="text-gray-500">Địa chỉ:</span> <span x-text="selectedOrder.DIACHI_GIAOHANG"></span></p>
                    <p class="mt-2"><span class="text-gray-500">Ghi chú:</span> <span class="italic text-sm" x-text="selectedOrder.GHICHU || 'Không có'"></span></p>
                </div>
                
                {{-- Thông tin trạng thái --}}
                <div class="p-4 border rounded-lg dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2 border-b pb-1">Trạng thái đơn</h4>
                    <p>Ngày đặt: <span x-text="formatDate(selectedOrder.NGAYDAT)"></span></p>
                    <p>Thanh toán: <span class="font-bold" :class="selectedOrder.TRANGTHAI_THANHTOAN == 1 ? 'text-green-600' : 'text-orange-600'" x-text="selectedOrder.TRANGTHAI_THANHTOAN == 1 ? 'Đã thanh toán' : 'COD (Chưa thanh toán)'"></span></p>
                    
                    {{-- Nếu đang giao hàng thì hiện thông tin vận chuyển --}}
                    <template x-if="selectedOrder.giao_hang">
                        <div class="mt-2 pt-2 border-t border-dashed">
                            <p>Đơn vị vận chuyển: <span class="font-bold text-blue-600" x-text="selectedOrder.giao_hang.don_vi_van_chuyen?.TENDVVC"></span></p>
                            <p>Mã vận đơn: <span class="font-mono bg-yellow-100 px-1" x-text="selectedOrder.giao_hang.MOTA_SUCO"></span></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Danh sách sản phẩm --}}
            <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-2">Danh sách sản phẩm</h4>
            <div class="overflow-x-auto border rounded-lg dark:border-gray-700 mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Sản phẩm</th>
                            <th class="px-4 py-2 text-center">Số lượng</th>
                            <th class="px-4 py-2 text-right">Đơn giá</th>
                            <th class="px-4 py-2 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="ct in selectedOrder.chi_tiet_don_hangs" :key="ct.MASP">
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="font-medium" x-text="ct.san_pham?.TENSP || ct.MASP"></div>
                                    <div class="text-xs text-gray-500" x-text="ct.MASP"></div>
                                </td>
                                <td class="px-4 py-2 text-center" x-text="ct.SOLUONG"></td>
                                <td class="px-4 py-2 text-right" x-text="formatMoney(ct.DONGIA)"></td>
                                <td class="px-4 py-2 text-right font-bold" x-text="formatMoney(ct.THANHTIEN)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-bold">Tổng tiền hàng:</td>
                            <td class="px-4 py-2 text-right font-bold" x-text="formatMoney(selectedOrder.TONGTIENHANG)"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right">Phí vận chuyển:</td>
                            <td class="px-4 py-2 text-right" x-text="formatMoney(selectedOrder.PHIVANCHUYEN)"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right text-lg font-bold text-red-600">Tổng thanh toán:</td>
                            <td class="px-4 py-2 text-right text-lg font-bold text-red-600" x-text="formatMoney(selectedOrder.TONGTHANHTOAN)"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            {{-- VÙNG TÁC VỤ (ACTION AREA) --}}
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <h4 class="font-bold mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    Xử lý đơn hàng
                </h4>
                
                {{-- Case 0: Chờ xác nhận -> Duyệt hoặc Hủy --}}
                <template x-if="selectedOrder.TRANGTHAI_DONHANG == 0">
                    <div class="flex gap-3">
                        <button @click="updateStatus('confirm')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-bold shadow">Xác nhận đơn & Chuẩn bị hàng</button>
                        <button @click="updateStatus('cancel')" class="px-6 bg-red-100 text-red-700 hover:bg-red-200 border border-red-300 rounded-lg font-bold">Hủy đơn</button>
                    </div>
                </template>

                {{-- Case 1: Đã xác nhận/Chuẩn bị -> Giao hàng --}}
                <template x-if="selectedOrder.TRANGTHAI_DONHANG == 1">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <select x-model="shipperId" class="p-2 border rounded dark:bg-gray-700">
                                <option value="">-- Chọn đơn vị vận chuyển --</option>
                                @foreach($donvivanchuyens as $dv)
                                    <option value="{{ $dv->MADVVC }}">{{ $dv->TENDVVC }}</option>
                                @endforeach
                            </select>
                            <input type="text" x-model="trackingCode" placeholder="Nhập mã vận đơn (nếu có)" class="p-2 border rounded dark:bg-gray-700">
                        </div>
                        <button @click="updateStatus('ship')" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-bold shadow">
                            Giao cho đơn vị vận chuyển
                        </button>
                    </div>
                </template>

                {{-- Case 2: Đang giao -> Hoàn thành hoặc Thất bại --}}
                <template x-if="selectedOrder.TRANGTHAI_DONHANG == 2">
                    <div class="flex gap-3">
                        <button @click="updateStatus('complete')" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-bold shadow">
                            Khách đã nhận hàng (Hoàn tất)
                        </button>
                        <button @click="updateStatus('fail')" class="px-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold">
                            Giao thất bại / Hoàn về
                        </button>
                    </div>
                </template>
                
                {{-- Case 3,4,5: Đã xong --}}
                <template x-if="selectedOrder.TRANGTHAI_DONHANG >= 3">
                    <p class="text-center text-gray-500 italic">Đơn hàng này đã kết thúc quy trình. Không thể thao tác thêm.</p>
                </template>

            </div>
        </div>
    </div>
</div>
<div x-data="{
        // 👇 THAY ĐỔI QUAN TRỌNG: Lấy dữ liệu trực tiếp từ biến PHP $loaisps
        types: {{ Js::from($loaisps) }}, 
        
        selectedTypes: [],       // mảng lưu MALOAI đã chọn
        
        toggleType(typeObj) {    // typeObj = {MALOAI, TENLOAI}
            const index = this.selectedTypes.findIndex(t => t === typeObj.MALOAI);
            if (index !== -1) {
                this.selectedTypes.splice(index, 1); // xóa nếu đã chọn
            } else {
                this.selectedTypes.push(typeObj.MALOAI); // thêm nếu chưa chọn
            }
            // Gửi sự kiện để bộ lọc cha chạy AJAX
            this.$dispatch('filter-change');
        },
        
        isSelected(maloai) {
            return this.selectedTypes.includes(maloai);
        }
        // ❌ ĐÃ XÓA hàm init() và fetch() vì không cần thiết nữa
    }" class="space-y-3 bg-gray-900 p-4 rounded-lg border border-gray-700">

    <label class="block text-sm font-medium text-gray-300 mb-1">Loại đồng hồ</label>

    <div class="grid grid-cols-2 gap-2">
        <template x-for="type in types" :key="type.MALOAI">
            <button type="button" @click="toggleType(type)"
                :class="isSelected(type.MALOAI)
                    ? 'bg-blue-600 border-blue-600 text-white'
                    : 'border-gray-600 text-gray-200 hover:bg-gray-700'"
                class="border rounded-md flex items-center justify-center px-3 py-2 transition">
                {{-- Icon demo (bạn có thể thay bằng icon động nếu có) --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="type.TENLOAI"></span>
            </button>
        </template>
    </div>

    <template x-if="selectedTypes.length">
        <div class="flex flex-wrap gap-2 mt-2">
            <template x-for="maloai in selectedTypes" :key="maloai">
                <span @click="toggleType(types.find(t => t.MALOAI === maloai))"
                      class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs cursor-pointer hover:bg-blue-700 transition flex items-center gap-1">
                    <span x-text="types.find(t => t.MALOAI === maloai).TENLOAI"></span> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </span>
            </template>
        </div>
    </template>

    <input type="hidden" name="category" :value="selectedTypes.join(',')">

</div>
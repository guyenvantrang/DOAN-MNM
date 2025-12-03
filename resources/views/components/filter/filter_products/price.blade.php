<div x-data="{
    min: 0,
    max: 50000000,
    minLimit: 0,
    maxLimit: 50000000,
    
    // Hàm định dạng tiền tệ (VD: 1.000.000)
    formatCurrency(value) {
        return new Intl.NumberFormat('vi-VN').format(value);
    },

    // Hàm xử lý khi kéo thanh trượt Min
    updateMin() {
        if (parseInt(this.min) > parseInt(this.max)) {
            this.min = this.max;
        }
        this.triggerFilter();
    },

    // Hàm xử lý khi kéo thanh trượt Max
    updateMax() {
        if (parseInt(this.max) < parseInt(this.min)) {
            this.max = this.min;
        }
        this.triggerFilter();
    },

    // Hàm chọn giá nhanh từ các nút
    setPriceRange(minVal, maxVal) {
        this.min = minVal;
        this.max = maxVal;
        this.triggerFilter();
    },

    // Gửi sự kiện để lọc
    triggerFilter() {
        // Dispatch sự kiện 'filter-change' để form cha (ở manager-product) bắt được
        this.$dispatch('filter-change');
    }
}" class="space-y-4 bg-gray-900 p-4 rounded-lg border border-gray-700">

    <label class="block text-sm font-medium text-gray-300">Khoảng giá (VNĐ)</label>

    <div class="flex justify-between text-sm text-gray-400">
        <div>
            <span>Từ:</span>
            <input type="text" :value="formatCurrency(min)" readonly
                class="w-24 bg-gray-800 border border-gray-700 text-gray-100 rounded-md text-right px-2 py-1 text-sm cursor-default focus:outline-none">
        </div>
        <div>
            <span>Đến:</span>
            <input type="text" :value="formatCurrency(max)" readonly
                class="w-24 bg-gray-800 border border-gray-700 text-gray-100 rounded-md text-right px-2 py-1 text-sm cursor-default focus:outline-none">
        </div>
    </div>

    <div class="relative w-full h-2 rounded-lg bg-gray-700">
        <div class="absolute h-2 bg-blue-600 rounded-lg pointer-events-none"
             :style="'left: ' + ((min / maxLimit) * 100) + '%; right: ' + (100 - (max / maxLimit) * 100) + '%'">
        </div>

        <input type="range" :min="minLimit" :max="maxLimit" step="500000" 
               x-model="min" @input="updateMin()"
               class="absolute w-full h-2 opacity-0 cursor-pointer pointer-events-auto z-20">
        
        <input type="range" :min="minLimit" :max="maxLimit" step="500000" 
               x-model="max" @input="updateMax()"
               class="absolute w-full h-2 opacity-0 cursor-pointer pointer-events-auto z-20">
        
        <div class="absolute w-4 h-4 bg-white rounded-full shadow border border-gray-300 top-1/2 transform -translate-y-1/2 pointer-events-none z-10"
             :style="'left: ' + ((min / maxLimit) * 100) + '%'"></div>
        <div class="absolute w-4 h-4 bg-white rounded-full shadow border border-gray-300 top-1/2 transform -translate-y-1/2 pointer-events-none z-10"
             :style="'left: ' + ((max / maxLimit) * 100) + '%'"></div>
    </div>

    <p class="text-sm text-gray-400 mt-2">
        Khoảng: 
        <span class="font-semibold text-gray-100">
            <span x-text="formatCurrency(min)"></span> - <span x-text="formatCurrency(max)"></span> VNĐ
        </span>
    </p>

    <div class="flex flex-wrap gap-2">
        <button type="button" @click="setPriceRange(0, 1000000)"
            :class="(min == 0 && max == 1000000) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'"
            class="text-sm px-3 py-1 rounded-md transition-all border border-gray-600">
            Dưới 1 triệu
        </button>
        <button type="button" @click="setPriceRange(1000000, 5000000)"
            :class="(min == 1000000 && max == 5000000) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'"
            class="text-sm px-3 py-1 rounded-md transition-all border border-gray-600">
            1 - 5 triệu
        </button>
        <button type="button" @click="setPriceRange(5000000, 10000000)"
            :class="(min == 5000000 && max == 10000000) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'"
            class="text-sm px-3 py-1 rounded-md transition-all border border-gray-600">
            5 - 10 triệu
        </button>
        <button type="button" @click="setPriceRange(10000000, 20000000)"
            :class="(min == 10000000 && max == 20000000) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'"
            class="text-sm px-3 py-1 rounded-md transition-all border border-gray-600">
            10 - 20 triệu
        </button>
        <button type="button" @click="setPriceRange(20000000, 50000000)"
            :class="(min == 20000000 && max == 50000000) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-200 hover:bg-gray-600'"
            class="text-sm px-3 py-1 rounded-md transition-all border border-gray-600">
            Trên 20 triệu
        </button>
        
        <button type="button" @click="setPriceRange(0, 50000000)" x-show="min > 0 || max < 50000000"
            class="text-xs text-red-400 underline hover:text-red-300 ml-auto">
            Đặt lại
        </button>
    </div>

    <input type="hidden" name="min_price" x-model="min">
    <input type="hidden" name="max_price" x-model="max">
</div>
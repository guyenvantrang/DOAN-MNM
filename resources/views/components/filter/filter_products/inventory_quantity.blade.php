<div 
    x-data="{ 
        stockType: '', 
        minStock: '', 
        maxStock: '',
        setStock(type) {
            if (this.stockType === type) {
                this.stockType = '';
                this.minStock = '';
                this.maxStock = '';
                return;
            }
            this.stockType = type;
            if (type === 'instock') { this.minStock = 6; this.maxStock = ''; }
            else if (type === 'low') { this.minStock = 1; this.maxStock = 5; }
            else if (type === 'out') { this.minStock = 0; this.maxStock = 0; }
            this.$dispatch('filter-change');
        },
        resetStock() {
            this.stockType = '';
            this.minStock = '';
            this.maxStock = '';
            this.$dispatch('filter-change');
        }
    }" 
    class="p-5 bg-gray-900 border border-gray-800 rounded-2xl shadow-lg space-y-5 w-full">

    <!-- Tiêu đề -->
    <div class="flex items-center gap-2 text-gray-100 font-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span>Số lượng tồn</span>
    </div>

    <!-- Grid 2x2 -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Tất cả -->
        <button type="button"
            @click="resetStock()"
            :class="stockType === '' 
                ? 'bg-gray-700 text-white border-gray-600 ring-2 ring-gray-500 shadow-inner' 
                : 'border border-gray-700 text-gray-200 hover:bg-gray-800 hover:border-gray-600'"
            class="flex flex-col items-center justify-center rounded-xl py-3 gap-2 text-sm font-medium transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span>Tất cả</span>
        </button>

        <!-- Còn hàng -->
        <button type="button"
            @click="setStock('instock')"
            :class="stockType === 'instock' 
                ? 'bg-green-600 text-white border-green-500 ring-2 ring-green-400 shadow-inner' 
                : 'border border-gray-700 text-gray-200 hover:bg-green-700/30 hover:border-green-500'"
            class="flex flex-col items-center justify-center rounded-xl py-3 gap-2 text-sm font-medium transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
            <span>Còn hàng</span>
        </button>

        <!-- Sắp hết -->
        <button type="button"
            @click="setStock('low')"
            :class="stockType === 'low' 
                ? 'bg-yellow-500 text-white border-yellow-400 ring-2 ring-yellow-300 shadow-inner' 
                : 'border border-gray-700 text-gray-200 hover:bg-yellow-700/20 hover:border-yellow-400'"
            class="flex flex-col items-center justify-center rounded-xl py-3 gap-2 text-sm font-medium transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Sắp hết (≤5)</span>
        </button>

        <!-- Hết hàng -->
        <button type="button"
            @click="setStock('out')"
            :class="stockType === 'out' 
                ? 'bg-red-600 text-white border-red-500 ring-2 ring-red-400 shadow-inner' 
                : 'border border-gray-700 text-gray-200 hover:bg-red-700/30 hover:border-red-500'"
            class="flex flex-col items-center justify-center rounded-xl py-3 gap-2 text-sm font-medium transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>Hết hàng</span>
        </button>
    </div>

    <!-- Khoảng nhập -->
    <div class="flex flex-col sm:flex-row gap-3">
        <input type="number" placeholder="Từ" x-model.number="minStock" readonly
            class="w-full sm:w-1/2 rounded-lg border border-gray-700 bg-gray-800 text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <input type="number" placeholder="Đến" x-model.number="maxStock" readonly
            class="w-full sm:w-1/2 rounded-lg border border-gray-700 bg-gray-800 text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
    </div>

    <!-- Hiển thị khoảng -->
    <template x-if="stockType">
        <p class="text-sm text-gray-400">
            Khoảng: 
            <span class="text-gray-100 font-semibold"
                x-text="(minStock || 0) + ' - ' + (maxStock || '∞') + ' sản phẩm'"></span>
        </p>
    </template>

    <!-- Hidden Inputs -->
    <input type="hidden" name="stock_type" :value="stockType">
    <input type="hidden" name="min_stock" :value="minStock">
    <input type="hidden" name="max_stock" :value="maxStock">
</div>

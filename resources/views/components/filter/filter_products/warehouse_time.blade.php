<div x-data="{ 
        mode: 'preset', 
        selected: '', 
        start: '', 
        end: '', 
        setMode(type) {
            this.mode = type;
            if (type === 'preset') {
                this.selected = ''; 
                this.start = ''; 
                this.end = '';
            } else if (type === 'custom') {
                const today = new Date().toISOString().split('T')[0];
                this.start = today;
                this.end = today;
                this.selected = '';
            }
        }
    }" class="space-y-4 bg-gray-900 p-4 rounded-lg border border-gray-700" 
    x-init="$watch('selected', () => $dispatch('filter-change')); $watch('start', () => $dispatch('filter-change')); $watch('end', () => $dispatch('filter-change'));">

    <label class="block text-sm font-medium text-gray-300 mb-2">Thời gian nhập</label>

    <!-- Nút chọn chế độ -->
    <div class="flex flex-col sm:flex-row gap-3 mb-3">
        <button type="button" @click="setMode('preset')"
            :class="mode === 'preset' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
            class="flex-1 sm:w-auto px-4 py-2 rounded-md text-sm font-medium transition">
            Khoảng thời gian
        </button>
        <button type="button" @click="setMode('custom')"
            :class="mode === 'custom' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
            class="flex-1 sm:w-auto px-4 py-2 rounded-md text-sm font-medium transition">
            Tùy chọn
        </button>
    </div>

    <!-- Chế độ chọn nhanh -->
    <!-- Chế độ chọn nhanh với flex-wrap -->
    <div x-show="mode === 'preset'" x-transition class="flex flex-wrap gap-2">
        <template x-for="option in [
        { label: '7 ngày ', value: '7' },
        { label: '30 ngày', value: '30' },
        { label: '3 tháng', value: '90' },
        { label: 'Năm nay', value: '0' }
    ]" :key="option.value">
            <button type="button" @click="selected = (selected === option.value ? '' : option.value)" :class="selected === option.value 
                    ? 'bg-blue-600 text-white border-blue-600' 
                    : 'border border-gray-600 text-gray-200 hover:bg-gray-700'"
                class="px-3 py-2 rounded-md text-sm text-center transition">
                <span x-text="option.label"></span>
            </button>
        </template>
    </div>


    <!-- Chế độ tùy chỉnh -->
    <!-- Chế độ tùy chỉnh -->
    <div x-show="mode === 'custom'" x-transition class="flex flex-wrap gap-4 mt-3">
        <!-- Ngày bắt đầu -->
        <div class="flex-1 min-w-[120px]">
            <label for="start" class="block text-sm font-medium text-gray-300 mb-1">Ngày bắt đầu</label>
            <input type="date" id="start" x-model="start" class="w-full rounded-lg border border-gray-600 bg-gray-900 text-gray-100 text-sm px-4 py-2 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition duration-150 ease-in-out">
        </div>

        <!-- Ngày kết thúc -->
        <div class="flex-1 min-w-[120px]">
            <label for="end" class="block text-sm font-medium text-gray-300 mb-1">Ngày kết thúc</label>
            <input type="date" id="end" x-model="end" class="w-full rounded-lg border border-gray-600 bg-gray-900 text-gray-100 text-sm px-4 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition duration-150 ease-in-out">
        </div>
    </div>


    <!-- Hiển thị kết quả -->
    <div class="text-sm text-gray-400 mt-1">
        <template x-if="mode === 'preset' && selected">
            <span>
                Đã chọn:
                <span class="text-gray-100 font-semibold" x-text="
                    selected === '7' ? '7 ngày gần nhất' :
                    selected === '30' ? '30 ngày gần nhất' :
                    selected === '90' ? '3 tháng gần nhất' :
                    selected === '0' ? 'Năm nay (2025)' : ''
                "></span>
            </span>
        </template>

        <template x-if="mode === 'custom' && (start || end)">
            <span>
                Từ
                <span class="text-gray-100 font-semibold" x-text="start || '...'"></span>
                →
                <span class="text-gray-100 font-semibold" x-text="end || '...'"></span>
            </span>
        </template>
    </div>

    <!-- Hidden inputs -->
    <input type="hidden" name="date_option" :value="mode">
    <input type="hidden" name="preset_date" :value="selected">
    <input type="hidden" name="start_date" :value="start">
    <input type="hidden" name="end_date" :value="end">

</div>
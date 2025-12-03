<div x-data="{
        brands: [],
        allBrands: {{ Js::from($thuonghieus) }},
        
        toggleBrand(brand) {
            console.log('Đã click hãng:', brand.TENTHUONGHIEU); // Kiểm tra click

            const index = this.brands.indexOf(brand.MATHUONGHIEU);
            if(index !== -1) {
                this.brands.splice(index, 1);
            } else {
                this.brands.push(brand.MATHUONGHIEU);
            }

            // 👇 QUAN TRỌNG: Thêm { bubbles: true } để tín hiệu bay lên tới window
            this.$dispatch('filter-change', { bubbles: true }); 
        },
        
        isSelected(math) {
            return this.brands.includes(math);
        }
    }" class="space-y-3 bg-gray-900 p-4 rounded-lg border border-gray-700">

    <label class="block text-sm font-medium text-gray-300 mb-1">Thương hiệu</label>

    <div class="grid grid-cols-2 gap-2">
        <template x-for="brand in allBrands" :key="brand.MATHUONGHIEU">
            <button type="button" @click="toggleBrand(brand)"
                :class="isSelected(brand.MATHUONGHIEU)
                    ? 'bg-blue-600 border-blue-600 text-white'
                    : 'border border-gray-600 text-gray-200 hover:bg-gray-700'"
                class="rounded-md flex items-center justify-start gap-2 px-3 py-2 transition">
                <span class="text-sm truncate" x-text="brand.TENTHUONGHIEU"></span>
            </button>
        </template>
    </div>

    <div class="mt-3 text-sm text-gray-300" x-show="brands.length > 0">
        <div class="flex flex-wrap gap-2">
            <template x-for="math in brands" :key="math">
                <span @click="toggleBrand(allBrands.find(b => b.MATHUONGHIEU === math))" 
                      class="inline-flex items-center gap-1 bg-blue-600 text-white px-2 py-1 rounded-full text-xs cursor-pointer hover:bg-blue-700 transition">
                    <span x-text="allBrands.find(b => b.MATHUONGHIEU === math)?.TENTHUONGHIEU"></span>
                    <span>✕</span>
                </span>
            </template>
        </div>
    </div>

    <input type="hidden" name="brands" :value="brands.join(',')">
</div>
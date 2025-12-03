<div class="space-y-4" 
     x-data="{
        // Định nghĩa danh sách cấu hình và DỮ LIỆU CÓ SẴN (Không cần API nữa)
        specs: [
            { 
                icon: '📏', label: 'Đường kính mặt', key: 'MADK', 
                data: {{ Js::from($duongkinhs) }}, 
                valueField: 'CHISO', unit: 'DONVIDO' 
            },
            { 
                icon: '📐', label: 'Chiều dài dây', key: 'MADD', 
                data: {{ Js::from($chieudadays) }}, 
                valueField: 'CHISO', unit: 'DONVIDO' 
            },
            { 
                icon: '📏', label: 'Độ dày', key: 'MADDY', 
                data: {{ Js::from($dodays) }}, 
                valueField: 'CHISO', unit: 'DONVIDO' 
            },
            { 
                icon: '⛓️', label: 'Chiều rộng dây', key: 'MCRD', 
                data: {{ Js::from($chieurongdays) }}, 
                valueField: 'CHISO', unit: 'DONVIDO' 
            },
            { 
                icon: '⚖️', label: 'Khối lượng', key: 'MKL', 
                data: {{ Js::from($khoiluongs) }}, 
                valueField: 'CHISO', unit: 'DONVIDO' 
            },
            { 
                icon: '💧', label: 'Chống nước', key: 'MCN', 
                data: {{ Js::from($chongnuocs) }}, 
                valueField: 'TEN', unit: '' 
            },
            { 
                icon: '🎨', label: 'Màu sắc', key: 'MMS', 
                data: {{ Js::from($mausacs) }}, 
                valueField: 'TENMAU', unit: '' 
            },
            { 
                icon: '⚙️', label: 'Chức năng', key: 'MCNANG', 
                data: {{ Js::from($chucnangs) }}, 
                valueField: 'TENCHUCNANG', unit: '' 
            }
        ]
     }">

    <template x-for="(item, i) in specs" :key="i">

        <div x-data="{
                open: false,
                selected: '',
                toggleOpen() {
                    this.open = !this.open;
                },
                select(val) { 
                    // Nếu bấm lại cái đã chọn thì bỏ chọn, ngược lại thì chọn mới
                    this.selected = (this.selected === val) ? '' : val;
                    // Kích hoạt sự kiện lọc
                    this.$dispatch('filter-change');
                }
            }" class="bg-gray-900 p-4 rounded-lg border border-gray-800 transition hover:border-gray-700">

            <div class="flex justify-between items-center cursor-pointer select-none" @click="toggleOpen()">
                <div class="flex items-center gap-2 text-gray-100 font-medium text-sm">
                    <span x-text="item.icon" class="text-lg"></span>
                    <span x-text="item.label"></span>
                </div>
                
                <svg class="w-4 h-4 text-gray-400 transform transition-transform duration-200" 
                     :class="{'rotate-180 text-blue-400': open}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div x-show="open" class="mt-3 flex flex-wrap gap-2">
                
                <div x-show="item.data.length === 0" class="text-xs text-gray-500 italic w-full">Đang cập nhật...</div>

                <template x-for="opt in item.data" :key="opt[item.key]">
                    <button type="button"
                        @click="select(opt[item.key])"
                        :class="selected === opt[item.key]
                            ? 'bg-blue-600 text-white border-blue-500 shadow-md'
                            : 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700'"
                        class="border px-3 py-2 rounded-md text-xs transition">
                       
                       <span x-text="opt[item.valueField]"></span>
                       <span x-show="item.unit && opt[item.unit]" x-text="opt[item.unit]"></span>
                    </button>
                </template>
            </div>

            <div x-show="selected" class="text-blue-400 text-xs mt-2 ml-1 italic">
                Đã chọn: 
                <span x-text="item.data.find(o => o[item.key] === selected)?.[item.valueField]"></span>
            </div>

            <input type="hidden" :name="item.key" :value="selected">
        </div>

    </template>

</div>
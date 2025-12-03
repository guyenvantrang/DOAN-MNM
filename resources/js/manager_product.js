export function initPriceRange() {
    document.addEventListener("DOMContentLoaded", () => {
        const sliderMin = document.getElementById('sliderMin');
        const sliderMax = document.getElementById('sliderMax');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const display = document.getElementById('rangeDisplay');
        const buttons = document.querySelectorAll('.price-btn');
        const gap = 1000000;

        if (!sliderMin || !sliderMax || !minPrice || !maxPrice || !display) return;

        // Hàm format tiền VNĐ
        const formatVND = (value) => Intl.NumberFormat('vi-VN').format(value);

        // Cập nhật giá trị hiển thị
        function updateValues(e) {
            let minVal = parseInt(sliderMin.value);
            let maxVal = parseInt(sliderMax.value);
            if (maxVal - minVal < gap) {
                if (e?.target === sliderMin) sliderMin.value = maxVal - gap;
                else sliderMax.value = minVal + gap;
            }
            minPrice.value = sliderMin.value;
            maxPrice.value = sliderMax.value;
            display.textContent = `${formatVND(sliderMin.value)} - ${formatVND(sliderMax.value)} VNĐ`;
        }

        // Lắng nghe sự kiện slider
        sliderMin.addEventListener('input', updateValues);
        sliderMax.addEventListener('input', updateValues);

        // Nút chọn giá cố định
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const minVal = parseInt(btn.dataset.min);
                const maxVal = parseInt(btn.dataset.max);

                sliderMin.value = minVal;
                sliderMax.value = maxVal;
                minPrice.value = minVal;
                maxPrice.value = maxVal;
                updateValues();

                // Đổi màu nút đang chọn
                buttons.forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
                btn.classList.add('bg-blue-600', 'text-white');
            });
        });

        updateValues();
    });
}

export function initDateFilter() {
    document.addEventListener("DOMContentLoaded", () => {
        const radios = document.querySelectorAll('input[name="date_option"]');
        const customRange = document.getElementById('custom-date-range');

        // Nếu không tìm thấy phần tử thì thoát
        if (!radios.length || !customRange) return;

        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                // Ẩn/hiện vùng chọn ngày tùy chỉnh
                const isCustom = e.target.value === 'custom';
                customRange.classList.toggle('hidden', !isCustom);
            });
        });
    });
}

export function initPagination() {
    document.addEventListener("DOMContentLoaded", () => {
        document.addEventListener("click", (e) => {
            const link = e.target.closest(".pagination a");
            if (!link) return;

            e.preventDefault();
            const url = link.getAttribute("href");

            fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
                .then((response) => response.text())
                .then((html) => {
                    const table = document.querySelector("#table-sanpham");
                    if (table) {
                        table.innerHTML = html;
                        window.history.pushState({}, "", url);
                    }
                })
                .catch((err) => console.error("Lỗi phân trang:", err));
        });
    });
}

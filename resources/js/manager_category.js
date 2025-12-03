export function initCategorySearch() {
    const searchInput = document.getElementById('search');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const resetBtn = document.getElementById('resetBtn');

    if (!searchInput || !dateFrom || !dateTo || !resetBtn) return;

    const urlSearch = searchInput.dataset.urlSearch;
    const urlDate = searchInput.dataset.urlDate;
    const urlAll = searchInput.dataset.urlAll;

    let timer;

    function fetchData() {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const query = searchInput.value;
            const from = dateFrom.value;
            const to = dateTo.value;

            let url = '';
            if (query) url = urlSearch + '?search=' + encodeURIComponent(query);
            else if (from || to) url = urlDate + '?date_from=' + from + '&date_to=' + to;
            else url = urlAll;

            try {
                const res = await fetch(url);
                if (!res.ok) throw new Error('Lỗi server');
                const html = await res.text();
                document.getElementById('category-table').innerHTML = html;
            } catch (err) {
                console.error(err);
                alert('Không thể tải dữ liệu. Vui lòng thử lại!');
            }
        }, 300);
    }

    searchInput.addEventListener('input', fetchData);
    dateFrom.addEventListener('change', fetchData);
    dateTo.addEventListener('change', fetchData);
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        fetchData();
    });
}

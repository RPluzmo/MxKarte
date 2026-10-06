const trackPicker = document.getElementById('track-picker-dialog');

if (trackPicker) {
    const openButton = document.getElementById('open-track-picker');
    const filterInput = document.getElementById('announcement-track');
    const searchInput = document.getElementById('track-picker-search');
    const emptyMessage = document.getElementById('track-picker-empty');
    const filterOptions = [...trackPicker.querySelectorAll('[data-track-filter]')];
    const searchOptions = [...trackPicker.querySelectorAll('[data-track-search-option]')];

    openButton?.addEventListener('click', () => {
        searchInput.value = '';
        searchOptions.forEach((option) => {
            option.hidden = false;
        });
        emptyMessage.hidden = true;
        trackPicker.showModal();
        searchInput.focus();
    });

    searchInput.addEventListener('input', () => {
        const search = searchInput.value.toLocaleLowerCase('lv').trim();
        let visibleCount = 0;

        searchOptions.forEach((option) => {
            const matches = option.dataset.trackName.toLocaleLowerCase('lv').includes(search);
            option.hidden = !matches;
            visibleCount += Number(matches);
        });

        emptyMessage.hidden = visibleCount > 0;
    });

    filterOptions.forEach((option) => {
        option.addEventListener('click', () => {
            filterInput.value = option.dataset.trackId;
            openButton.textContent = option.dataset.trackLabel;
            openButton.setAttribute('aria-label', `Trase: ${option.dataset.trackLabel}`);
            filterOptions.forEach((item) => {
                item.setAttribute('aria-pressed', String(item === option));
            });
            trackPicker.close();
        });
    });
}

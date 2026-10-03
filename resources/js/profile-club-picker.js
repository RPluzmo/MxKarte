const dialog = document.querySelector('#club-picker-dialog');

if (dialog) {
    const clubInput = document.querySelector('#profile-club-value');
    const selectedClubLabel = document.querySelector('#selected-club-label');
    const selectedClubLogo = document.querySelector('#selected-club-logo');
    const searchInput = dialog.querySelector('#club-search');
    const emptyMessage = dialog.querySelector('#club-search-empty');
    const clubOptions = [...dialog.querySelectorAll('[data-club-option]')];

    const normalize = (value) => value.toLocaleLowerCase('lv').trim();

    const setSelectedClub = (value, label, logo) => {
        clubInput.value = value;
        selectedClubLabel.textContent = label;
        selectedClubLogo.src = logo;
        selectedClubLogo.hidden = !logo;
        selectedClubLogo.alt = logo ? `${label} logo` : '';

        clubOptions.forEach((option) => {
            option.setAttribute('aria-pressed', String(option.dataset.clubValue === value));
        });

        dialog.close();
    };

    document.querySelector('#open-club-picker')?.addEventListener('click', () => {
        dialog.showModal();
        searchInput.focus();
    });

    dialog.addEventListener('click', (event) => {
        const option = event.target.closest('[data-club-option]');

        if (option) {
            setSelectedClub(
                option.dataset.clubValue,
                option.dataset.clubName,
                option.dataset.clubLogo
            );
        }
    });

    searchInput.addEventListener('input', () => {
        const search = normalize(searchInput.value);
        let visibleCount = 0;

        clubOptions.forEach((option) => {
            const matches = normalize(option.dataset.clubName).includes(search);
            option.hidden = !matches;
            visibleCount += Number(matches);
        });

        emptyMessage.hidden = visibleCount > 0;
    });
}

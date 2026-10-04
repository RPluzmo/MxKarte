function setupChoicePicker({ dialogId, openButtonId, inputId, labelId, imageId, searchId, emptyMessageId }) {
    const dialog = document.getElementById(dialogId);

    if (!dialog) {
        return;
    }

    const valueInput = document.getElementById(inputId);
    const selectedLabel = document.getElementById(labelId);
    const selectedImage = document.getElementById(imageId);
    const searchInput = document.getElementById(searchId);
    const emptyMessage = document.getElementById(emptyMessageId);
    const options = [...dialog.querySelectorAll('[data-choice-option]')];
    const normalize = (value) => value.toLocaleLowerCase('lv').trim();

    const choose = (option) => {
        const { choiceValue, choiceName, choiceImage } = option.dataset;

        valueInput.value = choiceValue;
        selectedLabel.textContent = choiceName;
        selectedImage.src = choiceImage;
        selectedImage.hidden = !choiceImage;
        selectedImage.alt = choiceImage ? `${choiceName} attēls` : '';

        options.forEach((item) => {
            item.setAttribute('aria-pressed', String(item === option));
        });

        dialog.close();
    };

    document.getElementById(openButtonId)?.addEventListener('click', () => {
        dialog.showModal();
        searchInput.focus();
    });

    dialog.addEventListener('click', (event) => {
        const option = event.target.closest('[data-choice-option]');

        if (option) {
            choose(option);
        }
    });

    searchInput.addEventListener('input', () => {
        const search = normalize(searchInput.value);
        let visibleCount = 0;

        options.forEach((option) => {
            const matches = normalize(option.dataset.choiceName).includes(search);
            option.hidden = !matches;
            visibleCount += Number(matches);
        });

        emptyMessage.hidden = visibleCount > 0;
    });
}

setupChoicePicker({
    dialogId: 'club-picker-dialog',
    openButtonId: 'open-club-picker',
    inputId: 'profile-club-value',
    labelId: 'selected-club-label',
    imageId: 'selected-club-logo',
    searchId: 'club-search',
    emptyMessageId: 'club-search-empty',
});

setupChoicePicker({
    dialogId: 'category-picker-dialog',
    openButtonId: 'open-category-picker',
    inputId: 'profile-category-value',
    labelId: 'selected-category-label',
    imageId: 'selected-category-image',
    searchId: 'category-search',
    emptyMessageId: 'category-search-empty',
});

setupChoicePicker({
    dialogId: 'experience-picker-dialog',
    openButtonId: 'open-experience-picker',
    inputId: 'profile-experience-value',
    labelId: 'selected-experience-label',
    imageId: 'selected-experience-image',
    searchId: 'experience-search',
    emptyMessageId: 'experience-search-empty',
});
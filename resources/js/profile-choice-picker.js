function setupChoicePicker(dialog) {
    const key = dialog.dataset.choiceDialog;
    const valueInput = document.getElementById(`${key}-value`);
    const selectedLabel = document.getElementById(`${key}-label`);
    const selectedImage = document.getElementById(`${key}-image`);
    const searchInput = document.getElementById(`${key}-search`);
    const emptyMessage = document.getElementById(`${key}-search-empty`);
    const options = [...dialog.querySelectorAll('[data-choice-option]')];
    const normalize = (value) => value.toLocaleLowerCase('lv').trim();

    const choose = (option) => {
        const { choiceValue, choiceName, choiceImage } = option.dataset;

        valueInput.value = choiceValue;
        selectedLabel.textContent = choiceName;
        if (selectedImage) {
            selectedImage.src = choiceImage;
            selectedImage.hidden = !choiceImage;
            selectedImage.alt = choiceImage ? `${choiceName} attēls` : '';
        }

        options.forEach((item) => {
            item.setAttribute('aria-pressed', String(item === option));
        });

        dialog.close();
    };

    document.getElementById(`open-${key}-picker`)?.addEventListener('click', () => {
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

document.querySelectorAll('[data-choice-dialog]').forEach(setupChoicePicker);
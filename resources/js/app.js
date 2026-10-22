import './profile-choice-picker.js';
import './track-image-viewer.js';
import './track-announcement-picker.js';
import './prevent-double-submit.js';
import './latvia-map.js';

document.querySelectorAll('[data-track-file-input]').forEach((input) => {
    const status = document.getElementById(input.dataset.trackFileInput);
    const preview = document.getElementById(input.dataset.trackFileInput.replace('-file-name', '-file-preview'));
    const previewUrls = [];

    if (!status || !preview) {
        return;
    }

    input.addEventListener('change', () => {
        const files = Array.from(input.files || []);
        previewUrls.splice(0).forEach((url) => URL.revokeObjectURL(url));
        preview.replaceChildren();

        if (files.length === 0) {
            status.textContent = input.multiple ? 'Nav izvēlēti jauni attēli' : 'Nav izvēlēts jauns attēls';
            return;
        }

        status.textContent = files.length === 1
            ? files[0].name
            : `Atlasīti ${files.length} attēli: ${files.map((file) => file.name).join(', ')}`;

        files.forEach((file) => {
            const url = URL.createObjectURL(file);
            previewUrls.push(url);

            const figure = document.createElement('figure');
            const image = document.createElement('img');
            const caption = document.createElement('figcaption');

            image.src = url;
            image.alt = file.name;
            caption.textContent = file.name;
            figure.append(image, caption);
            preview.append(figure);
        });
    });
});

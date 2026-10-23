document.querySelectorAll('[data-password-visibility-toggle]').forEach((toggle) => {
    const input = toggle.closest('label')?.querySelector('[data-password-visibility-input]');

    if (!input) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isVisible = input.type === 'text';

        input.type = isVisible ? 'password' : 'text';
        toggle.textContent = isVisible ? 'Rādīt paroli' : 'Paslēpt paroli';
        toggle.setAttribute('aria-pressed', String(!isVisible));
    });
});

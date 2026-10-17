const releaseForm = (form) => {
    delete form.dataset.submitting;
    form.querySelectorAll('[data-submit-locked]').forEach((button) => {
        button.disabled = false;
        delete button.dataset.submitLocked;
    });
};

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (event.defaultPrevented || form.method === 'get' || form.method === 'dialog') {
        return;
    }

    if (form.dataset.submitting) {
        event.preventDefault();

        return;
    }

    form.dataset.submitting = 'true';

    // Disable after the submit has started so the submitter value is still sent.
    setTimeout(() => {
        form.querySelectorAll('button[type="submit"], button:not([type])').forEach((button) => {
            button.disabled = true;
            button.dataset.submitLocked = 'true';
        });
    }, 0);

    setTimeout(() => releaseForm(form), 10000);
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submitting]').forEach(releaseForm);
});

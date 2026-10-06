const viewer = document.getElementById('track-image-viewer');
const viewerImage = document.getElementById('track-image-viewer-image');

if (viewer && viewerImage) {
    const openViewer = (image) => {
        viewerImage.src = image.currentSrc || image.src;
        viewerImage.alt = image.alt;
        viewer.showModal();
    };

    document.querySelectorAll('.js-track-image').forEach((image) => {
        image.addEventListener('click', () => openViewer(image));
        image.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openViewer(image);
            }
        });
    });

    viewer.addEventListener('click', () => viewer.close());
}

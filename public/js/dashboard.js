// Afficher le nom du fichier sélectionné
document.getElementById('media').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || '';
    document.getElementById('file-name').textContent = fileName ? '✓ ' + fileName : '';
});

// Modal pour afficher les médias en grand
function openMediaModal(src, type) {
    const modal = document.getElementById('mediaModal');
    const modalImage = document.getElementById('modalImage');
    const modalVideo = document.getElementById('modalVideo');
    const modalVideoSource = document.getElementById('modalVideoSource');

    modal.style.display = 'flex';

    if (type === 'image') {
        modalImage.src = src;
        modalImage.style.display = 'block';
        modalVideo.style.display = 'none';
    } else {
        modalVideoSource.src = src;
        modalVideo.load();
        modalVideo.style.display = 'block';
        modalImage.style.display = 'none';
    }
}

function closeMediaModal() {
    const modal = document.getElementById('mediaModal');
    const modalVideo = document.getElementById('modalVideo');
    modal.style.display = 'none';
    modalVideo.pause();
}

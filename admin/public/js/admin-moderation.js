function toggleContent(postId) {
    const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);
    const preview = postText.querySelector('.post-preview');
    const fullContent = postText.querySelector('.post-full-content');
    const btn = document.querySelector(`.btn-expand-text[data-post-id="${postId}"]`) ||
        event.target.closest('.btn-expand-text');

    if (!fullContent) return;

    if (fullContent.style.display === 'none') {
        preview.style.display = 'none';
        fullContent.style.display = 'inline';
        if (btn) {
            btn.innerHTML = '<i class="fas fa-chevron-up"></i> Lire moins';
        }
    } else {
        preview.style.display = 'inline';
        fullContent.style.display = 'none';
        if (btn) {
            btn.innerHTML = '<i class="fas fa-chevron-down"></i> Lire plus';
        }
    }
}

// Gestion de la modal pour les images
function openMediaModal(src, type) {
    const modal = document.getElementById('mediaModal');
    const modalImage = document.getElementById('modalImage');

    if (type === 'image') {
        modalImage.src = src;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeMediaModal() {
    const modal = document.getElementById('mediaModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Fermer avec la touche Echap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMediaModal();
    }
});

function openDeleteModal(postId) {
    document.getElementById('postIdToDelete').value = postId;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

// Fermer le modal en cliquant en dehors
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Toggle pour voir plus/moins de texte
function toggleContent(button) {
    const contentItem = button.closest('.post-content-item');
    const contentText = contentItem.querySelector('.content-text');
    const fullText = contentItem.getAttribute('data-full-text');
    const isExpanded = contentItem.classList.contains('expanded');

    if (isExpanded) {
        // Réduire
        const shortText = fullText.substring(0, 300) + '...';
        contentText.innerHTML = shortText.replace(/\n/g, '<br>');
        button.innerHTML = '<i class="fas fa-chevron-down"></i> Voir plus';
        contentItem.classList.remove('expanded');
    } else {
        // Étendre
        contentText.innerHTML = fullText.replace(/\n/g, '<br>');
        button.innerHTML = '<i class="fas fa-chevron-up"></i> Voir moins';
        contentItem.classList.add('expanded');
    }
}

// Fonctions pour le modal de media
function openMediaModal(src, type) {
    const modal = document.getElementById('mediaModal');
    const modalContent = document.getElementById('modalMediaContent');

    modalContent.src = src;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeMediaModal() {
    const modal = document.getElementById('mediaModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

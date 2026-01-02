function toggleContent(btn) {
    const container = btn.closest('.item-content, .item-post-ref');
    const shortText = container.querySelector('.content-text');
    const fullText = container.querySelector('.full-content');
    const icon = btn.querySelector('i');

    if (fullText.style.display === 'none') {
        shortText.style.display = 'none';
        fullText.style.display = 'inline';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        btn.innerHTML = '<i class="fas fa-chevron-up"></i> ' + (btn.classList.contains('btn-see-more-small') ? '' : 'Voir moins');
    } else {
        shortText.style.display = 'inline';
        fullText.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        btn.innerHTML = '<i class="fas fa-chevron-down"></i> ' + (btn.classList.contains('btn-see-more-small') ? '' : 'Voir plus');
    }
}

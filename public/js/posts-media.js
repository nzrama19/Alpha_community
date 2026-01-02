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

// Read More / See Less functionality
document.addEventListener('DOMContentLoaded', function() {
    // Pour index.php
    document.querySelectorAll('.btn-read-more').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);

            if (postText) {
                postText.classList.toggle('expanded');
                this.classList.toggle('expanded');

                if (postText.classList.contains('expanded')) {
                    // Show full content
                    const fullContent = postText.querySelector('.post-full-content');
                    const preview = postText.querySelector('.post-preview');
                    if (fullContent && preview) {
                        preview.style.display = 'none';
                        fullContent.style.display = 'inline';
                    }
                    this.innerHTML = '<i class="fas fa-chevron-up"></i> Voir moins';
                } else {
                    // Show preview
                    const fullContent = postText.querySelector('.post-full-content');
                    const preview = postText.querySelector('.post-preview');
                    if (fullContent && preview) {
                        preview.style.display = 'inline';
                        fullContent.style.display = 'none';
                    }
                    this.innerHTML = '<i class="fas fa-chevron-down"></i> Lire plus';
                }
            }
        });
    });

    // Pour categories.php
    document.querySelectorAll('.btn-expand-text').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const postText = document.querySelector(`.post-text[data-post-id="${postId}"]`);

            if (postText) {
                postText.classList.toggle('expanded');
                this.classList.toggle('expanded');

                if (postText.classList.contains('expanded')) {
                    // Show full content
                    const fullContent = postText.querySelector('.post-full-content');
                    const preview = postText.querySelector('.post-preview');
                    if (fullContent && preview) {
                        preview.style.display = 'none';
                        fullContent.style.display = 'inline';
                    }
                    this.innerHTML = '<i class="fas fa-chevron-up"></i> Voir moins';
                } else {
                    // Show preview
                    const fullContent = postText.querySelector('.post-full-content');
                    const preview = postText.querySelector('.post-preview');
                    if (fullContent && preview) {
                        preview.style.display = 'inline';
                        fullContent.style.display = 'none';
                    }
                    this.innerHTML = '<i class="fas fa-chevron-down"></i> Lire plus';
                }
            }
        });
    });

    // Toggle Comments Section
    document.querySelectorAll('.btn-toggle-comments').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const commentsSection = document.getElementById(`comments-${postId}`);

            if (commentsSection) {
                commentsSection.classList.toggle('comments-hidden');
                commentsSection.classList.toggle('comments-visible');

                // Change icon style when comments are visible
                if (commentsSection.classList.contains('comments-visible')) {
                    this.style.color = 'var(--primary-color)';
                } else {
                    this.style.color = '';
                }
            }
        });
    });
});

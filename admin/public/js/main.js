// Gestion des likes
document.addEventListener('DOMContentLoaded', function() {
    
    // Toggle Like
    document.querySelectorAll('.btn-like').forEach(button => {
        button.addEventListener('click', async function() {
            if (this.disabled) return;
            
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.likes-count');
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                
                const response = await fetch('../api/toggle-like.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour l'interface
                    countSpan.textContent = data.count;
                    
                    if (data.liked) {
                        this.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        this.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        });
    });
    
    // Toggle affichage des commentaires
    document.querySelectorAll('.btn-comment').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const commentsSection = document.getElementById('comments-' + postId);
            
            if (commentsSection.style.display === 'none' || !commentsSection.style.display) {
                commentsSection.style.display = 'block';
            } else {
                commentsSection.style.display = 'none';
            }
        });
    });
    
    // Soumettre un commentaire
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const postId = this.dataset.postId;
            const input = this.querySelector('input[name="comment"]');
            const content = input.value.trim();
            
            if (!content) return;
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('content', content);
                
                const response = await fetch('../api/add-comment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Créer l'élément commentaire
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    const avatarSrc = data.comment.avatar ? `../config/uploads/${data.comment.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(data.comment.username)}&background=d4a574&color=fff`;
                    commentDiv.innerHTML = `
                        <img src="${avatarSrc}" alt="Avatar" class="avatar-small">
                        <div class="comment-content">
                            <div class="comment-header">
                                <strong>${escapeHtml(data.comment.username)}</strong>
                                <span class="comment-date">${formatDate(data.comment.created_at)}</span>
                            </div>
                            <p>${escapeHtml(data.comment.content)}</p>
                        </div>
                    `;
                    
                    // Ajouter le commentaire à la liste
                    const commentsList = document.querySelector(`#comments-${postId} .comments-list`);
                    commentsList.appendChild(commentDiv);
                    
                    // Mettre à jour le compteur
                    const countSpan = document.querySelector(`.btn-comment[data-post-id="${postId}"] .comments-count`);
                    countSpan.textContent = data.total;
                    
                    // Vider le champ
                    input.value = '';
                    
                    // Scroller vers le nouveau commentaire
                    commentDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        });
    });
});

// Fonction pour échapper le HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Fonction pour formater la date
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${day}/${month}/${year} ${hours}:${minutes}`;
}

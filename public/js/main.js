// Gestion des likes
document.addEventListener('DOMContentLoaded', function() {
    
    // Déterminer automatiquement le chemin de base
    const getBasePath = () => {
        // Récupérer le chemin jusqu'à la racine du projet
        const currentPath = window.location.pathname;
        // Supposer que la racine du projet se termine avant /index.php ou /admin/dashboard.php
        const basePath = currentPath.split('/').slice(0, -1).join('/');
        return basePath === '' ? '/' : basePath;
    };
    
    const BASE_PATH = getBasePath();
    
    // Charger les likes anonymes depuis localStorage
    const loadAnonymousLikes = () => {
        const stored = localStorage.getItem('anonymous_likes');
        return stored ? JSON.parse(stored) : [];
    };
    
    // Sauvegarder les likes anonymes dans localStorage
    const saveAnonymousLikes = (likes) => {
        localStorage.setItem('anonymous_likes', JSON.stringify(likes));
    };
    
    // Vérifier si un post est liké (anonyme ou connecté)
    const isPostLiked = (postId, isLogged) => {
        if (isLogged) {
            // Pour les connectés, on vérient via le serveur (voir plus bas)
            return false;
        } else {
            // Pour les anonymes, vérifier dans localStorage
            const anonymousLikes = loadAnonymousLikes();
            return anonymousLikes.includes(postId.toString());
        }
    };
    
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
                
                const response = await fetch(BASE_PATH + '/api/toggle-like.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Mettre à jour l'interface
                    countSpan.textContent = data.count;
                    
                    if (data.is_logged) {
                        // Utilisateur connecté: utiliser l'état du serveur
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
                        // Utilisateur anonyme: gérer localement
                        const anonymousLikes = loadAnonymousLikes();
                        const postIdStr = postId.toString();
                        const isAlreadyLiked = anonymousLikes.includes(postIdStr);
                        
                        if (isAlreadyLiked) {
                            // Retirer le like
                            const index = anonymousLikes.indexOf(postIdStr);
                            anonymousLikes.splice(index, 1);
                            this.classList.remove('liked');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        } else {
                            // Ajouter le like
                            anonymousLikes.push(postIdStr);
                            this.classList.add('liked');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        }
                        
                        saveAnonymousLikes(anonymousLikes);
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
    
    // Appliquer l'état des likes anonymes au chargement
    const anonymousLikes = loadAnonymousLikes();
    document.querySelectorAll('.btn-like').forEach(button => {
        const postId = button.dataset.postId;
        if (anonymousLikes.includes(postId.toString())) {
            const icon = button.querySelector('i');
            button.classList.add('liked');
            icon.classList.remove('far');
            icon.classList.add('fas');
        }
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
                
                const response = await fetch(BASE_PATH + '/api/add-comment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Créer l'élément commentaire
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    const avatarSrc = data.comment.avatar ? `${BASE_PATH}/config/uploads/${data.comment.avatar}` : `${BASE_PATH}/public/images/default-avatar.png`;
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

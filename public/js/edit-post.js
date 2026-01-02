// Character counter
const textarea = document.getElementById('content');
const charCount = document.getElementById('charCount');

function updateCharCount() {
    charCount.textContent = textarea.value.length;
}

textarea.addEventListener('input', updateCharCount);
updateCharCount();

// Gestion des médias multiples
const mediaInput = document.getElementById('media');
const mediaPreviews = document.getElementById('mediaPreviews');
let selectedFiles = [];

mediaInput.addEventListener('change', function() {
    selectedFiles = Array.from(this.files);
    displayPreviews();
});

function displayPreviews() {
    mediaPreviews.innerHTML = '';

    if (selectedFiles.length === 0) {
        return;
    }

    selectedFiles.forEach((file, index) => {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'media-preview-item';

        const mediaInfo = document.createElement('div');
        mediaInfo.className = 'media-info-item';
        mediaInfo.innerHTML = `
            <div>
                <i class="fas fa-file"></i>
                <span>${file.name}</span>
            </div>
            <button type="button" class="remove-media" onclick="removeMedia(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        `;

        previewContainer.appendChild(mediaInfo);

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                img.alt = 'Aperçu';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.className = 'preview-image';
            video.controls = true;
            video.src = URL.createObjectURL(file);
            previewContainer.appendChild(video);
        }

        mediaPreviews.appendChild(previewContainer);
    });
}

function removeMedia(index) {
    selectedFiles.splice(index, 1);

    // Mettre à jour l'input file
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    mediaInput.files = dt.files;

    displayPreviews();
}

// Fonctions pour supprimer un média existant
function deleteExistingMedia(mediaId, postId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
        // Créer un champ caché pour indiquer la suppression
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_media[]';
        input.value = mediaId;
        document.querySelector('form').appendChild(input);

        // Masquer visuellement l'élément
        const mediaItem = document.getElementById(`existing-media-${mediaId}`);
        if (mediaItem) {
            mediaItem.style.display = 'none';
        }
    }
}

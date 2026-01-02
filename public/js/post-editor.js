const textarea = document.getElementById('content');
const charCount = document.getElementById('charCount');
const mediaInput = document.getElementById('media');
const mediaPreviews = document.getElementById('mediaPreviews');
let selectedFiles = [];

// Compteur de caractères
if (textarea && charCount) {
    textarea.addEventListener('input', function() {
        charCount.textContent = Math.min(this.value.length, 5000);
    });
}

// Prévisualisation des fichiers multiples
if (mediaInput) {
    mediaInput.addEventListener('change', function() {
        selectedFiles = Array.from(this.files);
        displayPreviews();
    });
}

function displayPreviews() {
    if (!mediaPreviews) return;
    
    mediaPreviews.innerHTML = '';

    if (selectedFiles.length === 0) {
        return;
    }

    selectedFiles.forEach((file, index) => {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'media-preview-item';

        const mediaInfo = document.createElement('div');
        mediaInfo.className = 'media-info';
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

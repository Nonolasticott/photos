@extends('layouts.app')

@section('titre', 'Ajouter une photo')

@section('styles-css')
<link rel="stylesheet" href="{{ asset('css/formulaire.css') }}">
@endsection

@section('contenu')
<div class="conteneur-formulaire">
    <div class="formulaire-ajout">
        <h2 class="titre-formulaire">
            <i class="fas fa-plus-circle"></i> Ajouter une photo
        </h2>
        <p class="sous-titre-formulaire">
            Album : <strong>{{ $album->titre }}</strong>
        </p>
        
        @if ($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('enregistrer-photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="album_id" value="{{ $album->id }}">
            
            <!-- Upload de fichier -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-upload"></i> Upload d'un fichier image
                </label>
                <div class="zone-upload" onclick="document.getElementById('photo_file').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p style="margin-top: 0.75rem;">Cliquez ou glissez-déposez votre image</p>
                    <small>JPEG, PNG, JPG, GIF (max 5 Mo)</small>
                </div>
                <input type="file" id="photo_file" name="photo_file" class="champ-texte" accept="image/*" style="display: none;">
                <div class="message-aide" id="nom-fichier"></div>
            </div>
            
            <!-- URL externe -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-link"></i> Ou lien URL d'une image
                </label>
                <input type="url" name="photo_url" class="champ-texte" 
                       placeholder="https://exemple.com/image.jpg"
                       value="{{ old('photo_url') }}">
                <div class="message-aide">Copiez-collez l'URL directe vers l'image</div>
            </div>
            
            <!-- Titre -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-heading"></i> Titre de la photo *
                </label>
                <input type="text" name="titre" class="champ-texte" 
                       placeholder="Ex: Coucher de soleil à la plage" 
                       required
                       value="{{ old('titre') }}">
            </div>
            
            <!-- Note -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-star"></i> Note *
                </label>
                <div class="rating-stars">
                    @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" id="star{{ $i }}" name="note" value="{{ $i }}" 
                           {{ old('note') == $i ? 'checked' : '' }} required>
                    <label for="star{{ $i }}" title="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                        <i class="fas fa-star"></i>
                    </label>
                    @endfor
                </div>
            </div>
            
            <!-- Tags -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-tags"></i> Étiquettes / Tags (optionnel)
                </label>
                <div class="zone-tags">
                    @foreach($tags as $tag)
                    <div class="option-tag">
                        <label>
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                            <span>{{ $tag->nom }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Ajout de nouveau tag -->
            <div class="zone-champ">
                <label class="etiquette-champ">
                    <i class="fas fa-plus-circle"></i> Ajouter un nouveau tag
                </label>
                <div class="zone-nouveau-tag">
                    <div class="groupe-input-tag">
                        <input type="text" id="nouveau-tag" placeholder="Nom du tag..." 
                               class="champ-texte">
                        <button type="button" id="btn-ajouter-tag" class="bouton-ajouter-tag">
                            <i class="fas fa-plus"></i> Ajouter
                        </button>
                    </div>
                    <div id="nouveau-tag-message"></div>
                </div>
            </div>
            
            <!-- Boutons -->
            <div class="actions-formulaire">
                <a href="{{ route('voir-album', $album->id) }}" class="bouton-annuler">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="bouton-valider">
                    <i class="fas fa-check"></i> Ajouter la photo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Affichage du nom du fichier sélectionné
document.getElementById('photo_file').addEventListener('change', function() {
    const nomFichier = this.files[0]?.name || '';
    document.getElementById('nom-fichier').textContent = nomFichier 
        ? `✓ Fichier sélectionné : ${nomFichier}` 
        : '';
});

// Glisser-déposer
const zoneUpload = document.querySelector('.zone-upload');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    zoneUpload.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    zoneUpload.addEventListener(eventName, () => {
        zoneUpload.style.borderColor = '#6366f1';
        zoneUpload.style.background = '#f0f1ff';
    });
});

['dragleave', 'drop'].forEach(eventName => {
    zoneUpload.addEventListener(eventName, () => {
        zoneUpload.style.borderColor = '#d1d5db';
        zoneUpload.style.background = '#f9fafb';
    });
});

zoneUpload.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('photo_file').files = files;
    
    const nomFichier = files[0]?.name || '';
    document.getElementById('nom-fichier').textContent = nomFichier 
        ? `✓ Fichier sélectionné : ${nomFichier}` 
        : '';
});

// Gestion de l'ajout de nouveau tag
document.getElementById('btn-ajouter-tag').addEventListener('click', function() {
    const input = document.getElementById('nouveau-tag');
    const nomTag = input.value.trim();
    const message = document.getElementById('nouveau-tag-message');
    
    if (!nomTag) {
        message.textContent = '⚠ Veuillez entrer un nom de tag';
        message.className = 'error';
        return;
    }
    
    // Créer le nouveau tag via AJAX
    fetch('{{ route("creer-tag") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({ nom: nomTag })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter le nouveau tag à la liste et le cocher
            const zoneTagsDiv = document.querySelector('.zone-tags');
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-tag';
            optionDiv.innerHTML = `
                <label>
                    <input type="checkbox" name="tags[]" value="${data.tag.id}" checked>
                    <span>${data.tag.nom}</span>
                </label>
            `;
            zoneTagsDiv.appendChild(optionDiv);
            
            // Réinitialiser l'input et afficher un message de succès
            input.value = '';
            message.textContent = '✓ Tag "' + data.tag.nom + '" ajouté avec succès';
            message.className = 'success';
            setTimeout(() => { message.className = ''; message.textContent = ''; }, 3000);
        } else {
            message.textContent = '✗ ' + (data.message || 'Ce tag existe déjà');
            message.className = 'error';
        }
    })
    .catch(error => {
        message.textContent = '✗ Erreur lors de l\'ajout du tag';
        message.className = 'error';
        console.error('Erreur:', error);
    });
});
</script>
@endsection

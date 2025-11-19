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
                <select name="note" class="champ-texte" required>
                    <option value="">-- Sélectionner une note --</option>
                    <option value="5" {{ old('note') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excellent (5/5)</option>
                    <option value="4" {{ old('note') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Très bon (4/5)</option>
                    <option value="3" {{ old('note') == 3 ? 'selected' : '' }}>⭐⭐⭐ Bon (3/5)</option>
                    <option value="2" {{ old('note') == 2 ? 'selected' : '' }}>⭐⭐ Acceptable (2/5)</option>
                    <option value="1" {{ old('note') == 1 ? 'selected' : '' }}>⭐ Moyen (1/5)</option>
                </select>
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
</script>
@endsection

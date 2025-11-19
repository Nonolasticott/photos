@extends('layouts.app')

@section('titre', $album->titre)

@section('styles-css')
<link rel="stylesheet" href="{{ asset('css/album-detail.css') }}">
@endsection

@section('contenu')
<div>
    <div class="entete-album">
        <h1 class="titre-album">
            <i class="fas fa-folder-open"></i> {{ $album->titre }}
        </h1>
        <div class="infos-album">
            <div>
                <i class="fas fa-calendar"></i> 
                Créé le {{ date('d/m/Y', strtotime($album->creation)) }}
            </div>
            <div>
                <i class="fas fa-images"></i> 
                {{ count($photos) }} {{ count($photos) > 1 ? 'photos' : 'photo' }}
            </div>
        </div>
        <div class="actions-album">
            <a href="{{ route('liste-albums') }}" class="bouton-retour">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="{{ route('ajouter-photo', $album->id) }}" class="bouton-ajouter">
                <i class="fas fa-plus"></i> Ajouter une photo
            </a>
        </div>
    </div>

    <div class="zone-filtres">
        <form method="GET" action="{{ route('voir-album', $album->id) }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="label-filtre">
                        <i class="fas fa-search"></i> Rechercher
                    </label>
                    <input type="text" name="search" class="champ-recherche" 
                           placeholder="Titre de la photo..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="label-filtre">
                        <i class="fas fa-tags"></i> Filtrer par tags
                    </label>
                    <select name="tags[]" class="champ-recherche" multiple size="3">
                        @foreach($tousTags as $tag)
                        <option value="{{ $tag->id }}" 
                                {{ in_array($tag->id, (array) request('tags')) ? 'selected' : '' }}>
                            {{ $tag->nom }}
                        </option>
                        @endforeach
                    </select>
                    <small style="color: #6b7280;">Ctrl pour plusieurs</small>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="label-filtre">
                        <i class="fas fa-sort"></i> Trier par
                    </label>
                    <select name="sort" class="champ-recherche">
                        <option value="titre" {{ $tri == 'titre' ? 'selected' : '' }}>Titre</option>
                        <option value="note" {{ $tri == 'note' ? 'selected' : '' }}>Note</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                <button type="submit" class="bouton-filtrer">
                    <i class="fas fa-filter"></i> Appliquer
                </button>
                <a href="{{ route('voir-album', $album->id) }}" class="bouton-reinitialiser">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    @if(count($photos) > 0)
    <div class="grille-photos">
        @foreach($photos as $photo)
        <div class="carte-photo">
            <form action="{{ route('supprimer-photo', $photo->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="bouton-supprimer" 
                        onclick="return confirm('Supprimer cette photo ?')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            
            <img src="{{ $photo->url }}" 
                 alt="{{ $photo->titre }}" 
                 class="image-photo"
                 onclick="ouvrirModal('{{ $photo->url }}', '{{ $photo->titre }}')">
            
            <div class="corps-carte">
                <div class="titre-photo">{{ $photo->titre }}</div>
                
                <div>
                    @foreach($photo->tags as $tag)
                    <span class="etiquette-tag">{{ $tag->nom }}</span>
                    @endforeach
                </div>
                
                <div class="note-photo">
                    <i class="fas fa-star"></i> {{ $photo->note }}/5
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="etat-vide" style="background: white;">
        <i class="fas fa-image"></i>
        <p>Aucune photo ne correspond à vos critères</p>
    </div>
    @endif
</div>

<div id="modalPhoto" class="modal-photo" onclick="fermerModal()">
    <span class="bouton-fermer-modal">&times;</span>
    <img id="imageModal" class="image-modal">
</div>
@endsection

@section('scripts')
<script>
function ouvrirModal(urlImage, titreImage) {
    document.getElementById('modalPhoto').style.display = 'block';
    document.getElementById('imageModal').src = urlImage;
    document.getElementById('imageModal').alt = titreImage;
    document.body.style.overflow = 'hidden';
}

function fermerModal() {
    document.getElementById('modalPhoto').style.display = 'none';
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        fermerModal();
    }
});
</script>
@endsection

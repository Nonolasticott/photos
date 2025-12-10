@extends('layouts.app')

@section('titre', $album->titre)

@section('styles-css')
<link rel="stylesheet" href="{{ asset('css/photos.css') }}">
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
            <div class="rangee-filtres">
                <div class="colonne-recherche">
                    <label class="etiquette-filtre">
                        <i class="fas fa-search"></i> Rechercher
                    </label>
                    <input type="text" name="search" class="champ-recherche" placeholder="Titre de la photo..." value="{{ request('search') }}">
                </div>
                
                <div class="colonne-tri">
                    <label class="etiquette-filtre">
                        <i class="fas fa-sort"></i> Trier par
                    </label>
                    <div class="conteneur-boutons-tri">
                        <a href="{{ route('voir-album', ['id' => $album->id, 'sort' => 'titre', 'order' => 'asc']) }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('tags') ? '&tags[]=' . implode('&tags[]=', request('tags')) : '' }}" class="bouton-tri">
                            <i class="fas fa-sort-alpha-down"></i> Titre (A <i class="fa-solid fa-arrow-right-long"></i> Z)
                        </a>
                        <a href="{{ route('voir-album', ['id' => $album->id, 'sort' => 'titre', 'order' => 'desc']) }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('tags') ? '&tags[]=' . implode('&tags[]=', request('tags')) : '' }}" class="bouton-tri">
                            <i class="fas fa-sort-alpha-up"></i> Titre (Z <i class="fa-solid fa-arrow-right-long"></i> A)
                        </a>
                        <a href="{{ route('voir-album', ['id' => $album->id, 'sort' => 'note', 'order' => 'desc']) }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('tags') ? '&tags[]=' . implode('&tags[]=', request('tags')) : '' }}" class="bouton-tri">
                            <i class="fas fa-arrow-up"></i> Bonne Note
                        </a>
                        <a href="{{ route('voir-album', ['id' => $album->id, 'sort' => 'note', 'order' => 'asc']) }}{{ request('search') ? '&search=' . request('search') : '' }}{{ request('tags') ? '&tags[]=' . implode('&tags[]=', request('tags')) : '' }}" class="bouton-tri">
                            <i class="fas fa-arrow-down"></i> Mauvaise Note
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="section-filtrage-tags">
                <label class="etiquette-filtre">
                    <i class="fas fa-tags"></i> Filtrer par tags
                </label>
                <div class="liste-tags">
                    @foreach($tousTags as $tag)
                    <label class="etiquette-tag-cocher">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               {{ in_array($tag->id, (array) request('tags')) ? 'checked' : '' }}
                               style="display: none;">
                        <span class="badge-tag">{{ $tag->nom }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            
            <div class="groupe-boutons-action">
                <button type="submit" class="bouton-appliquer-filtres">
                    <i class="fas fa-filter"></i> Appliquer
                </button>
                <a href="{{ route('voir-album', $album->id) }}" class="bouton-reinitialiser-filtres">
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
            
            <div class="contenu-carte">
                <div class="nom-photo">{{ $photo->titre }}</div>
                
                <div>
                    @foreach($photo->tags as $tag)
                    <span class="etiquette-tag">{{ $tag->nom }}</span>
                    @endforeach
                </div>
                
                <div class="note-affichage">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $photo->note)
                            <i class="fas fa-star" style="color: #fbbf24;"></i>
                        @else
                            <i class="fas fa-star" style="color: #d1d5db;"></i>
                        @endif
                    @endfor
                    <span style="margin-left: 0.5rem; font-size: 0.9rem; color: #6b7280;">{{ round($photo->note, 1) }}/5</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="zone-vide">
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

@extends('layouts.app')

@section('titre', 'Mes Albums Photo')

@section('styles-css')
<link rel="stylesheet" href="{{ asset('css/albums.css') }}">
@endsection

@section('contenu')
<div>
    <h1 class="titre-page">
        <i class="fas fa-images"></i> Mes Albums Photo
    </h1>

    <div class="panneau-controle">
        <div class="titre-panneau">
            <i class="fas fa-sliders-h"></i>
            <span>Trier les albums</span>
        </div>
        <div class="groupe-boutons">
            <a href="{{ route('liste-albums', ['sort' => 'titre', 'order' => 'asc']) }}" class="bouton-tri">
                <i class="fas fa-sort-alpha-down"></i> Titre (A <i class="fa-solid fa-arrow-right-long"></i> Z)
            </a>
            <a href="{{ route('liste-albums', ['sort' => 'titre', 'order' => 'desc']) }}" class="bouton-tri">
                <i class="fas fa-sort-alpha-up"></i> Titre (Z <i class="fa-solid fa-arrow-right-long"></i> A)
            </a>
            <a href="{{ route('liste-albums', ['sort' => 'creation', 'order' => 'desc']) }}" class="bouton-tri">
                <i class="fas fa-calendar-alt"></i> Plus récents
            </a>
            <a href="{{ route('liste-albums', ['sort' => 'creation', 'order' => 'asc']) }}" class="bouton-tri">
                <i class="fas fa-history"></i> Plus anciens
            </a>
        </div>
    </div>

    @if(count($albums) > 0)
    <div class="grille-albums">
        @foreach($albums as $album)
        <div class="carte-album folder" onclick="window.location='{{ route('voir-album', $album->id) }}'">
                <div class="corps-carte-album">
                    <h3 class="nom-album">{{ $album->titre }}</h3>
                <div class="info-album">
                    <i class="fas fa-images"></i>
                    <span>{{ $album->photo_count }} {{ $album->photo_count > 1 ? 'photos' : 'photo' }}</span>
                </div>
                <div class="info-album">
                    <i class="fas fa-calendar"></i>
                    <span>{{ date('d/m/Y', strtotime($album->creation)) }}</span>
                </div>
                </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="etat-vide">
        <i class="fas fa-folder-open"></i>
        <p>Aucun album disponible pour le moment</p>
    </div>
    @endif
</div>
@endsection

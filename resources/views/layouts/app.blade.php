<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'PhotoManager')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @yield('styles-css')

</head>
<body>
    <nav class="barre-navigation">
        <div class="contenu-nav">
            <a href="{{ route('liste-albums') }}" class="logo-site">
                <i class="fas fa-camera-retro"></i>
                <span>PhotoManager</span>
            </a>
            <a href="{{ route('liste-albums') }}" class="lien-navigation">
                <i class="fas fa-images"></i>
                <span>Mes Albums</span>
            </a>
        </div>
    </nav>

    @if(session('success'))
    <div style="padding: 0 2rem;">
        <div class="message-succes">
            <i class="fas fa-check-circle" style="font-size: 1.25rem;"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div style="padding: 0 2rem;">
        <div class="message-erreur">
            <i class="fas fa-exclamation-circle" style="font-size: 1.25rem;"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <div class="conteneur-principal">
        @yield('contenu')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>

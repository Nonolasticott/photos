<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        $tri = $request->get('sort', 'titre');
        $ordre = $request->get('order', 'asc');
        
        if (!in_array($tri, ['titre', 'creation'])) {
            $tri = 'titre';
        }
        
        if (!in_array($ordre, ['asc', 'desc'])) {
            $ordre = 'asc';
        }
        
        $albums = DB::table('albums')
            ->leftJoin('photos', 'albums.id', '=', 'photos.album_id')
            ->select('albums.*', DB::raw('COUNT(photos.id) as photo_count'))
            ->groupBy('albums.id', 'albums.titre', 'albums.creation', 'albums.user_id')
            ->orderBy($tri, $ordre)
            ->get();
        
        return view('index', compact('albums', 'tri', 'ordre'));
    }
    
    public function show($id, Request $request)
    {
        $album = DB::table('albums')->where('id', $id)->first();
        
        if (!$album) {
            abort(404, 'Album non trouvé');
        }
        
        $requete = DB::table('photos')->where('album_id', $id);
        
        if ($request->has('search') && $request->search != '') {
            $requete->where('titre', 'LIKE', '%' . $request->search . '%');
        }
        
        if ($request->has('tags') && !empty($request->tags)) {
            $requete->whereIn('photos.id', function($sousRequete) use ($request) {
                $sousRequete->select('photo_id')
                    ->from('possede_tag')
                    ->whereIn('tag_id', $request->tags)
                    ->groupBy('photo_id')
                    ->havingRaw('COUNT(DISTINCT tag_id) = ?', [count($request->tags)]);
            });
        }
        
        $tri = $request->get('sort', 'titre');
        $ordre = $request->get('order', 'asc');
        
        if (in_array($tri, ['titre', 'note'])) {
            $requete->orderBy($tri, $ordre);
        }
        
        $photos = $requete->get();
        
        foreach ($photos as $photo) {
            $photo->tags = DB::table('tags')
                ->join('possede_tag', 'tags.id', '=', 'possede_tag.tag_id')
                ->where('possede_tag.photo_id', $photo->id)
                ->select('tags.*')
                ->get();
        }
        
        $tousTags = DB::table('tags')
            ->join('possede_tag', 'tags.id', '=', 'possede_tag.tag_id')
            ->join('photos', 'possede_tag.photo_id', '=', 'photos.id')
            ->where('photos.album_id', $album->id)
            ->select('tags.*')
            ->distinct()
            ->get();
        
        return view('show', compact('album', 'photos', 'tousTags', 'tri', 'ordre'));
    }
    
    public function update($id, Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255'
        ]);
        
        DB::table('albums')
            ->where('id', $id)
            ->update(['titre' => $request->titre]);
        
        return redirect()->route('voir-album', $id)
            ->with('success', 'Album renommé avec succès !');
    }
}

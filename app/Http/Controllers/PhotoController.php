<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function create($album_id)
    {
        $album = DB::table('albums')->where('id', $album_id)->first();
        
        if (!$album) {
            abort(404, 'Album non trouvé');
        }
        
        $tags = DB::table('tags')->get();
        
        return view('create', compact('album', 'tags'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|max:255',
            'note' => 'required|integer|min:0|max:5',
            'album_id' => 'required|exists:albums,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ]);
        
        $urlPhoto = '';
        
        if ($request->hasFile('photo_file')) {
            $request->validate([
                'photo_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);
            
            $fichier = $request->file('photo_file');
            $nomFichier = time() . '_' . $fichier->getClientOriginalName();
            $chemin = $fichier->storeAs('photos', $nomFichier, 'public');
            $urlPhoto = '/storage/' . $chemin;
            
        } elseif ($request->filled('photo_url')) {
            $request->validate([
                'photo_url' => 'required|url',
            ]);
            
            $urlPhoto = $request->photo_url;
        } else {
            return back()->withErrors(['photo' => 'Vous devez fournir soit un fichier, soit une URL.']);
        }
        
        $photoId = DB::table('photos')->insertGetId([
            'titre' => $request->titre,
            'url' => $urlPhoto,
            'note' => $request->note,
            'album_id' => $request->album_id,
        ]);
        
        if ($request->has('tags') && !empty($request->tags)) {
            foreach ($request->tags as $tagId) {
                DB::table('possede_tag')->insert([
                    'photo_id' => $photoId,
                    'tag_id' => $tagId,
                ]);
            }
        }
        
        return redirect()
            ->route('voir-album', $request->album_id)
            ->with('success', 'Photo ajoutée avec succès !');
    }
    
    public function destroy($id)
    {
        $photo = DB::table('photos')->where('id', $id)->first();
        
        if (!$photo) {
            abort(404, 'Photo non trouvée');
        }
        
        if (strpos($photo->url, '/storage/photos/') === 0) {
            $cheminFichier = str_replace('/storage/', '', $photo->url);
            Storage::disk('public')->delete($cheminFichier);
        }
        
        DB::table('possede_tag')->where('photo_id', $id)->delete();
        DB::table('photos')->where('id', $id)->delete();
        
        return redirect()
            ->route('voir-album', $photo->album_id)
            ->with('success', 'Photo supprimée avec succès !');
    }
}

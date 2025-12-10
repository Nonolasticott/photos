<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $nomTag = $request->input('nom');
        
        if (!$nomTag) {
            return response()->json([
                'success' => false,
                'message' => 'Le nom du tag est requis'
            ], 400);
        }
        
        try {
            $tagExistant = DB::table('tags')->where('nom', $nomTag)->first();
            if ($tagExistant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce tag existe déjà'
                ], 409);
            }
            
            $tagId = DB::table('tags')->insertGetId([
                'nom' => $nomTag,
            ]);
            
            $tag = DB::table('tags')->where('id', $tagId)->first();

            return response()->json([
                'success' => true,
                'tag' => [
                    'id' => $tag->id,
                    'nom' => $tag->nom,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}

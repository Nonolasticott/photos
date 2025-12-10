<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100|unique:tags,nom',
        ]);

        try {
            $tag = DB::table('tags')->insertGetId([
                'nom' => $request->input('nom'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'tag' => [
                    'id' => $tag,
                    'nom' => $request->input('nom'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du tag'
            ], 500);
        }
    }
}

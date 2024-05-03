<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        // Recherche les utilisateurs par leur nom d'utilisateur ou leur nom
        $searchResults = User::query()
            ->where('username', 'LIKE', "%{$search}%")
            ->orWhere('name', 'LIKE', "%{$search}%")
            ->get();

        // Passe les résultats de la recherche à la vue
        return Inertia::render('Search', [
            'searchResults' => $searchResults
        ]);
    }
}

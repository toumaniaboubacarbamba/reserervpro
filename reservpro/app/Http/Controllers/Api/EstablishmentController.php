<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstablishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Autorisation via la Policy (on vérifie si l'utilisateur a le droit de créer)
        $this->authorize('create', Establishment::class);

        // 2. Validation (champs obligatoires pour la création, contrairement au update)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
        ]);

        // 3. Liaison de l'établissement à l'utilisateur connecté (owner_id)
        // On fusionne l'ID de l'utilisateur authentifié avec les données validées
        $validated['owner_id'] = $request->user()->id;

        // 4. Création en base de données
        $establishment = Establishment::create($validated);

        // 5. Retour de la réponse JSON avec un statut 201 (Created)
        return response()->json([
            'message' => 'Établissement créé avec succès.',
            'data' => $establishment,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Establishment $establishment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Establishment $establishment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Establishment $establishment)
{
    $this->authorize('update', $establishment);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'category' => 'sometimes|string|max:255',
        'address' => 'sometimes|nullable|string|max:255',
        'description' => 'sometimes|nullable|string',
        'is_published' => 'sometimes|boolean',
    ]);

    $establishment->update($validated);

    return response()->json([
        'message' => 'Établissement mis à jour avec succès.',
        'data' => $establishment,
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Establishment $establishment): JsonResponse
{
    // 1. Vérification des droits d'accès via la Policy
    $this->authorize('delete', $establishment);

    // 2. Suppression de l'établissement
    $establishment->delete();

    // 3. Retour de la réponse JSON (Statut 200 ou 204 selon vos préférences)
    return response()->json([
        'message' => 'Établissement supprimé avec succès.'
    ], 200);
}
}

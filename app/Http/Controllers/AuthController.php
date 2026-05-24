<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/**
 * Contrôleur pour gérer l'authentification des utilisateurs.
 */
#[OA\Info(
    version: "1.0.0",
    title: "CAMWATER PRO API",
    description: "API REST sécurisée pour la gestion des abonnés, factures et réclamations de CAMWATER"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Serveur de développement"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class AuthController extends Controller
{
    /**
     * Authentifie un utilisateur et génère un token API.
     */
    #[OA\Post(
        path: "/api/login",
        summary: "Connexion utilisateur",
        description: "Authentifie un utilisateur avec email et mot de passe, retourne un token API",
        tags: ["Authentification"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@camwater.cm"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Connexion réussie."),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "user",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 1),
                                        new OA\Property(property: "name", type: "string", example: "Admin CAMWATER"),
                                        new OA\Property(property: "email", type: "string", example: "admin@camwater.cm")
                                    ],
                                    type: "object"
                                ),
                                new OA\Property(property: "token", type: "string", example: "1|abc123..."),
                                new OA\Property(property: "token_type", type: "string", example: "Bearer")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Identifiants invalides"
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        // Validation des données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rechercher l'utilisateur
        $user = User::where('email', $request->email)->first();

        // Vérifier les identifiants
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Générer un token API
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Déconnecte l'utilisateur en révoquant son token.
     */
    #[OA\Post(
        path: "/api/logout",
        summary: "Déconnexion utilisateur",
        description: "Révoque le token API de l'utilisateur authentifié",
        security: [["bearerAuth" => []]],
        tags: ["Authentification"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Déconnexion réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Déconnexion réussie.")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Non authentifié"
            )
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ], 200);
    }

    /**
     * Retourne les informations de l'utilisateur authentifié.
     */
    #[OA\Get(
        path: "/api/me",
        summary: "Informations utilisateur",
        description: "Retourne les informations de l'utilisateur authentifié",
        security: [["bearerAuth" => []]],
        tags: ["Authentification"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Informations utilisateur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Admin CAMWATER"),
                                new OA\Property(property: "email", type: "string", example: "admin@camwater.cm")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Non authentifié"
            )
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ], 200);
    }
}

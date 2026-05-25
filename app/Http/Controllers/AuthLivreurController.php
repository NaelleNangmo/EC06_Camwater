<?php
namespace App\Http\Controllers;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// Authentification livreur via Sanctum
class AuthLivreurController extends Controller
{
    public function login(Request $r)
    {
        $r->validate(["email" => "required|email", "password" => "required"]);
        $livreur = Livreur::where("email", $r->email)->first();
        if (!$livreur || !Hash::check($r->password, $livreur->password)) {
            return response()->json(["message" => "Identifiants invalides"], 422);
        }
        return response()->json(["token" => $livreur->createToken("livreur")->plainTextToken]);
    }
    public function logout(Request $r) { $r->user()->currentAccessToken()->delete(); return response()->json(["message" => "Deconnecte"]); }
}

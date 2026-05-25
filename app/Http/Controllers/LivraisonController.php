<?php
namespace App\Http\Controllers;
use App\Models\Livraison;
use App\Services\LivraisonService;
use Illuminate\Http\Request;
// Controleur CRUD livraisons
class LivraisonController extends Controller
{
    public function __construct(private LivraisonService $service) {}
    public function index()  { return response()->json(["data" => Livraison::paginate(15)]); }
    public function store(Request $r) { return response()->json(["data" => $this->service->creer($r->validated())], 201); }
    public function show(Livraison $livraison) { return response()->json(["data" => $livraison]); }
    public function suivi(Livraison $livraison) { return response()->json(["data" => $this->service->suivi($livraison)]); }
}

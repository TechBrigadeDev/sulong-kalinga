<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipality;

class MunicipalityController extends Controller
{
    public function getAllMunicipalities()
    {
        // Fetch all municipalities from the database
        $municipalities = Municipality::all();

        // Return the municipalities as a JSON response
        return response()->json($municipalities);
    }
}

?>
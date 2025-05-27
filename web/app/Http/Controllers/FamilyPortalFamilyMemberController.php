<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class FamilyPortalFamilyMemberController extends Controller
{
    public function index()
    {
        return view('familyPortal.familyMembers');
    }
}
?>

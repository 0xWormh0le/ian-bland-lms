<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CourseUser;
use App\MyCertificate;
use Auth, Alert;
use Carbon\Carbon;

class MyCertificateController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certificates = MyCertificate::getCertificates(Auth::id());

        
        return view('my-certificates.index', compact('certificates'));
    }

    
    
}

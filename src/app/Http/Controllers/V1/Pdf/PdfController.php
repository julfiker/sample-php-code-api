<?php
namespace App\Http\Controllers\V1\Pdf;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;

class PdfController extends Controller
{
    private $currentUser;

    public function __construct() {
        $this->currentUser = Auth::user();
    }

    public function myData() {
        $data = array("user" => $this->currentUser);
        $pdf = PDF::loadView('pdf.mydata', $data);
        return $pdf->download('invoice.pdf');
    }
}
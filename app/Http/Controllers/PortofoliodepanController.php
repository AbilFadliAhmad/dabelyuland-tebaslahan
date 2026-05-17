<?php

namespace App\Http\Controllers;

use App\Models\Portofolio;
class PortofoliodepanController extends Controller
{
      public function index()
    {
        // Ambil data portofolio dengan pagination
        $portofolios = Portofolio::paginate(6); // 6 item per halaman

        return view('frontside.portfolio', compact('portofolios'));
    }
}

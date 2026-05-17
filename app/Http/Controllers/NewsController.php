<?php

namespace App\Http\Controllers;

class NewsController extends Controller
{
    public function index() {
        return view('frontside.news'); // Sesuaikan path view
    }
}

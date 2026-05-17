<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
class KontakController extends Controller
{
 public function index()
    {
        $contacts = Contact::all();
        return view('frontside.contact', compact('contacts'));
    }
}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        // Nanti ganti dengan query model Spatie/ActivityLog atau tabel log buatan sendiri
        return view('admin.audit.index');
    }
}
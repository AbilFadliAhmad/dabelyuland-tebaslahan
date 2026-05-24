<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $testimonis = Testimoni::all();
        return view('admin.testimoni.list', compact('testimonis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $isEdit = false;
        return view('admin.testimoni.form', compact('isEdit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'rating' => 'required|numeric|between:1.0,5.0', // Batasan minimal 1.0 dan maksimal 5.0
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Maksimal 2MB
        ]);

        if ($request->hasFile('foto')) {
            $validatedData['foto'] = $request->file('foto')->store('testimonials', 'public');
        }

        Testimoni::create($validatedData);

        return redirect()->route('testimonis.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function show(Testimoni $testimoni)
    {
        return view('testimonis.show', compact('testimoni'));
    }

    public function edit(Testimoni $testimoni)
    {
        $isEdit = true;
        return view('admin.testimoni.form', compact('testimoni'), compact('isEdit'));
    }

    public function update(Request $request, Testimoni $testimoni)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($testimoni->foto && Storage::exists($testimoni->foto)) {
                Storage::delete($testimoni->foto);
            }
            $validatedData['foto'] = $request->file('foto')->store('testimonials', 'public');
        }

        $testimoni->update($validatedData);

        return redirect()->route('testimonis.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimoni $testimoni)
    {
        if ($testimoni->foto && Storage::exists($testimoni->foto)) {
            Storage::delete($testimoni->foto);
        }
        $testimoni->delete();

        return redirect()->route('testimonis.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;
class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();
        return view('admin.contacts.list', compact('contacts'));
    }

    public function create()
    {
        $isEdit = false;
        return view('admin.contacts.form', compact('isEdit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string',
            'email' => 'required|email|unique:contacts,email',
            'nowa' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('contacts', 'public');
        }

        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil ditambahkan.');
    }

    public function edit(Contact $contact)
    {
        $isEdit = true;
        return view('admin.contacts.form', compact('contact'), compact('isEdit'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string',
            'email' => 'required|email|unique:contacts,email,'.$contact->id,
            'nowa' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($contact->foto && Storage::disk('public')->exists($contact->foto)) {
                Storage::disk('public')->delete($contact->foto);
            }

            $validated['foto'] = $request->file('foto')->store('contacts', 'public');
        }

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil diperbarui.');
    }

    public function destroy(Contact $contact)
    {
        if ($contact->foto && Storage::disk('public')->exists($contact->foto)) {
            Storage::disk('public')->delete($contact->foto);
        }

        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Kontak berhasil dihapus.');
    }
}

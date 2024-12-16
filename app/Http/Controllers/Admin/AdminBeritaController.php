<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminBeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::all();
        return view('admin.berita.index', compact('beritas'));   
    }

    public function store(Request $request) 
    {
        try {
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'description' => 'required|string|max:255',
                'date' => 'required|date',
            ]);
    
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }
    
            Berita::create([
                'image' => $imagePath,
                'description' => $request->description,
                'date' => $request->date,
            ]);
    
            return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.berita.index')->with('failed', 'Terjadi kesalahan, perubahan gagal!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5000',
                'description' => 'required|string|max:255',
                'date' => 'required|date',
            ]);
    
            $berita = Berita::findOrFail($id);
    
            
            if ($request->hasFile('image')) {
                if ($berita->image && Storage::exists($berita->image)) {
                    Storage::delete($berita->image);
                }
                
                $path = $request->file('image')->store('images', 'public');
                $berita->image = $path;
            }
    
            $berita->description = $request->description;
            $berita->date = $request->date;
            
            $berita->save();
    
            return redirect()->route('admin.berita.index')->with('success', 'Berita Berhasil Diubah!');
        } catch (\Exception $e) {
            return redirect()->route('admin.berita.index')->with('failed', 'Terjadi kesalahan, perubahan gagal!');
        }
    }

    public function delete($id)
    {
        try {
            $berita = Berita::findOrFail($id);

            if ($berita->image && Storage::exists($berita->image)) {
                Storage::delete($berita->image);
            }

            $berita->delete();

            return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.berita.index')->with('failed', 'Terjadi kesalahan, data gagal dihapus!');
        }
    }

}
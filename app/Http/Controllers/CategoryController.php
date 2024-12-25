<?php

namespace App\Http\Controllers; // Menentukan namespace untuk controller ini agar sesuai dengan struktur folder.

use App\Models\Category; // Mengimpor model Category yang merepresentasikan tabel kategori di database.
use Illuminate\Http\Request; // Mengimpor Request untuk menangani data permintaan dari klien.

class CategoryController extends Controller // Membuat kelas CategoryController yang merupakan turunan dari Controller Laravel.
{
    /**
     * Display a listing of the resource.
     */
    public function index() // Fungsi untuk menampilkan semua data kategori.
    {
        $categories = Category::all(); // Mengambil semua data kategori dari database.
        return response()->json($categories); // Mengirimkan data kategori dalam format JSON sebagai respons.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // Fungsi untuk menyimpan kategori baru.
    {
        Category::create($request->all()); // Membuat kategori baru di database berdasarkan data dari permintaan.
        return response()->json(["message" => "Berhasil"]); // Mengembalikan pesan sukses dalam format JSON.
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category) // Fungsi untuk menampilkan data kategori tertentu berdasarkan ID.
    {
        return response()->json($category); // Mengirimkan data kategori yang diminta dalam format JSON.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category) // Fungsi untuk memperbarui data kategori yang ada.
    {
        $category->update($request->all()); // Memperbarui data kategori dengan data baru dari permintaan.
        return response()->json($category); // Mengembalikan data kategori yang diperbarui dalam format JSON.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category) // Fungsi untuk menghapus kategori tertentu.
    {
        $category->delete(); // Menghapus data kategori dari database.
        return response()->json(["message" => "Berhasil"]); // Mengembalikan pesan sukses dalam format JSON.
    }
}

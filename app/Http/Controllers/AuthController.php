<?php

namespace App\Http\Controllers\API; // Menentukan namespace untuk controller ini agar dapat diorganisasikan dalam folder API.

use App\Http\Controllers\Controller; // Mengimpor Controller dasar dari Laravel.
use Illuminate\Http\Request; // Mengimpor Request untuk menangani data permintaan dari klien.
use Illuminate\Support\Facades\Auth; // Mengimpor Auth untuk autentikasi pengguna.
use App\Models\User; // Mengimpor model User yang merepresentasikan tabel pengguna.
use Illuminate\Support\Facades\Validator; // Mengimpor Validator untuk memvalidasi data input.

class AuthController extends Controller // Membuat kelas AuthController yang merupakan turunan dari Controller.
{
    public function register(Request $permintaan) // Fungsi untuk menangani pendaftaran pengguna baru.
    {
        $aturan = [ // Aturan validasi untuk input pengguna.
            'name' => 'required', // Nama wajib diisi.
            'email' => 'required|email', // Email wajib diisi dan harus valid.
            'password' => 'required', // Password wajib diisi.
            'confirm_password' => 'required|same:password', // Konfirmasi password wajib diisi dan harus sama dengan password.
        ];

        $validasi = Validator::make($permintaan->all(), $aturan); // Melakukan validasi input berdasarkan aturan.

        if ($validasi->fails()) { // Jika validasi gagal, kembalikan respons error.
            return response()->json([
                'status' => 'error', // Status respons.
                'kode' => 400, // Kode HTTP untuk Bad Request.
                'keterangan' => 'Data yang diberikan tidak valid', // Pesan error.
                'detail' => $validasi->errors() // Detil error dari validasi.
            ], 400);
        }

        $dataPengguna = $permintaan->all(); // Mengambil semua data dari permintaan.
        $dataPengguna['password'] = bcrypt($dataPengguna['password']); // Mengenkripsi password menggunakan bcrypt.
        $penggunaBaru = User::create($dataPengguna); // Membuat pengguna baru di database.

        $hasil = [ // Menyiapkan data untuk respons.
            'token_akses' => $penggunaBaru->createToken('token_auth')->plainTextToken, // Membuat token akses untuk pengguna.
            'nama_pengguna' => $penggunaBaru->name, // Menyertakan nama pengguna dalam respons.
        ];

        return response()->json([ // Mengirim respons sukses.
            'status' => 'sukses', // Status respons.
            'kode' => 201, // Kode HTTP untuk Created.
            'pesan' => 'Pengguna berhasil terdaftar', // Pesan sukses.
            'data' => $hasil // Data pengguna yang baru dibuat.
        ], 201);
    }

    public function login(Request $permintaan) // Fungsi untuk menangani login pengguna.
    {
        $kredensial = ['email' => $permintaan->email, 'password' => $permintaan->password]; // Mendapatkan email dan password dari permintaan.

        if (Auth::attempt($kredensial)) { // Mengecek apakah kredensial valid.
            $pengguna = Auth::user(); // Mengambil data pengguna yang sedang login.

            $respons = [ // Menyiapkan data untuk respons.
                'token_akses' => $pengguna->createToken('token_auth')->plainTextToken, // Membuat token akses untuk pengguna.
                'nama_pengguna' => $pengguna->name, // Menyertakan nama pengguna.
                'email_pengguna' => $pengguna->email, // Menyertakan email pengguna.
            ];

            return response()->json([ // Mengirim respons sukses.
                'status' => 'sukses', // Status respons.
                'kode' => 200, // Kode HTTP untuk OK.
                'pesan' => 'Login berhasil', // Pesan sukses.
                'data' => $respons // Data pengguna yang sedang login.
            ], 200);
        }

        return response()->json([ // Jika kredensial salah, mengirim respons error.
            'status' => 'gagal', // Status respons.
            'kode' => 401, // Kode HTTP untuk Unauthorized.
            'pesan' => 'Email atau kata sandi tidak sesuai', // Pesan error.
            'data' => null // Tidak ada data tambahan.
        ], 401);
    }
}

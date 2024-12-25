<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $permintaan)
    {
        $aturan = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ];

        $validasi = Validator::make($permintaan->all(), $aturan);

        if ($validasi->fails()) {
            return response()->json([
                'status' => 'error',
                'kode' => 400,
                'keterangan' => 'Data yang diberikan tidak valid',
                'detail' => $validasi->errors()
            ], 400);
        }

        $dataPengguna = $permintaan->all();
        $dataPengguna['password'] = bcrypt($dataPengguna['password']);
        $penggunaBaru = User::create($dataPengguna);

        $hasil = [
            'token_akses' => $penggunaBaru->createToken('token_auth')->plainTextToken,
            'nama_pengguna' => $penggunaBaru->name,
        ];

        return response()->json([
            'status' => 'sukses',
            'kode' => 201,
            'pesan' => 'Pengguna berhasil terdaftar',
            'data' => $hasil
        ], 201);
    }

    public function login(Request $permintaan)
    {
        $kredensial = ['email' => $permintaan->email, 'password' => $permintaan->password];

        if (Auth::attempt($kredensial)) {
            $pengguna = Auth::user();

            $respons = [
                'token_akses' => $pengguna->createToken('token_auth')->plainTextToken,
                'nama_pengguna' => $pengguna->name,
                'email_pengguna' => $pengguna->email,
            ];

            return response()->json([
                'status' => 'sukses',
                'kode' => 200,
                'pesan' => 'Login berhasil',
                'data' => $respons
            ], 200);
        }

        return response()->json([
            'status' => 'gagal',
            'kode' => 401,
            'pesan' => 'Email atau kata sandi tidak sesuai',
            'data' => null
        ], 401);
    }
}

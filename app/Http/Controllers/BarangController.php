<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BarangController extends Controller
{
    public function index()
    {
        $response = [
            'message' => "Daftar Barang",
            "data" => Barang::latest()->get(),
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $input)
    {
        $validator = Validator::make($input->all(), [
            'kode_barang' => ['required', 'unique:barangs,kode_barang', 'size:10'],
            'nama_barang' => ['required', 'string'],
            'stok' => ['required', 'numeric'],
            'satuan' => ['required', 'string'],
            'harga' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Gagal menyimpan data!',
                'data' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = Barang::create([
                'kode_barang' => $input->kode_barang,
                'nama_barang' => $input->nama_barang,
                'stok' => $input->stok,
                'satuan' => $input->satuan,
                'harga' => $input->harga,
                'user_id' => Auth::user()->id
            ]);
            $response = [
                'message' => "Data berhasil ditambahkan",
                'data' => [
                    'user' => $data,
                ]
            ];
            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Store Data Error!",
                'error' => $e->errorInfo
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}

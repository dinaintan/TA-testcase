<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Menampilkan form upload.
     */
    public function index()
    {
        return view('upload');
    }

    /**
     * Memproses file yang diunggah dan menyimpannya.
     */
    public function proses(Request $request)
    {
        // Validasi: hanya file yang diupload, wajib ada
        $request->validate([
            'puml_file' => 'required|file'
        ]);

        // Pastikan file berekstensi .puml
        $ext = strtolower($request->file('puml_file')->getClientOriginalExtension());
        if ($ext !== 'puml') {
            return back()
                ->withErrors(['puml_file' => 'File harus dalam format .puml'])
                ->withInput();
        }

        // Simpan file ke storage/app/public/uploads
        $path = $request->file('puml_file')->store('uploads', 'public');

        // Simpan path ke session
        session()->put('uploaded_puml_path', $path);

        // Redirect ke halaman tabel ADT
        return redirect()->route('tableadt');
    }

    /**
     * Menampilkan tabel hasil parsing file PUML (ADT).
     */
    public function tampiltabel()
    {
        // Ambil path dari session
        $path = session('uploaded_puml_path');
        if (!$path) {
            return redirect()->route('upload.form')->with('error', 'Tidak ada file yang diunggah.');
        }

        $fullPath = Storage::disk('public')->path($path);
        if (!is_file($fullPath)) {
            return redirect()->route('upload.form')->with('error', 'File tidak ditemukan di server.');
        }

        // Pastikan helper ada
        if (!function_exists('parsePumlToJson')) {
            require_once app_path('helpers/parser.php');
        }

        // Jalankan parser
        $result = parsePumlToJson($fullPath) ?? [];

        // Simpan ke session untuk graph
        session()->put('parsed_data', $result);

        return view('tableadt', ['data' => $result]);
    }

    /**
     * Menampilkan graph hasil parsing file PUML (ADG).
     */
    public function tampilgraph()
    {
        $data = session('parsed_data', []);
        if (empty($data)) {
            return redirect()->route('upload.form')->with('error', 'Data belum diproses.');
        }

        return view('tableadg', ['data' => $data]);
    }
}

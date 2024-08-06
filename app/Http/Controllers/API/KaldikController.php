<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kaldik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KaldikController extends Controller
{
    //menampilkan data kaldik
    public function dashboardKaldik()
    {
        try {
            $kaldik = DB::table('kaldiks')
            ->where('tahun',Carbon::now()->format('Y'))->get();
            return response()->json([
                'status' => 200,
                'all_kaldik' => $kaldik
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], $th->getCode());
        }
    }

    //tambah data kaldik
    public function tambahKaldik(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'tahun' => 'required|integer',
        'semester' => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        'waktu_mulai' => 'required|date',
        'waktu_selesai' => 'required|date',
        'status' => 'required|in:kuliah,fakultatif,libur',
        'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'keterangan' => 'nullable|string',
    ], [
        'status.in' => 'Status harus salah satu dari: kuliah, fakultatif, libur.',
        'lampiran.file' => 'Lampiran harus berupa file.',
        'lampiran.mimes' => 'Lampiran harus berupa file dengan ekstensi pdf, doc, atau docx.',
        'lampiran.max' => 'Lampiran tidak boleh lebih dari 2MB.',
    ]);

    $data = $request->except('lampiran'); // Ambil data selain file
    if ($request->hasFile('lampiran')) {
        $file = $request->file('lampiran');
        $filePath = $file->store('lampiran', 'public'); // Menyimpan file ke storage/app/public/lampiran
        $data['lampiran'] = $filePath;
    } else {
        $data['lampiran'] = null; // Set lampiran ke null jika tidak ada file
    }

    // Cek jika validasi gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Buat entri kaldik baru
    $kaldik = Kaldik::create($data);

    return response()->json([
        'message' => 'Kaldik added successfully',
        'data' => $kaldik,
    ], 201);
}


    public function updateKaldik(Request $request)
{
    // Ambil ID dari body request
    $id = (int) $request->input('id');
    Log::info('Parsed ID:', ['id' => $id]);

    // Temukan entri kaldik berdasarkan ID
    $kaldik = Kaldik::find($id);

    // Cek jika entri tidak ditemukan
    if (!$kaldik) {
        Log::info('Kaldik not found:', ['id' => $id]);
        return response()->json([
            'message' => 'Kaldik not found',
        ], 404);
    }

    // Validasi input
    $validator = Validator::make($request->all(), [
        'id' => 'required|integer|exists:kaldiks,id', // Validasi ID
        'tahun' => 'required|integer',
        'semester' => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        'waktu_mulai' => 'required|date',
        'waktu_selesai' => 'required|date',
        'status' => 'required|in:kuliah,fakultatif,libur',
        'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        'keterangan' => 'nullable|string',
    ], [
        'status.in' => 'Status harus salah satu dari: kuliah, fakultatif, libur.',
        'lampiran.file' => 'Lampiran harus berupa file.',
        'lampiran.mimes' => 'Lampiran harus berupa file dengan ekstensi pdf, doc, atau docx.',
        'lampiran.max' => 'Lampiran tidak boleh lebih dari 2MB.',
    ]);

    // Cek jika validasi gagal
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Tangani file upload
    $data = $request->except('lampiran'); // Ambil data selain file
    if ($request->hasFile('lampiran')) {
        // Hapus file lama jika ada
        if ($kaldik->lampiran && Storage::exists($kaldik->lampiran)) {
            Storage::delete($kaldik->lampiran);
        }

        $file = $request->file('lampiran');
        $filePath = $file->store('lampiran', 'public'); // Menyimpan file ke storage/app/public/lampiran
        $data['lampiran'] = $filePath;
    }

    // Update entri kaldik
    $kaldik->update($data);

    return response()->json([
        'message' => 'Kaldik updated successfully',
        'data' => $kaldik,
    ], 200);
}














}

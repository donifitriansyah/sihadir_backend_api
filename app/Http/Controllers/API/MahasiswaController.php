<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Ket_mhs;
use App\Models\Presensi;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Kompen_mhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\Catch_;

class MahasiswaController extends Controller
{
    public function profilMahasiswa(Request $request)
    {
        $nim = $request->nomor_induk;
        try {
            $Mahasiswa = Mahasiswa::select('nim', 'nama', 'foto')
                ->where('nim', $nim)->get();
            return response()->json([
                'status' => 200,
                $Mahasiswa
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], $th->getCode());
        }
    }
    public function daftarKetidakhadiranMhs(Request $request)
    {
        // $nim = $request->query('nomor_induk');
        // $nomor_induk = $request->query('nomor_induk');
        $ket_mhs = Ket_mhs::where("id_presensi", $request->id_presensi)->first();
        $nim = Mahasiswa::where("nim", $request->nomor_induk)->first();
        $presensis = Presensi::where("id_presensi", $request->id_presensi)->first();

        return [
            'k' => $ket_mhs,
            'n' => $nim,
            'p' => $presensis
        ];
        if ($ket_mhs !== null) {
            if ($nim !== null) {
                try {
                    if ($ket_mhs->id_presensi == $presensis->id_presensi) {
                        if (now()->lt($ket_mhs->limit_surat)) {
                            // $nomor_induk = $request->query('nomor_induk');
                            // $Jadwal = Jadwal::where("id_jdwl", $request->id_jdwl)->first();
                            // $inputToken = Jadwal::where("token", $request->token)->first();
                            // if ($Jadwal !== null) {
                            //     if ($inputToken !== null) {
                            //         try {
                            // if ($Jadwal->token == $inputToken->token) {
                            //     if (now()->lt($inputToken->expires_at)) {
                            $checkKehadiran = DB::table('mahasiswas')
                                ->join('kelas', 'mahasiswas.id_kls', '=', 'kelas.id_kls')
                                ->join('presensis', 'mahasiswas.id_mhs', '=', 'presensis.id_mhs',)
                                ->join('ket_mhs', 'presensis.id_presensi', '=', 'ket_mhs.id_presensi')
                                ->join('logs', 'presensis.id_tahun_ajar', '=', 'logs.id_tahun_ajar')
                                ->join('dosens', 'logs.id_dosen', '=', 'dosens.id_dosen')
                                ->where('mahasiswas.nim', '=', $nim)
                                ->where('presensis.status', '=', 'A')
                                ->where('ket_mhs.status_confirm', '=', '0')
                                ->where('surat_bukti', '<>', NULL)
                                // ->whereNotNull('surat_bukti')
                                ->select('presensis.id_presensi', 'presensis.status', 'mahasiswas.nama', 'mahasiswas.nim', 'kelas.smt', 'kelas.abjad_kls')
                                ->get();
                        }
                    }
                    // ->insert();
                    // $limitSurat = now()->addDays(2);
                    // foreach ($konfirmKehadiran as $value) {
                    //     if ($konfirmKehadiran) {
                    //         DB::table('ket_mhs')
                    //             ->insert([
                    //                 'id_presensis' => $value->id_presensi,
                    //                 'status_confirm' => 0,
                    //                 'surat_bukti' => 'null',
                    //                 'deskripsi' => 'null',
                    //                 'limit_surat' => $limitSurat,
                    //                 'created_at' => now(),
                    //                 'updated_at' => now()
                    //             ]);
                    //     }
                    // }
                } catch (\Throwable $th) {
                    return response()->json([
                        "error" => $th->getMessage()
                    ], $th->getCode());
                }
                // return response()->json([
                //     'status' => 200,
                //     'daftarKetidakHadiran' => $konfirmKehadiran
                // ], 200);
            }
        }
    }

    public function kirimSuratKetidakhadiran(Request $request)
    {
        $nim = $request->input('nomor_induk');
        $sts = $request->input('status');
        $alamatsurat = $request->input('surat');
        $keterangan = $request->input('keterangan');

        // Log parameter yang diterima
        Log::info('Received parameters', compact('nim', 'sts', 'alamatsurat', 'keterangan'));

        // Query untuk mendapatkan data mahasiswa
        $mahasiswa = DB::table('mahasiswas')
            ->where('nim', '=', $nim)
            ->first();
        Log::info('Mahasiswa Data', ['data' => $mahasiswa]);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 404,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        // Query untuk mendapatkan data presensi
        $presensi = DB::table('presensis')
            ->where('id_mhs', '=', $mahasiswa->id_mhs)
            ->where('status', '=', 'A')
            ->first();
        Log::info('Presensi Data', ['data' => $presensi]);

        if (!$presensi) {
            return response()->json([
                'status' => 404,
                'message' => 'Data presensi tidak ditemukan'
            ], 404);
        }

        // Query untuk mendapatkan data ketidakhadiran
        $ketidakhadiran = DB::table('ket_mhs')
            ->where('id_presensi', '=', $presensi->id_presensi)
            ->where('status_confirm', '=', '0')
            ->first();
        Log::info('Ketidakhadiran Data', ['data' => $ketidakhadiran]);

        if ($ketidakhadiran) {
            return response()->json([
                'status' => 404,
                'message' => 'Data ketidakhadiran sudah ada dan belum dikonfirmasi'
            ], 404);
        }

        // Insert data ketidakhadiran
        $insertKetidakHadiran = DB::table('ket_mhs')->insert([
            'id_presensi' => $presensi->id_presensi,
            'status_confirm' => 0,
            'surat_bukti' => $alamatsurat,
            'deskripsi' => $keterangan,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        Log::info('Insert KetidakHadiran', ['data' => $insertKetidakHadiran]);

        // Update data presensi
        $updatePresensi = DB::table('presensis')
            ->where('id_presensi', '=', $presensi->id_presensi)
            ->update(['status' => $sts]);
        Log::info('Update Presensi', ['data' => $updatePresensi]);

        return response()->json([
            'status' => 200,
            'daftarKetidakHadiran' => $insertKetidakHadiran,
            'update_status' => $updatePresensi
        ], 200);
    }


    public function jadwalHariIniMhs(Request $request)
    {
        $nim = $request->nomor_induk;
        try {
            $date = Carbon::parse(now())->locale('id');
            $date->settings(['formatFunction' => 'translatedFormat']);
            $day = $date->format('l');

            $jadwalHariIni = DB::table('jadwals')
                ->join('logs', 'jadwals.id_jdwl', '=', 'logs.id_jdwl')
                ->join('dosens', 'logs.id_dosen', '=', 'dosens.id_dosen')
                ->join('matkuls', 'jadwals.id_mk', '=', 'matkuls.id_mk')
                ->join('kelas', 'jadwals.id_kls', '=', 'kelas.id_kls')
                ->join('mahasiswas', 'kelas.id_kls', '=', 'mahasiswas.id_kls')
                ->where('jadwals.hari', '=', $day)
                ->orderBy('jadwals.start')
                ->whereRaw('jadwals.finish > curtime() ')
                ->where('mahasiswas.nim', '=', $nim)
                ->select('jadwals.id_jdwl', 'matkuls.nama as Mata Kuliah', DB::raw('CONCAT(TIME_FORMAT(jadwals.start, "%H.%i"), " - ",TIME_FORMAT(jadwals.finish , "%H.%i")) AS Waktu'), 'kelas.smt', 'kelas.abjad_kls', 'jadwals.ruang', 'jadwals.jumlah_jam')
                ->get();
            return response()->json([
                'status' => '200',
                'jadwalHariIni' => $jadwalHariIni
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function LaporanMhs(Request $request)
{
    $nim = $request->input('nim');

    try {
        // Find the mahasiswa by NIM
        $mahasiswa = Mahasiswa::where('nim', $nim)
            ->with([
                'presensi' => function($query) {
                    $query->select('id_mhs', 'status', 'ketidakhadiran');
                },
                'kompen_mhs' => function($query) {
                    $query->select('id_mhs', 'jumlah_kompen');
                }
            ])
            ->firstOrFail(['id_mhs', 'nim', 'nama']);

        // Prepare the report data
        $report = [
            'id_mhs' => $mahasiswa->id_mhs,
            'nim' => $mahasiswa->nim,
            'nama' => $mahasiswa->nama,
            'status' => optional($mahasiswa->presensi)->status,
            'ketidakhadiran' => optional($mahasiswa->presensi)->ketidakhadiran,
            'jumlah_kompen' => optional($mahasiswa->kompen_mhs)->jumlah_kompen,
        ];

        // Return the report as JSON
        return response()->json([
            'message' => 'Laporan berhasil diambil',
            'data' => $report,
        ], 200);

    } catch (\Exception $e) {
        // Log the error
        Log::error('Error retrieving report: ' . $e->getMessage());

        return response()->json([
            'message' => 'Tidak berhasil mengambil laporan',
        ], 500);
    }
}

public function LaporanMhsdosen(Request $request)
{
    $id_kls = $request->input('id_kls');

    try {
        // Fetch all students related to the given id_kls
        $mahasiswaList = Mahasiswa::where('id_kls', $id_kls)
            ->with([
                'presensi' => function($query) {
                    $query->select('id_mhs', 'status', 'ketidakhadiran');
                },
                'kompen_mhs' => function($query) {
                    $query->select('id_mhs', 'jumlah_kompen');
                },
                'kelas' => function($query) {
                    $query->select('id_kls', 'abjad_kls', 'smt');
                }
            ])
            ->get(['id_mhs', 'nim', 'nama', 'id_kls']);

        // Check if any data was found
        if ($mahasiswaList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada mahasiswa ditemukan dengan ID kelas ini',
                'data' => [],
            ], 200);
        }

        // Prepare the report data
        $report = $mahasiswaList->map(function($mahasiswa) {
            return [
                'id_kls' => optional($mahasiswa->kelas)->id_kls,
                'abjad_kls' => optional($mahasiswa->kelas)->abjad_kls,
                'smt' => optional($mahasiswa->kelas)->smt,
                'id_mhs' => $mahasiswa->id_mhs,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama,
                'status' => optional($mahasiswa->presensi)->status,
                'ketidakhadiran' => optional($mahasiswa->presensi)->ketidakhadiran,
                'jumlah_kompen' => optional($mahasiswa->kompen_mhs)->jumlah_kompen,
            ];
        });

        // Return the report as JSON
        return response()->json([
            'message' => 'Laporan berhasil diambil',
            'data' => $report,
        ], 200);

    } catch (\Exception $e) {
        // Log the error
        Log::error('Error retrieving report: ' . $e->getMessage());

        return response()->json([
            'message' => 'Tidak berhasil mengambil laporan',
        ], 500);
    }
}
    


}



<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Http\Resources\NilaiResource;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nilais = Nilai::with('details') // Eager load the details relationship
            ->orderBy('created_at', 'desc')
            ->get();
        return new NilaiResource(true, 'List of Nilai', $nilais);
    }
    private function getIndexNilai(float $nilai_akhir): string
    {
        if ($nilai_akhir >= 94 && $nilai_akhir <= 100) {
            return 'A';
        } elseif ($nilai_akhir >= 86 && $nilai_akhir < 94) {
            return 'B';
        } elseif ($nilai_akhir >= 80 && $nilai_akhir < 86) {
            return 'C';
        }

        return 'D'; // default jika di bawah 80
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
            'tanggal' => 'required|date',
            'penilaians' => 'required|array',
            'penilaians.*.siswa_id' => 'required|exists:siswas,id',
            'penilaians.*.kehadiran' => 'required|string',
            'penilaians.*.keaktifan' => 'required|string',
            'penilaians.*.praktik' => 'required|string',
            'penilaians.*.keterangan' => 'nullable|string|max:255',
        ]);
        $nilai = Nilai::create([
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
            'tanggal' => $validated['tanggal'],
        ]);
        foreach ($validated['penilaians'] as $data) {
            // Konversi sementara ke float untuk perhitungan
            $kehadiran = (float) $data['kehadiran'];
            $keaktifan = (float) $data['keaktifan'];
            $praktik = (float) $data['praktik'];

            $nilai_akhir = ($kehadiran * 0.4) + ($keaktifan * 0.3) + ($praktik * 0.3);
            $index_nilai = $this->getIndexNilai($nilai_akhir);
            // Simpan ke tabel nilai (contoh: model Nilai)
            $nilai->details()->create([
                'siswa_id' => $data['siswa_id'],
                'kehadiran' => $data['kehadiran'], // string
                'keaktifan' => $data['keaktifan'], // string
                'praktik' => $data['praktik'],   // string
                'nilai_akhir' => (string) round($nilai_akhir, 2),
                'index_nilai' => $index_nilai,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }
        // Eager load the details relationship to include in the response
        $nilai->load('details');

        return new NilaiResource(true, 'Nilai Created Successfully', $nilai);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nilai = Nilai::with('details.siswa')->find($id);
        // $nilai->load('details.siswa'); // Eager load details relationship
        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        return new NilaiResource(true, 'Nilai Found', $nilai);
    }
    // buat showByIdEkskul
    public function showByEkskul($ekskulId, $total_page)
    {
        // $nilai = Nilai::with('details.siswa')->where('ekskul_id', $ekskulId)->first();
        $nilais = Nilai::with('details', 'kelasEkskul.ekskul') // Eager load kelasEkskul dan ekskul
            ->whereHas('kelasEkskul', function ($query) use ($ekskulId) {
                $query->where('ekskul_id', $ekskulId); // Filter berdasarkan ekskul_id
            })
            ->orderBy('created_at', 'desc')
            ->paginate($total_page);

        if (!$nilais) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        return new NilaiResource(true, 'Nilai Found', $nilais);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        $validated = $request->validate([
            'kelas_ekskul_id' => 'required|exists:kelas_ekskuls,id',
            'tanggal' => 'required|date',
            'penilaians' => 'required|array',
            'penilaians.*.siswa_id' => 'required|exists:siswas,id',
            'penilaians.*.kehadiran' => 'required|string',
            'penilaians.*.keaktifan' => 'required|string',
            'penilaians.*.praktik' => 'required|string',
            'penilaians.*.keterangan' => 'nullable|string|max:255',
        ]);

        // Update data utama nilai
        $nilai->update([
            'kelas_ekskul_id' => $validated['kelas_ekskul_id'],
            'tanggal' => $validated['tanggal'],
        ]);

        // Hapus detail nilai lama
        $nilai->details()->delete();

        // Simpan detail nilai baru
        foreach ($validated['penilaians'] as $data) {
            $kehadiran = (float) $data['kehadiran'];
            $keaktifan = (float) $data['keaktifan'];
            $praktik = (float) $data['praktik'];

            $nilai_akhir = ($kehadiran * 0.4) + ($keaktifan * 0.3) + ($praktik * 0.3);
            $index_nilai = $this->getIndexNilai($nilai_akhir);

            $nilai->details()->create([
                'siswa_id' => $data['siswa_id'],
                'kehadiran' => $data['kehadiran'],
                'keaktifan' => $data['keaktifan'],
                'praktik' => $data['praktik'],
                'nilai_akhir' => (string) round($nilai_akhir, 2),
                'index_nilai' => $index_nilai,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        }

        // Muat relasi details untuk ditampilkan dalam respons
        $nilai->load('details');

        return new NilaiResource(true, 'Nilai Updated Successfully', $nilai);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $nilai = Nilai::find($id);

        if (!$nilai) {
            return new NilaiResource(false, 'Nilai Not Found', null);
        }

        $nilai->delete();

        return new NilaiResource(true, 'Nilai Deleted Successfully', null);
    }

    public function showBySiswaId($siswaId, $tahun = null)
    {
        // Ambil nilai berdasarkan siswa_id dari relasi details
        $nilais = Nilai::whereHas('details', function ($query) use ($siswaId) {
            $query->where('siswa_id', $siswaId);
        })
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereYear('tanggal', $tahun); // Filter berdasarkan tahun jika diberikan
            })
            ->with([
                'details' => function ($query) use ($siswaId) {
                    $query->where('siswa_id', $siswaId); // Filter hanya untuk siswa dengan ID spesifik
                },
                'details.siswa', // Tambahkan relasi siswa untuk mendapatkan kelas
                'kelas_ekskul.ekskul',        // Tambahkan relasi ekskul untuk mendapatkan nama_ekskul
            ])
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($nilais->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Nilai Found for the Given Siswa and Year',
            ], 404);
        }

        // Tambahkan field nama_ekskul dan kelas siswa pada setiap item
        $result = $nilais->map(function ($nilai) {
            return [
                'id' => $nilai->id,
                'tanggal' => $nilai->tanggal,
                'nama_ekskul' => $nilai->kelas_ekskul->ekskul->nama_ekskul ?? null, // Ambil nama ekskul
                'details' => $nilai->details->map(function ($detail) {
                    return [
                        'siswa_id' => $detail->siswa_id,
                        'nama_siswa' => $detail->siswa->nama ?? null, // Ambil nama siswa
                        'kelas' => $detail->siswa->kelas ?? null,     // Ambil kelas siswa
                        'kehadiran' => $detail->kehadiran,
                        'keaktifan' => $detail->keaktifan,
                        'praktik' => $detail->praktik,
                        'nilai_akhir' => $detail->nilai_akhir,
                        'index_nilai' => $detail->index_nilai,
                        'keterangan' => $detail->keterangan,
                    ];
                }),
            ];
        });

        return new NilaiResource(true, 'Nilai Found for Siswa', $result);
    }

    public function indexByNilai(Request $request)
    {
        // Ambil parameter sorting dan filter dari query string
        $sortBy = $request->query('sort_by', 'nilai_id'); // Default: nilai_id
        $sortOrder = $request->query('sort_order', 'desc'); // Default: desc
        $tahun = $request->query('tahun'); // Filter berdasarkan tahun

        // Validasi parameter sorting
        $allowedSortBy = ['kelas', 'ekskul', 'nilai_id'];
        if (!in_array($sortBy, $allowedSortBy)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid sort_by parameter. Allowed values: kelas, ekskul, nilai_id',
            ], 400);
        }

        // Ambil data dari tabel nilai_details dengan relasi siswa dan nilai
        $detailNilais = \App\Models\DetailNilai::with(['siswa', 'nilai.ekskul'])
            ->when($tahun, function ($query) use ($tahun) {
                $query->whereHas('nilai', function ($query) use ($tahun) {
                    $query->whereYear('tanggal', $tahun); // Filter berdasarkan tahun
                });
            })
            ->when($sortBy === 'kelas', function ($query) use ($sortOrder) {
                $query->join('siswas', 'detail_nilais.siswa_id', '=', 'siswas.id')
                    ->orderBy('siswas.kelas', $sortOrder);
            })
            ->when($sortBy === 'ekskul', function ($query) use ($sortOrder) {
            $query->join('nilais', 'detail_nilais.nilai_id', '=', 'nilais.id')
                ->join('kelas_ekskuls', 'nilais.kelas_ekskul_id', '=', 'kelas_ekskuls.id')
                ->join('ekskuls', 'kelas_ekskuls.ekskul_id', '=', 'ekskuls.id')
                ->orderBy('ekskuls.nama_ekskul', $sortOrder);
            }, function ($query) use ($sortBy, $sortOrder) {
                $query->orderBy($sortBy, $sortOrder); // Default sorting
            })
            ->get();

        if ($detailNilais->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Nilai Found',
            ], 404);
        }

        // Format data untuk ditampilkan per nilai
        $result = $detailNilais->map(function ($detail) {
            return [
                'nilai_id' => $detail->nilai_id,
                'tanggal' => $detail->nilai->tanggal,
                'ekskul_id' => $detail->nilai->kelas_ekskul->ekskul->ekskul_id,
                'nama_ekskul' => $detail->nilai->kelas_ekskul->ekskul->nama_ekskul ?? null,
                'siswa_id' => $detail->siswa->id,
                'nama_siswa' => $detail->siswa->nama,
                'kelas' => $detail->siswa->kelas,
                'nilai_akhir' => $detail->nilai_akhir,
                'index_nilai' => $detail->index_nilai,
                'keterangan' => $detail->keterangan,
            ];
        });

        return new NilaiResource(true, 'List of Nilai Details', $result);
    }
}

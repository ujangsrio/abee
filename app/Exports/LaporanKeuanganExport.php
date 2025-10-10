<?php

namespace App\Exports;

use App\Models\LabaRugi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = LabaRugi::query();

        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'KATEGORI',
            'ITEM',
            'KETERANGAN',
            'JUMLAH (Rp)',
            'STATUS'
        ];
    }

    public function map($labaRugi): array
    {
        return [
            Carbon::parse($labaRugi->tanggal)->format('d/m/Y'),
            $labaRugi->kategori,
            $labaRugi->nama_item,
            $labaRugi->keterangan ?? '-',
            number_format($labaRugi->jumlah, 0, ',', '.'),
            $labaRugi->kategori === 'Pendapatan' ? 'PENDAPATAN' : 'PENGELUARAN'
        ];
    }
}

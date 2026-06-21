<?php
// services/LaporanService.php

class LaporanService {
    private $laporanRepo;

    public function __construct($laporanRepository) {
        $this->laporanRepo = $laporanRepository;
    }

    // Mengumpulkan dan menyusun data grafik omset bulanan
    public function dataOmsetGrafik($bulan, $tahun) {
        $rawData = $this->laporanRepo->getPendapatanHarian($bulan, $tahun);
        
        $labels = [];
        $datasets = [];
        
        foreach ($rawData as $row) {
            $labels[] = date('d/m', strtotime($row->tanggal));
            $datasets[] = (float)$row->total_pendapatan;
        }

        return [
            "labels" => $labels,
            "datasets" => $datasets,
            "raw" => $rawData
        ];
    }

    public function ringkasanStatistik() {
        return $this->laporanRepo->getRingkasanStatistik();
    }
}
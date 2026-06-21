<?php
// controllers/LaporanController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/LaporanRepository.php';
require_once __DIR__ . '/../services/LaporanService.php';

class LaporanController {
    private $laporanService;

    public function __construct() {
        $database = new Database();
        $dbConn = $database->getConnection();
        
        $laporanRepo = new LaporanRepository($dbConn);
        $this->laporanService = new LaporanService($laporanRepo);
    }

    public function renderDashboardOwner($bulan, $tahun) {
        $grafikData = $this->laporanService->dataOmsetGrafik($bulan, $tahun);
        $statistik = $this->laporanService->ringkasanStatistik();
        
        return [
            "grafik" => $grafikData,
            "statistik" => $statistik
        ];
    }
}
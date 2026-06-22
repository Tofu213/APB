<?php
// controllers/PaymentController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/PaymentRepository.php';
require_once __DIR__ . '/../repositories/SlotRepository.php';
require_once __DIR__ . '/../services/PaymentService.php';

class PaymentController {
    private $paymentService;

    public function __construct() {
        $database = new Database();
        $dbConn = $database->getConnection();
        
        $paymentRepo = new PaymentRepository($dbConn);
        $slotRepo = new SlotRepository($dbConn);
        $this->paymentService = new PaymentService($paymentRepo, $slotRepo);
    }

    public function lihatPermintaanVerifikasi() {
        return $this->paymentService->getDaftarVerifikasi();
    }

    public function handleAksiAdmin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Validasi hak akses Admin
            if (!isset($_SESSION['user_id']) || $_SESSION['peran'] !== 'admin') {
                header("Location: ../login.php");
                exit();
            }

            $id_admin = $_SESSION['user_id'];
            $id_booking = intval($_POST['id_booking'] ?? 0);
            $id_slot = intval($_POST['id_slot'] ?? 0);
            $action = $_POST['action'] ?? '';
            $catatan = htmlspecialchars($_POST['catatan_admin'] ?? '');

            if ($action === 'konfirmasi') {
                $this->paymentService->konfirmasiPembayaran($id_booking, $id_slot, $id_admin);
                header("Location: dashboard.php?status=confirmed");
                exit();
            } elseif ($action === 'tolak') {
                $this->paymentService->tolakPembayaran($id_booking, $id_slot, $id_admin, $catatan);
                header("Location: dashboard.php?status=rejected");
                exit();
            }
        }
    }
}
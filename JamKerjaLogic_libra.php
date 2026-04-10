<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JamKerjaLogic_libra
{

    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('JamKerja_model');
        $this->CI->load->database();
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Cek apakah hari ini hari libur
     */
    public function isHariLibur()
    {
        $today = date('Y-m-d');

        $libur = $this->CI->db
            ->where('tanggal_libnas', $today)
            ->get('hari_libnas')
            ->row();

        return $libur ? true : false;
    }

    /**
     * Ambil jam kerja hari ini dari DB
     */
    public function getJamHariIni()
    {
        return $this->CI->JamKerja_model->getJamHariIni();
    }

    /**
     * Cek apakah sekarang jam kerja
     */
    public function isJamKerja()
    {
        // Kalau hari libur → langsung false
        if ($this->isHariLibur()) {
            return false;
        }

        $data = $this->getJamHariIni();

        if (!$data) {
            return false;
        }

        $jamSekarang = date('H:i:s');

        return ($jamSekarang >= $data->jam_mulai && $jamSekarang <= $data->jam_selesai);
    }

    /**
     * Ambil script JS berdasarkan kondisi
     */
    public function getScript()
    {
        if ($this->isJamKerja()) {
            return "assets/js/tombolKaburNormal.js";
        }

        return "assets/js/tombolKabur.js";
    }

    /**
     * Ambil info jam kerja (untuk ditampilkan di view)
     */
    public function getInfoJam()
    {
        $data = $this->getJamHariIni();

        if (!$data) {
            return "Tidak ada jadwal hari ini";
        }

        $date_first = new DateTime($data->jam_mulai);
        $date_second = new DateTime($data->jam_selesai);

        return $date_first->format('H:i') . " - " . $date_second->format('H:i') . " WIB";
    }

    /**
     * Pesan status (opsional untuk UX)
     */
    public function getStatusMessage()
    {
        if ($this->isHariLibur()) {
            return "Hari ini libur, layanan SPM tutup";
        }

        if ($this->isJamKerja()) {
            return "";
            /**Pesan dikosongkan saat Layanan sedang dibuka dan bukan hari libur */
        }
        $final_message = "<small>Jam Kerja Hari ini: " . $this->getInfoJam() . "</small>";

        return "Di luar jam kerja <br>" . $final_message;
    }
}

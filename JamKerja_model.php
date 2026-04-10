<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JamKerja_model extends CI_Model
{
    public function getJamHariIni()
    {
        $hari = date('N');

        return $this->db
            ->where('hari_jk', $hari)
            ->where('aktif_jk', 1)
            ->get('jam_kerja')
            ->row();
    }
}

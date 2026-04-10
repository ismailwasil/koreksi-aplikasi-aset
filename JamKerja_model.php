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

    public function getDataJamKerja()
    {
        return $this->db->get('jam_kerja')->result_array();
    }

    public function getHariLibur()
    {
        return $this->db->get('hari_libnas')->result_array();
    }

    public function updateJamKerja($id, $jam_mulai, $jam_selesai, $status)
    {
        $this->db->where('id_jk', $id);
        return $this->db->update('jam_kerja', [
            'jam_mulai'   => $jam_mulai,
            'jam_selesai'    => $jam_selesai,
            'aktif_jk' => $status
        ]);
    }
}

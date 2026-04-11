<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');


        if ($this->session->userdata('id_role') != 2) {
            redirect('auth/');
        }

        // using helper instead
        // is_logged_in();
    }

    public function index()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['title'] = "Dashboard";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/dashboard', $data);
        $this->load->view('templates/page_footer');
    }

    public function inventaris()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['title'] = "Inventaris";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/inventaris', $data);
        $this->load->view('templates/page_footer');
    }

    public function stock()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['title'] = "Persediaan";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/stock');
        $this->load->view('templates/page_footer');
    }

    public function stock2()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['title'] = "Persediaan";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/stock2');
        $this->load->view('templates/page_footer');
    }

    public function aset()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Aset";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/lasada2', $data);
        $this->load->view('templates/page_footer');
    }

    public function lasada()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Aset";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/sewa', $data);
        $this->load->view('templates/page_footer');
    }

    public function layanan_lainnya()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Aset";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/layanan-lainnya', $data);
        $this->load->view('templates/page_footer');
    }

    public function info()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Info";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/info');
        $this->load->view('templates/page_footer', $data);
    }

    public function pengajuan_spm()
    {
        $this->load->library('JamKerjaLogic_libra');

        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Versi Barada-E";
        $data['tahunIdent'] = date("Y");

        $data['status_kerja'] = $this->jamkerjalogic_libra->getStatusMessage();

        $indicator = $this->jamkerjalogic_libra->isJamKerja() ? 'buka' : 'kunci';
        $scriptMap = [
            'kunci' => 'assets/js/tombolKabur.js',
            'buka'  => 'assets/js/tombolKaburNormal.js',
        ];

        $data['iniKunciTombolKabur'] = $scriptMap[$indicator] ?? null;


        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/pengajuan_spm', $data);
        $this->load->view('templates/page_footer');
    }

    public function tampilkanDataAjuByYear()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Versi Barada-E";
        $selectedYear = $this->input->post('tahun', TRUE);
        $data['tahunIdent'] = $selectedYear;


        if ($selectedYear == date("Y")) {
            redirect('user/pengajuan_spm');
        } else {
            $this->load->view('templates/page_header', $data);
            $this->load->view('templates/menu/sidebar-menu');
            $this->load->view('templates/navbar', $data);
            $this->load->view('templates/pages/pengajuan_spm', $data);
            $this->load->view('templates/page_footer');
        }
    }


    public function view_edit_pengajuan_spm($id_edit_spm)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Versi Barada-E";

        $data['spm_masuk'] = $this->db
            ->select('*, spm_masuk.id AS id_masuk_spm')
            ->from('status_spm')
            ->join('spm_masuk', 'status_spm.id = spm_masuk.id_status')
            ->join('data_user', 'spm_masuk.skpd = data_user.id')
            ->where('spm_masuk.id', (int) $id_edit_spm)
            ->get()
            ->row_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/view_edit_pengajuan_spm', $data);
        $this->load->view('templates/page_footer');
    }

    public function contact()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Kontak";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/contact', $data);
        $this->load->view('templates/page_footer');
    }
}

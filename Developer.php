<?php

use SebastianBergmann\Type\TrueType;

defined('BASEPATH') or exit('No direct script access allowed');

class Developer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        if ($this->session->userdata('id_role') != 4) {
            redirect('auth/');
            // $this->load->view('error403');
        }

        // using helper instead
        // is_logged_in();
    }
    public function index()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['title'] = "Dashboard";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu', $data);
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/dashboard');
        $this->load->view('templates/page_footer');
    }

    public function admin_sewa()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $this->db->select('COUNT(event_acara.id) AS series, event_acara.id_aset, aset_sewa.nm_aset, aset_sewa.color');
        $this->db->from('event_acara');
        $this->db->join('aset_sewa', 'event_acara.id_aset=aset_sewa.id_aset');
        $this->db->group_by('event_acara.id_aset, aset_sewa.nm_aset, aset_sewa.color');
        $this->db->order_by('id_aset', 'ASC');
        $data['acara'] = $this->db->get()->result();

        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/admin_sewa', $data);
        $this->load->view('templates/page_footer', $data);
    }

    public function view_details_verif_sewa($id)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";

        $sewaQuery = "SELECT * FROM status_sewa JOIN event_acara
                            ON status_sewa.id_status=event_acara.id_status JOIN aset_sewa
                            ON event_acara.id_aset=aset_sewa.id_aset
                            WHERE id=$id";
        $data['sewa_masuk'] = $this->db->query($sewaQuery)->row_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/view_details_verif_sewa', $data);
        $this->load->view('templates/page_footer');
    }

    public function edit_sewa($id)
    {
        $nama = htmlspecialchars($this->input->post('nama', TRUE));
        $telepon = htmlspecialchars($this->input->post('telepon', TRUE));
        $alamat = htmlspecialchars($this->input->post('alamat', TRUE));
        $tgl_awal_acara = htmlspecialchars($this->input->post('tanggal-awal', TRUE)) . ' ' . htmlspecialchars($this->input->post('waktu-awal', TRUE));
        $tgl_akhir_acara = htmlspecialchars($this->input->post('tanggal-akhir', TRUE))  . ' ' . htmlspecialchars($this->input->post('waktu-akhir', TRUE));
        $keperluan = htmlspecialchars($this->input->post('acara', TRUE));
        $catatan_acara = htmlspecialchars($this->input->post('catatan_acara', TRUE));

        // cek jika ada gambar diupload
        $bukti_pengenal = $_FILES['buktiIDupdate']['name'];
        // var_dump($bukti_pengenal);
        // die();

        if ($bukti_pengenal) {
            $config['allowed_types'] = 'jpeg|jpg|png|pdf';
            $config['max_size'] = '1024000';
            $config['upload_path'] = './assets/doc/LASADA';

            $this->load->library('upload', $config);
            $this->upload->initialize($config); //mengatasi error upload_path

            if ($this->upload->do_upload('buktiIDupdate')) {
                // hapus file dokumen lama
                $uploadDirUpdate = './assets/doc/LASADA/';
                $hasilrecordUpdate = $this->db->get_where('event_acara', ['id' => $id])->row_array();
                $filenameLama = $hasilrecordUpdate['bukti_pengenal'];
                // Delete the temporary uploaded file
                unlink($uploadDirUpdate . $filenameLama);

                $dok = $this->upload->data('file_name');
                $queryUpdate1 = "UPDATE event_acara
                                SET nama='$nama', no_hp='$telepon', alamat='$alamat', keperluan='$keperluan', tgl_awal_acara='$tgl_awal_acara', tgl_akhir_acara='$tgl_akhir_acara', bukti_pengenal='$dok', catatan_acara='$catatan_acara'
                                WHERE id=$id
                                ";
                $this->db->query($queryUpdate1);
                $swal1 = '<script>
                            window.addEventListener("load", function() {
                                Toastify({
                                    text: "Sewa Berhasil Diedit",
                                    duration: 3000,
                                    close: true,
                                    gravity: "center",
                                    position: "center",
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                }).showToast();
                            });
                        </script>';
                $this->session->set_flashdata('message', $swal1);
                redirect('developer/view_details_verif_sewa/' . $id);
            } else {
                $pesanError = $this->upload->display_errors();
                $swalerror = '<script>
                                    window.addEventListener("load", function() {
                                        Toastify({
                                            text: "' . $pesanError . '",
                                            duration: 3000,
                                            close: true,
                                            gravity: "center",
                                            position: "center",
                                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                        }).showToast();
                                    });
                                </script>';
                $this->session->set_flashdata('message', $swalerror);
                redirect('developer/view_details_verif_sewa/' . $id);
            }
        } else {
            $queryUpdate = "UPDATE event_acara
                                    SET nama='$nama', no_hp='$telepon', alamat='$alamat', keperluan='$keperluan', tgl_awal_acara='$tgl_awal_acara', tgl_akhir_acara='$tgl_akhir_acara', catatan_acara='$catatan_acara'
                                    WHERE id=$id
                                    ";
            $this->db->query($queryUpdate);
            $swal = '<script>
                            window.addEventListener("load", function() {
                                Toastify({
                                    text: "Sewa Berhasil Diedit",
                                    duration: 3000,
                                    close: true,
                                    gravity: "center",
                                    position: "center",
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                }).showToast();
                            });
                        </script>';
            $this->session->set_flashdata('message', $swal);
            redirect('developer/view_details_verif_sewa/' . $id);
        }
    }

    public function admin_item_aset($id_aset)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $querySewaAset = "SELECT * FROM aset_sewa JOIN event_acara ON aset_sewa.id_aset=event_acara.id_aset JOIN status_sewa
                            ON event_acara.id_status=status_sewa.id_status
                            WHERE event_acara.id_aset = $id_aset AND event_acara.id_status=?
                            ORDER BY ? ASC
                        ";
        $data['sewa'] = $this->db->query($querySewaAset, array(1, 'tgl_book'))->result_array();
        $data['sewaPesan'] = $this->db->query($querySewaAset, array(3, 'tgl_awal_acara'))->result_array();
        $data['JumlahSewa'] = $this->db->query($querySewaAset, array(1, 'tgl_book'))->num_rows();
        $data['JumlahPesan'] = $this->db->query($querySewaAset, array(3, 'tgl_awal_acara'))->num_rows();

        $queryAcara = "SELECT COUNT(event_acara.id) AS series, nm_aset, color
                        FROM event_acara JOIN aset_sewa ON event_acara.id_aset=aset_sewa.id_aset
                        GROUP BY aset_sewa.id_aset,aset_sewa.nm_aset, aset_sewa.color
                        ORDER BY aset_sewa.id_aset ASC";
        $data['acara'] = $this->db->query($queryAcara)->result();
        $data['id_aset'] = $id_aset;

        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/admin_item_aset', $data);
        $this->load->view('templates/page_footer', $data);
    }

    public function verif_lasada($id_lasada)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $queryVerifLasada = "UPDATE event_acara 
                        SET id_status=3
                        WHERE id=$id_lasada
                        ";
        $this->db->query($queryVerifLasada);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Sewa Berhasil Disetujui",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        // cek aset mana yang aktif
        $aset_active = $this->db->get_where('event_acara', ['id' => $id_lasada])->row_array();
        redirect('developer/admin_item_aset/' . $aset_active['id_aset']);
    }

    public function hapus_lasada($id_lasada)
    {
        $id_role = $this->session->userdata('id_role');
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $uploadDir = './assets/doc/LASADA/';
        $hasilrecord = $this->db->get_where('event_acara', ['id' => $id_lasada])->row_array();
        $filename = $hasilrecord['bukti_pengenal'];
        // Delete the temporary uploaded file
        unlink($uploadDir . $filename);

        $queryHapus = "DELETE FROM event_acara 
                        WHERE id=$id_lasada
                        ";
        $this->db->query($queryHapus);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Sewa Berhasil Dihapus",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/admin_sewa');
    }

    public function verifikasi_spm()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $tahun_now = date("Y");
        $data['tahunIdent'] = date("Y");

        $queryJumlahSPMmasuk = "SELECT * FROM spm_masuk WHERE id_status=? AND tgl_aju LIKE '$tahun_now%'";

        $data['proses'] = $this->db->query($queryJumlahSPMmasuk, array(1))->num_rows();
        $data['tolak'] = $this->db->query($queryJumlahSPMmasuk, array(2))->num_rows();
        $data['verified'] = $this->db->query($queryJumlahSPMmasuk, array(3))->num_rows();

        $spmQuery = "SELECT spm_masuk.id AS id_masuk_spm, spm_masuk.tgl_verif, spm_masuk.reg, data_user.name, spm_masuk.no_spm, spm_masuk.jenis,spm_masuk.dokumen, spm_masuk.verifikator FROM status_spm JOIN spm_masuk 
                                                                ON status_spm.id = spm_masuk.id_status JOIN data_user ON spm_masuk.skpd=data_user.id
                                                                WHERE spm_masuk.id_status=3 AND spm_masuk.tgl_aju LIKE '$tahun_now%'
                                                                ORDER BY spm_masuk.reg DESC
                                                                ";
        $data['spm_masuk'] = $this->db->query($spmQuery)->result_array();

        $data['title'] = "Admin";
        $data['has_sub'] = "SPM";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/verifikasi_spm', $data);
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

    public function pengajuan_spm()
    {
        $this->load->model('JamKerja_model');

        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Versi Barada-E";
        $data['tahunIdent'] = date("Y");

        $data['status_kerja'] = "";
        $data['iniKunciTombolKabur'] = 'assets/js/tombolKaburNormal.js';

        $data['dataJamKerja'] = $this->JamKerja_model->getDataJamKerja();
        $data['dataHariLibur'] = $this->JamKerja_model->getHariLibur();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/pengajuan_spm', $data);
        $this->load->view('templates/page_footer');
    }

    public function editJamKerja()
    {
        $this->load->model('JamKerja_model');

        $id = $this->input->post('id_jk');
        $jam_mulai = $this->input->post('jam_mulai');
        $jam_selesai = $this->input->post('jam_selesai');
        $status = $this->input->post('aktif_jk');

        $this->JamKerja_model->updateJamKerja($id, $jam_mulai, $jam_selesai, $status);

        redirect('developer/pengajuan_spm');
    }

    public function addLibnas()
    {
        $this->load->model('JamKerja_model');

        $tgl_libnas = $this->input->post('tanggal_libnas');
        $ket_libnas = $this->input->post('ket_libnas');

        $this->JamKerja_model->createHarLib($tgl_libnas, $ket_libnas);

        redirect('developer/pengajuan_spm');
    }

    public function editLibnas()
    {
        $this->load->model('JamKerja_model');

        $id_libnas = $this->input->post('id_libnas');
        $tgl_libnas = $this->input->post('tanggal_libnas_view');
        $ket_libnas = $this->input->post('ket_libnas_view');

        $this->JamKerja_model->updateHarLib($id_libnas, $tgl_libnas, $ket_libnas);

        redirect('developer/pengajuan_spm');
    }

    public function deleteLibnas($id)
    {
        $this->load->model('JamKerja_model');
        $this->JamKerja_model->deleteHarLib($id);

        redirect('developer/pengajuan_spm');
    }

    public function tampilkanDataAjuByYear()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Versi Barada-E";
        $selectedYear = $this->input->post('tahun', TRUE);
        $data['tahunIdent'] = $selectedYear;


        if ($selectedYear == date("Y")) {
            redirect('developer/pengajuan_spm');
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

        $spmQuery = "SELECT *, spm_masuk.id AS id_masuk_spm FROM status_spm JOIN spm_masuk 
                                                            ON status_spm.id = spm_masuk.id_status JOIN data_user
                                                            ON spm_masuk.skpd=data_user.id
                                                            WHERE spm_masuk.id='$id_edit_spm'
                                                            ";
        $data['spm_masuk'] = $this->db->query($spmQuery)->row_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/view_edit_pengajuan_spm', $data);
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

    public function menu()
    {
        $data['title'] = "Manage";
        $data['has_sub'] = "Menu";
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $data['menu'] = $this->db->get('menu')->result_array();
        $this->form_validation->set_rules('menu', 'Menu', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/page_header', $data);
            $this->load->view('templates/menu/sidebar-menu', $data);
            $this->load->view('templates/navbar', $data);
            $this->load->view('templates/pages/menu');
            $this->load->view('templates/page_footer');
        } else {
            $this->db->insert('menu', ['menu' => $this->input->post('menu', TRUE)]);

            $swal = '<script>
                        window.addEventListener("load", function() {
                            Toastify({
                                text: "Menu Berhasil Ditambahkan",
                                duration: 3000,
                                close: true,
                                gravity: "center",
                                position: "center",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                        });
                    </script>';
            $this->session->set_flashdata('message', $swal);
            redirect('developer/menu');
        }
    }

    public function add_sub_menu()
    {
        $aksesMenu = htmlspecialchars($this->input->post('id_menu', TRUE));
        $namaSubMenu = htmlspecialchars($this->input->post('title', TRUE));
        $url = htmlspecialchars($this->input->post('url', TRUE));
        $icon = htmlspecialchars($this->input->post('icon', TRUE));
        $is_active = htmlspecialchars($this->input->post('is_active', TRUE));
        $data = [
            'id_menu' => $aksesMenu,
            'title' => $namaSubMenu,
            'url' => $url,
            'icon' => $icon,
            'is_active' => $is_active
        ];
        $this->db->insert('sub_menu', $data);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Sub Menu Baru Berhasil Ditambahkan",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/menu');
    }

    public function edit_menu($idMenu)
    {
        $NamaMenu = htmlspecialchars($this->input->post('menu', TRUE));
        $queryEditMenu = "UPDATE menu
                            SET menu='$NamaMenu'
                            WHERE id=$idMenu
                            ";
        $this->db->query($queryEditMenu);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Menu Berhasil Diedit",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/menu');
    }
    public function edit_sub_menu($idSubMenu)
    {
        $aksesMenu = htmlspecialchars($this->input->post('id_menu', TRUE));
        $namaSubMenu = htmlspecialchars($this->input->post('title', TRUE));
        $url = htmlspecialchars($this->input->post('url', TRUE));
        $icon = htmlspecialchars($this->input->post('icon', TRUE));
        $is_active = htmlspecialchars($this->input->post('is_active', TRUE));
        $queryEditMenu = "UPDATE sub_menu
                            SET id_menu='$aksesMenu', title='$namaSubMenu', url='$url',icon='$icon',is_active='$is_active'
                            WHERE id=$idSubMenu
                            ";
        $this->db->query($queryEditMenu);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Sub Menu Berhasil Diedit",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/menu');
    }

    public function delete($id)
    {
        // $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        // $verifikator = $user['name'];
        $queryDelete = "DELETE FROM menu
                        WHERE id=$id
                        ";
        $this->db->query($queryDelete);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Menu Berhasil Dihapus",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/menu');
    }
    public function delete_sub_menu($idSub)
    {
        $queryDelete = "DELETE FROM sub_menu
                        WHERE id=$idSub
                        ";
        $this->db->query($queryDelete);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Sub Menu Berhasil Dihapus",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/menu');
    }

    public function user_manage()
    {
        $data['title'] = "Manage";
        $data['has_sub'] = "User";
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $userMasuk = $user['id'];

        $data['userQuery'] = "SELECT *, data_user.id AS id_user FROM data_user JOIN user_role
                    ON data_user.id_role=user_role.id
                    WHERE data_user.id!=$userMasuk AND data_user.id_role!=? AND user_role.role LIKE ?
                    ";

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/user_manage');
        $this->load->view('templates/page_footer');
    }

    public function edit_user($id)
    {
        // $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        // $verifikator = $user['name'];

        $akronim = htmlspecialchars($this->input->post('akronim', TRUE));
        $role = htmlspecialchars($this->input->post('user_role', TRUE));
        $status = htmlspecialchars($this->input->post('status', TRUE));
        $link_bi = htmlspecialchars($this->input->post('link-bi', TRUE));
        $link_stock = htmlspecialchars($this->input->post('link-stock', TRUE));
        $queryDelete = "UPDATE data_user
                            SET id_role=$role, is_active=$status, akronim='$akronim', link_bi='$link_bi', link_stock='$link_stock'
                            WHERE id=$id
                        ";
        $this->db->query($queryDelete);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "User Berhasil Di-Update",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/user_manage');
    }

    public function add_user()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('akronim', 'Acronim', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[user.username]', [
            'is_unique' => 'Username ini sudah tersedia. Buat username lain!'
        ]);
        $this->form_validation->set_rules('user_role', 'User Role', 'required');
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]', [
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]);

        $data = [
            'name' => htmlspecialchars($this->input->post('name', true)),
            'username' => htmlspecialchars($this->input->post('username', true)),
            'image' => 'default.png',
            'password' => password_hash($this->input->post('password1', true), PASSWORD_DEFAULT),
            'id_role' => htmlspecialchars($this->input->post('user_role', true)),
            'is_active' => 1,
            'akronim' => htmlspecialchars($this->input->post('akronim', true)),
            'link_bi' => htmlspecialchars($this->input->post('link-bi', true)),
            'link_stock' => htmlspecialchars($this->input->post('link-stock', true)),
        ];
        $this->db->insert('data_user', $data);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "User Baru Berhasil Ditambahkan",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/user_manage');
    }

    public function delete_user()
    {
        $id_user = htmlspecialchars($this->input->post('id_user', true));
        $queryDelete = "DELETE FROM data_user
                        WHERE id=$id_user
                        ";
        $this->db->query($queryDelete);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "User Berhasil Dihapus",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/user_manage');
    }

    public function ubah_password()
    {
        $this->form_validation->set_rules('ubahpassword1', 'Password', 'required|trim|is_unique[user.password]|min_length[3]|matches[password2]', [
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]);
        $this->form_validation->set_rules('ubahpassword2', 'Password', 'required|trim|matches[password1]', [
            'matches' => 'Password tidak sama!',
            'min_length' => 'Password terlalu pendek!'
        ]);
        $pass1 = password_hash($this->input->post('ubahpassword1', true), PASSWORD_DEFAULT);
        $idUbah = htmlspecialchars($this->input->post('id', true));
        $queryUbahPwd = "UPDATE data_user
                            SET password='$pass1'
                            WHERE id=$idUbah
                        ";
        $this->db->query($queryUbahPwd);
        $swal = '<script>
                    window.addEventListener("load", function() {
                        Toastify({
                            text: "Password Berhasil Di-Update",
                            duration: 3000,
                            close: true,
                            gravity: "center",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        }).showToast();
                    });
                </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('developer/user_manage');
    }
}

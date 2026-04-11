<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        if ($this->session->userdata('id_role') != 1) {
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

        $data['sewa_masuk'] = $this->db
            ->select('status_sewa.*, event_acara.*, aset_sewa.*')
            ->from('status_sewa')
            ->join('event_acara', 'status_sewa.id_status = event_acara.id_status')
            ->join('aset_sewa', 'event_acara.id_aset = aset_sewa.id_aset')
            ->where('event_acara.id', (int)$id)
            ->get()
            ->row_array();

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
            $config = [
                'upload_path'   => FCPATH . 'assets/doc/LASADA/',
                'allowed_types' => 'jpg|jpeg|png|pdf',
                'max_size'      => 1024000,
                'encrypt_name'  => TRUE,
                // 'file_name' => $new_name,
            ];

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
                $data_sewa = [
                    'nama'             => $nama,
                    'no_hp'            => $telepon,
                    'alamat'           => $alamat,
                    'keperluan'        => $keperluan,
                    'tgl_awal_acara'   => $tgl_awal_acara,
                    'tgl_akhir_acara'  => $tgl_akhir_acara,
                    'bukti_pengenal'   => $dok,
                    'catatan_acara'    => $catatan_acara
                ];

                $this->db->where('id', (int) $id);
                $this->db->update('event_acara', $data_sewa);
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
                redirect('admin/view_details_verif_sewa/' . $id);
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
                redirect('admin/view_details_verif_sewa/' . $id);
            }
        } else {
            $data = [
                'nama'            => $nama,
                'no_hp'           => $telepon,
                'alamat'          => $alamat,
                'keperluan'       => $keperluan,
                'tgl_awal_acara'  => $tgl_awal_acara,
                'tgl_akhir_acara' => $tgl_akhir_acara,
                'catatan_acara'   => $catatan_acara
            ];

            $this->db->where('id', (int)$id);
            $this->db->update('event_acara', $data);

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
            redirect('admin/view_details_verif_sewa/' . $id);
        }
    }

    public function edit_sewa_cadangan($id)
    {
        $nama = htmlspecialchars($this->input->post('nama', TRUE));
        $tgl_awal_acara = htmlspecialchars($this->input->post('tanggal-awal', TRUE)) . ' ' . htmlspecialchars($this->input->post('waktu-awal', TRUE));
        $tgl_akhir_acara = htmlspecialchars($this->input->post('tanggal-akhir', TRUE))  . ' ' . htmlspecialchars($this->input->post('waktu-akhir', TRUE));
        $keperluan = htmlspecialchars($this->input->post('acara', TRUE));


        $data = [
            'nama'            => $nama,
            'keperluan'       => $keperluan,
            'tgl_awal_acara'  => $tgl_awal_acara,
            'tgl_akhir_acara' => $tgl_akhir_acara
        ];

        $this->db->where('id', (int)$id);
        $this->db->update('event_acara', $data);
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
        redirect('admin/view_details_verif_sewa/' . $id);
    }

    private function getSewaAset($id_aset, $id_status, $order_by)
    {
        return $this->db
            ->select('*')
            ->from('aset_sewa')
            ->join('event_acara', 'aset_sewa.id_aset = event_acara.id_aset')
            ->join('status_sewa', 'event_acara.id_status = status_sewa.id_status')
            ->where('event_acara.id_aset', (int) $id_aset)
            ->where('event_acara.id_status', (int) $id_status)
            ->order_by($order_by, 'ASC');
    }

    public function admin_item_aset($id_aset)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        // $querySewaAset = "SELECT * FROM aset_sewa JOIN event_acara ON aset_sewa.id_aset=event_acara.id_aset JOIN status_sewa
        //                         ON event_acara.id_status=status_sewa.id_status
        //                         WHERE event_acara.id_aset = $id_aset AND event_acara.id_status=?
        //                         ORDER BY ? ASC
        //                     ";
        // $data['sewa'] = $this->db->query($querySewaAset, array(1, 'tgl_book'))->result_array();
        // $data['sewaPesan'] = $this->db->query($querySewaAset, array(3, 'tgl_awal_acara'))->result_array();
        // $data['JumlahSewa'] = $this->db->query($querySewaAset, array(1, 'tgl_book'))->num_rows();
        // $data['JumlahPesan'] = $this->db->query($querySewaAset, array(3, 'tgl_awal_acara'))->num_rows();
        $data['sewa'] = $this->getSewaAset($id_aset, 1, 'tgl_book')
            ->get()
            ->result_array();

        $data['sewaPesan'] = $this->getSewaAset($id_aset, 3, 'tgl_awal_acara')
            ->get()
            ->result_array();

        $data['JumlahSewa'] = $this->getSewaAset($id_aset, 1, 'tgl_book')
            ->count_all_results();

        $data['JumlahPesan'] = $this->getSewaAset($id_aset, 3, 'tgl_awal_acara')
            ->count_all_results();


        $this->db->select('COUNT(event_acara.id) AS series, aset_sewa.nm_aset, aset_sewa.color');
        $this->db->from('event_acara');
        $this->db->join('aset_sewa', 'event_acara.id_aset = aset_sewa.id_aset');
        $this->db->group_by([
            'aset_sewa.id_aset',
            'aset_sewa.nm_aset',
            'aset_sewa.color'
        ]);
        $this->db->order_by('aset_sewa.id_aset', 'ASC');

        $data['acara'] = $this->db->get()->result();

        $data['id_aset'] = $id_aset;

        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/lasada/admin_item_aset', $data);
        $this->load->view('templates/page_footer', $data);
    }

    public function verifikasi_spm()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $tahun_now = date("Y");
        $data['tahunIdent'] = date("Y");

        $status = [
            'proses'   => 1,
            'tolak'    => 2,
            'verified' => 3
        ];

        foreach ($status as $key => $val) {
            $data[$key] = $this->db
                ->from('spm_masuk')
                ->where('id_status', $val)
                ->like('tgl_aju', $tahun_now, 'after')
                ->count_all_results();
        }

        $this->db
            ->select('
                spm_masuk.id AS id_masuk_spm,
                spm_masuk.tgl_verif,
                spm_masuk.reg,
                data_user.name,
                spm_masuk.no_spm,
                spm_masuk.jenis,
                spm_masuk.dokumen,
                spm_masuk.total_spm,
                spm_masuk.verifikator
            ')
            ->from('status_spm')
            ->join('spm_masuk', 'status_spm.id = spm_masuk.id_status')
            ->join('data_user', 'spm_masuk.skpd = data_user.id')
            ->where('spm_masuk.id_status', 3)
            ->like('spm_masuk.tgl_aju', $tahun_now, 'after')
            ->order_by('spm_masuk.reg', 'DESC');

        $data['spm_masuk'] = $this->db->get()->result_array();


        $data['title'] = "Admin";
        $data['has_sub'] = "SPM";
        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/verifikasi_spm', $data);
        $this->load->view('templates/page_footer');
    }

    // tampilan sesuai tahun Pilihan
    public function tampilkanDataByYear()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $selectedYear = $this->input->post('tahun', TRUE);
        $tahun_pilih = $selectedYear;
        $data['tahunIdent'] = $selectedYear;

        // $queryJumlahSPMmasuk = "SELECT * FROM spm_masuk WHERE id_status=? AND tgl_aju LIKE '$tahun_pilih%'";

        // $data['proses'] = $this->db->query($queryJumlahSPMmasuk, array(1))->num_rows();
        // $data['tolak'] = $this->db->query($queryJumlahSPMmasuk, array(2))->num_rows();
        // $data['verified'] = $this->db->query($queryJumlahSPMmasuk, array(3))->num_rows();

        $status = [
            'proses'   => 1,
            'tolak'    => 2,
            'verified' => 3
        ];

        foreach ($status as $key => $val) {
            $data[$key] = $this->db
                ->from('spm_masuk')
                ->where('id_status', $val)
                ->like('tgl_aju', $tahun_pilih, 'after')
                ->count_all_results();
        }

        $this->db
            ->select('
                spm_masuk.id AS id_masuk_spm,
                spm_masuk.tgl_verif,
                spm_masuk.reg,
                data_user.name,
                spm_masuk.no_spm,
                spm_masuk.jenis,
                spm_masuk.dokumen,
                spm_masuk.verifikator
            ')
            ->from('status_spm')
            ->join('spm_masuk', 'status_spm.id = spm_masuk.id_status')
            ->join('data_user', 'spm_masuk.skpd = data_user.id')
            ->where('spm_masuk.id_status', 3)
            ->like('spm_masuk.tgl_verif', $tahun_pilih, 'after')
            ->order_by('spm_masuk.reg', 'DESC');

        $result = $this->db->get()->result_array();

        if ($tahun_pilih == date("Y")) {
            redirect('admin/verifikasi_spm');
        } else {
            $data['spm_masuk'] = $result;
            $data['title'] = "Admin";
            $data['has_sub'] = "SPM";
            $this->load->view('templates/page_header', $data);
            $this->load->view('templates/menu/sidebar-menu');
            $this->load->view('templates/navbar', $data);
            $this->load->view('templates/pages/verifikasi_spm', $data);
            $this->load->view('templates/page_footer');
        }
    }
    // tampilan sesuai tahun Pilihan

    public function koreksi_spm($id)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        // mengambil nilai satu record untuk dicek
        $cekSPM = $this->db->get_where('spm_masuk', ['id' => $id])->row_array();
        // Cek apakah SPM sudah diverifikasi oleh admin lain sebelumnya
        if ($cekSPM['id_status'] == 3) {
            $verifikatorCek = $cekSPM['verifikator'];
            $swalSudah = '<script>
                        window.addEventListener("load", function() {
                            Swal.fire({
                                title: "Success!",
                                text: "SPM ini sudah diverifikasi oleh ' . $verifikatorCek . '",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500,
                            })
                        });
                    </script>';
            $this->session->set_flashdata('message', $swalSudah);
            redirect('admin/verifikasi_spm');
        } else {
            if ($cekSPM['verifikator'] != "") {
                if ($cekSPM['verifikator'] == $user['name']) {
                    $swal = '<script>
                                window.addEventListener("load", function() {
                                    Toastify({
                                        text: "Anda sedang di halaman periksa",
                                        duration: 3000,
                                        close: true,
                                        gravity: "center",
                                        position: "center",
                                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    }).showToast();
                                });
                            </script>';
                    $this->session->set_flashdata('message', $swal);
                    redirect('admin/view_details_verif_spm/' . $id);
                } else {
                    $verikatorSedangPeriksa = $cekSPM['verifikator'];
                    $swalSudah3 = '<script>
                                    window.addEventListener("load", function() {
                                        Swal.fire({
                                            title: "Success!",
                                            text: "SPM ini sedang diperiksa oleh ' . $verikatorSedangPeriksa . '",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500,
                                        })
                                    });
                                </script>';
                    $this->session->set_flashdata('message', $swalSudah3);
                    redirect('admin/verifikasi_spm');
                }
            } else {
                $verifikator = $user['name'];
                $this->db
                    ->set('verifikator', $verifikator)
                    ->where('id', (int)$id)
                    ->update('spm_masuk');
                $swal = '<script>
                                window.addEventListener("load", function() {
                                    Toastify({
                                        text: "Anda sedang di halaman periksa",
                                        duration: 3000,
                                        close: true,
                                        gravity: "center",
                                        position: "center",
                                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    }).showToast();
                                });
                            </script>';
                $this->session->set_flashdata('message', $swal);
                redirect('admin/view_details_verif_spm/' . $id);
            }
            // #############
        }
    }

    public function cancel_koreksi($idCancelSPM)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $this->db
            ->set('verifikator', '')
            ->where('id', (int)$idCancelSPM)
            ->update('spm_masuk');
        $swal = '<script>
                        window.addEventListener("load", function() {
                            Toastify({
                                text: "SPM Batal Diperiksa",
                                duration: 3000,
                                close: true,
                                gravity: "center",
                                position: "center",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                        });
                    </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('admin/verifikasi_spm');
    }

    public function verif($id)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $year_verif = date('Y');

        $this->db
            ->select_max('reg')
            ->from('spm_masuk')
            ->like('tgl_verif', $year_verif, 'after') // tgl_verif LIKE '2025%'
            ->where('id_status', 3);
        // Mengecek apakah ada verifikasi di tahun ini lalu ditambah 1 untuk dijadikan no. register
        $row = $this->db->get()->row_array();
        // var_dump($no_reg["MAX(reg)"]);
        // die;
        $no_reg_rslt = empty($row['reg']) ? 1 : ((int)$row['reg'] + 1);

        // mengambil nilai satu record untuk dicek
        $cekSPM = $this->db->get_where('spm_masuk', ['id' => $id])->row_array();
        // Cek apakah SPM sudah diverifikasi oleh admin lain sebelumnya
        if ($cekSPM['id_status'] == 3) {
            $verifikatorCek = $cekSPM['verifikator'];
            $swalSudah = '<script>
                        window.addEventListener("load", function() {
                            Swal.fire({
                                title: "Success!",
                                text: "SPM ini sudah diverifikasi oleh ' . $verifikatorCek . '",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500,
                            })
                        });
                    </script>';
            $this->session->set_flashdata('message', $swalSudah);
            redirect('admin/verifikasi_spm');
        } else {
            $verifikator = $user['name'];
            $tgl_verif = date('Y-m-d');
            $data_verif = [
                'reg'         => $no_reg_rslt,
                'tgl_verif'   => $tgl_verif,
                'verifikator' => $verifikator,
                'id_status'   => 3
            ];
            $this->db->where('id', (int)$id);
            $this->db->update('spm_masuk', $data_verif);
            $swal = '<script>
                            window.addEventListener("load", function() {
                                Toastify({
                                text: "SPM Berhasil Diverifikasi",
                                duration: 3000,
                                close: true,
                                gravity: "center",
                                position: "center",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                            });
                        </script>';
            $this->session->set_flashdata('message', $swal);
            redirect('admin/verifikasi_spm');
        }
    }

    public function tolak($id)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $verifikator = $user['name'];
        $note = htmlspecialchars($this->input->post('catatan', TRUE));

        // mengambil nilai satu record untuk dicek
        $cekSPMtolak = $this->db->get_where('spm_masuk', ['id' => $id])->row_array();
        // Cek apakah SPM sudah diperiksa oleh admin lain sebelumnya
        if ($cekSPMtolak['id_status'] == 2) {
            $swalSudah = '<script>
                                window.addEventListener("load", function() {
                                    Swal.fire({
                                        title: "Success!",
                                        text: "SPM ini sudah diperiksa sebelumnya",
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 1500,
                                    })
                                });
                            </script>';
            $this->session->set_flashdata('message', $swalSudah);
            redirect('admin/verifikasi_spm');
        } else {
            $this->db->where('id', (int)$id);
            $this->db->update('spm_masuk', [
                'verifikator' => '',
                'id_status'   => 2,
                'catatan'     => $note . ' <br> Diperiksa oleh: ' . $verifikator
            ]);
            $swal = '<script>
                            window.addEventListener("load", function() {
                                Toastify({
                                text: "SPM Berhasil Ditolak",
                                duration: 3000,
                                close: true,
                                gravity: "center",
                                position: "center",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                            });
                        </script>';
            $this->session->set_flashdata('message', $swal);
            redirect('admin/verifikasi_spm');
        }
    }
    public function edit_spm($id)
    {
        $noSPM = htmlspecialchars($this->input->post('no_spm', TRUE));
        $jenis = htmlspecialchars($this->input->post('jenis', TRUE));
        $total_spm_val = htmlspecialchars($this->input->post('total-spm', TRUE));
        $total_spm = str_replace(',', '.', str_replace('.', '', $total_spm_val));

        $this->db->where('id', $id);
        $this->db->update('spm_masuk', [
            'no_spm'    => $noSPM,
            'jenis'     => $jenis,
            'total_spm' => $total_spm
        ]);

        $swal = '<script>
                        window.addEventListener("load", function() {
                            Toastify({
                                text: "SPM Berhasil Diedit",
                                duration: 3000,
                                close: true,
                                gravity: "center",
                                position: "center",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                        });
                    </script>';
        $this->session->set_flashdata('message', $swal);
        redirect('admin/view_details_verif_spm/' . $id);
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


    public function verif_lasada($id_lasada)
    {
        $user = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();

        $this->db
            ->where('id', (int) $id_lasada)
            ->update('event_acara', [
                'id_status' => 3
            ]);
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
        redirect('admin/admin_item_aset/' . $aset_active['id_aset']);
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

        $this->db->delete('event_acara', ['id' => (int)$id_lasada]);

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
        redirect('admin/admin_sewa');
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
            redirect('admin/pengajuan_spm');
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
            ->select('spm_masuk.id AS id_masuk_spm,
                spm_masuk.*,
                status_spm.status,
                data_user.name')
            ->from('status_spm')
            ->join('spm_masuk', 'status_spm.id = spm_masuk.id_status')
            ->join('data_user', 'spm_masuk.skpd = data_user.id')
            ->where('spm_masuk.id', $id_edit_spm)
            ->get()
            ->row_array();


        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/view_edit_pengajuan_spm', $data);
        $this->load->view('templates/page_footer');
    }

    public function view_details_verif_spm($id)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Admin";
        $data['has_sub'] = "SPM";

        $data['spm_masuk'] = $this->db
            ->select('*, spm_masuk.id AS id_masuk_spm')
            ->from('status_spm')
            ->join('spm_masuk', 'status_spm.id = spm_masuk.id_status', 'inner')
            ->join('data_user', 'spm_masuk.skpd = data_user.id', 'inner')
            ->where('spm_masuk.id', $id)
            ->get()
            ->row_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/view_details_verif_spm', $data);
        $this->load->view('templates/page_footer');
    }


    public function bukti_bayar()
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";

        $data['daftarBayar'] = $this->db
            ->from('bukti_bayar')
            ->order_by('id_bb', 'DESC')
            ->get()
            ->result_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/bayar_setoran', $data);
        $this->load->view('templates/page_footer');
    }

    public function getBayarById($id)
    {
        $data['user'] = $this->db->get_where('data_user', ['username' => $this->session->userdata('username')])->row_array();
        $data['title'] = "Admin";
        $data['has_sub'] = "Admin Sewa";
        // mengambil data untuk diedit, HANYA MENGAMBIL DATA
        $data['daftarBayar'] = $this->db->get_where('bukti_bayar', ['id_bb' => $id])->row_array();

        $this->load->view('templates/page_header', $data);
        $this->load->view('templates/menu/sidebar-menu');
        $this->load->view('templates/navbar', $data);
        $this->load->view('templates/pages/view_edit_bayar_setoran', $data);
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
}

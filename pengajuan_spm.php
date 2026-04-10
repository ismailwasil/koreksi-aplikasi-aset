<div class="page-heading">
    <div class="page-title">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <img src="<?= base_url('assets/'); ?>images/logo/versi_barada_e2.png" alt="Versi Barada-E"
                class="responsive" height="60"><br>
        </div>
        <div class="row">
            <br>
        </div>
    </div>

    <!-- 2//////////// -->
    <form id="formTahunLive" method="post" action="<?= base_url('admin/tampilkanDataByYearLive'); ?>">
        <div class="row justify-content-center">
            <div class="col-sm-3 mb-1">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-info text-white" for="tahunLive">Pilih Tahun</span>
                    <select class="form-select" name="tahunLive" id="tahunLive">
                        <!-- Opsi tahun dapat diisi dari rentang tahun yang diinginkan -->
                        <?php for ($i = date("Y"); $i >= date("Y") - 2; $i--) : ?>
                            <option class="text-center"
                                style="color:<?= $i == date('Y') - 2 ? 'red;' : ($i == date('Y') - 1 ? 'blue;' : 'green') ?>"
                                value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <span class="input-group-text"
                        style="border: none; background-color: transparent; font-weight: bolder;"><i
                            class="fa fa-fw fa-lg fa-spin fa-spinner d-none" id="loader"></i></span>
                </div>
            </div>
        </div>
    </form>
    <!-- /2/////////// -->
    <div id="dataLive">
        <?php if ($user['id_role'] != 2) : ?>
            <?php
            $spmProses = "SELECT spm_masuk.id FROM spm_masuk JOIN data_user
                                                        ON spm_masuk.skpd=data_user.id
                                                        WHERE spm_masuk.tgl_aju LIKE '$tahunIdent%' AND spm_masuk.id_status=?
                                                        ";
            $jumlahProses = $this->db->query($spmProses, array(1))->num_rows();
            $jumlahTolak = $this->db->query($spmProses, array(2))->num_rows();
            $jumlahVerif = $this->db->query($spmProses, array(3))->num_rows();
            ?>
        <?php elseif ($user['id_role'] = 2) : ?>
            <?php
            if ($user['status_skpd'] != 1) {
                $NameUser = $user['id'];
            } elseif ($user['status_skpd'] == 1) {
                $NameUser = 13;
            }
            $spmProses = "SELECT spm_masuk.id FROM spm_masuk JOIN data_user
                                                        ON spm_masuk.skpd=data_user.id
                                                        WHERE data_user.id='$NameUser' AND spm_masuk.tgl_aju LIKE '$tahunIdent%'
                                                        AND spm_masuk.id_status=?
                                                        ";
            $jumlahProses = $this->db->query($spmProses, array(1))->num_rows();
            $jumlahTolak = $this->db->query($spmProses, array(2))->num_rows();
            $jumlahVerif = $this->db->query($spmProses, array(3))->num_rows();
            ?>
        <?php endif ?>
        <?= form_error('menu', '<div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>', '</div>'); ?>
        <?= $this->session->flashdata('message'); ?>

        <section class="section">
            <!-- Container -->
            <div id="container">
                <?php if ($user['status_skpd'] != NULL): ?>
                    <!-- Nothing -->
                <?php else: ?>
                    <div class="row justify-content-center">
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card-is bg-light-success-ismail" onclick="verified()" style="cursor: pointer;">
                                <div class="card-body px-1 py-1-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon bg-white"
                                                style="box-shadow: inset 5px -5px 2px #e0e0e0, inset -5px 5px 2px #fcfcfc;">
                                                <span>
                                                    <i class="fa fa-fw fa-lg fa-check-square-o"
                                                        style="color: rebeccapurple;"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Verified</h6>
                                            <h4 class="font-extrabold mb-0"><strong
                                                    id="jml-verif-user"><?= $jumlahVerif ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card-is  bg-light-warning-ismail" onclick="proses()" style="cursor: pointer;">
                                <div class="card-body px-1 py-1-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon bg-white"
                                                style="box-shadow: inset 5px -5px 2px #e0e0e0, inset -5px 5px 2px #fcfcfc;">
                                                <!-- <i class="iconly-boldShow"></i> -->
                                                <span>
                                                    <i class="fa fa-fw fa-lg fa-pencil-square-o"
                                                        style="color: rebeccapurple;"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Proses</h6>
                                            <h4 class="font-extrabold mb-0"><strong
                                                    id="jml-proses-user"><?= $jumlahProses ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card-is bg-light-danger" onclick="tolak()" style="cursor: pointer;">
                                <div class="card-body px-1 py-1-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stats-icon bg-white"
                                                style="box-shadow: inset 5px -5px 2px #e0e0e0, inset -5px 5px 2px #fcfcfc;">
                                                <span>
                                                    <i class="fa fa-fw fa-lg fa-ban" style="color: rebeccapurple;"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="text-muted font-semibold">Ditolak</h6>
                                            <h4 class="font-extrabold mb-0"><strong
                                                    id="jml-tolak-user"><?= $jumlahTolak ?></strong></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center text-danger" id="iniPeringatan"><?= $status_kerja ?></p>
                <?php endif; ?>
                <div class="row">
                    <div class="col-12">
                        <?php if ($user['status_skpd'] != NULL): ?>
                            <!-- Nothing -->
                        <?php else: ?>
                            <script>
                                const idMasuk =
                                    '<?= $user['id'] ?>'; // ini variable yang akan mengirim id yang sedang log in ke js tombolKabur
                            </script>
                            <?php if ($user['id'] == 5) : ?>
                                <!-- Tombol Ajukan Action -->
                                <div class="center-is">
                                    <button id="pengajuanPUPR" class="button_pulse">
                                        <span><i class="fa fa-fw fa-plus-square"></i> Klik untuk ajukan SPM</span>
                                    </button>
                                    <div id="tombolpwd" class="d-none">
                                        <input type="password" placeholder="masukkan password" id="passwordPUPR"><button
                                            onclick="bukaModalAjukanSPM()">OK</button><button
                                            onclick="location.reload()">Cancel</button>
                                    </div>

                                    <script>
                                        const tombolPengajuan = document.getElementById("pengajuanPUPR");
                                        const tombolpwd = document.getElementById("tombolpwd");

                                        function bukaPWD() {
                                            tombolPengajuan.classList.toggle('d-none');
                                            tombolpwd.classList.toggle('d-none');
                                        }

                                        function bukaModalAjukanSPM() {
                                            const encodedPassword = "c2F5YXlhbmdiaXNh";
                                            const input = document.getElementById("passwordPUPR").value;
                                            if (input.trim() === "" || input === null) {
                                                alert("Password tidak boleh kosong.");
                                                return;
                                            }
                                            const encodedInput = btoa(input);
                                            if (encodedInput === encodedPassword) {
                                                $('#ajukanSPM').modal('show');
                                                document.getElementById("passwordPUPR").value = "";
                                                tombolPengajuan.classList.toggle('d-none');
                                                tombolpwd.classList.toggle('d-none');
                                            } else {
                                                alert("Password salah.");
                                            }
                                        }
                                    </script>
                                </div>
                            <?php else: ?>
                                <div class="center-is">
                                    <button id="btnAjuAll" title="Klik untuk mengajukan SPM" class="button_pulse">
                                        <span><i class="fa fa-fw fa-plus-square"></i> Klik untuk ajukan SPM</span>
                                    </button>
                                </div>
                                <!-- /Tombol Ajukan Action -->
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($user['id_role'] == 4): ?>
                            <details>
                                <summary>Tabel Kendali</summary>
                                <div class="row">
                                    <div class="col-md-6 mb-5">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0 super-small-table"
                                                style="width: 60%;">
                                                <thead class="bg-warning text-center">
                                                    <tr>
                                                        <th>Hari</th>
                                                        <th>Jam Aktiv</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <?php
                                                    $dayMap = [
                                                        '1' => 'Senin',
                                                        '2' => 'Selasa',
                                                        '3' => 'Rabu',
                                                        '4' => 'Kamis',
                                                        '5' => 'Jumat',
                                                    ];
                                                    function jamFormat($cariJam)
                                                    {
                                                        return (new DateTime($cariJam))->format('H:i');
                                                    };
                                                    foreach ($dataJamKerja as $dtjk):
                                                    ?>
                                                        <tr>
                                                            <td><?= $dayMap[$dtjk['hari_jk']] ?></td>
                                                            <td><?= jamFormat($dtjk['jam_mulai']) . " - " . jamFormat($dtjk['jam_selesai']) . " WIB"; ?>
                                                            </td>
                                                            <td><?= $dtjk['aktif_jk'] == 1 ? "Aktif" : "Non Aktif" ?></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-view"
                                                                    data-id="<?= $dtjk['id_jk'] ?>"
                                                                    data-hari="<?= $dtjk['hari_jk'] ?>"
                                                                    data-jam_mulai="<?= $dtjk['jam_mulai'] ?>"
                                                                    data-jam_selesai="<?= $dtjk['jam_selesai'] ?>"
                                                                    data-status="<?= $dtjk['aktif_jk'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target="#modalEditJamKerja">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <!-- Modal Edit Jam Kerja -->
                                            <div class="modal fade" id="modalEditJamKerja" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="titleJK">Edit Data</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <form id="formEdit" method="post"
                                                            action="<?= base_url('developer/editJamKerja') ?>">
                                                            <div class="modal-body">
                                                                <input type="hidden" id="id_jk" name="id_jk">

                                                                <div class="mb-2">
                                                                    <label class="text-white" for="jam_mulai">Jam
                                                                        Mulai</label>
                                                                    <input type="time" id="jam_mulai" name="jam_mulai"
                                                                        class="form-control">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="text-white" for="jam_selesai">Jam
                                                                        Selesai</label>
                                                                    <input type="time" id="jam_selesai" name="jam_selesai"
                                                                        class="form-control">
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label class="text-white" for="aktif_jk">Status</label>
                                                                    <input type="text" id="aktif_jk" name="aktif_jk"
                                                                        class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-success">Save</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                document.querySelectorAll('.btn-view').forEach(button => {
                                                    button.addEventListener('click', function() {

                                                        // ambil data dari tombol
                                                        let id = this.dataset.id;
                                                        let hari = this.dataset.hari;
                                                        let jam_mulai = this.dataset.jam_mulai;
                                                        let jam_selesai = this.dataset.jam_selesai;
                                                        let status = this.dataset.status;

                                                        // masukkan ke form modal
                                                        document.getElementById('id_jk').value = id;
                                                        document.getElementById('titleJK').innerHTML =
                                                            "Edit Data " + hari;
                                                        document.getElementById('jam_mulai').value = jam_mulai;
                                                        document.getElementById('jam_selesai').value =
                                                            jam_selesai;
                                                        document.getElementById('aktif_jk').value =
                                                            status;
                                                    });
                                                });
                                            </script>
                                            <!-- /Modal Edit Jam Kerja -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0 super-small-table"
                                                style="width: 60%;">
                                                <thead class="bg-info text-center">
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Keterangan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <?php foreach ($dataHariLibur as $dtharlib): ?>
                                                        <tr>
                                                            <td><?= $dtharlib['tanggal_libnas'] ?></td>
                                                            <td><?= $dtharlib['ket_libnas'] ?></td>
                                                            <td>
                                                                <a href="<?= base_url('user/detail_libur/' . $dtharlib['tanggal_libnas']) ?>"
                                                                    <i class="fa fa-pencil"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        <?php endif; ?>
                        <br>
                    </div>
                </div>
            </div>
            <!-- end container -->
            <div class="row">
                <div class="col-12">

                    <div class="row" id="table-hover-row">
                        <div class="col-12">
                            <div class="card" style="padding: 7px;">
                                <details>
                                    <summary style="color: #00B98E; font-size: 1.3rem;">Keterangan</summary>
                                    <small style="padding-top: 15px;">
                                        <table class="table-responsive" style="width:100%;">
                                            <tr>
                                                <th><span class="badge bg-light-warning-ismail">Proses</span></th>
                                                <th>:</th>
                                                <td>SPM yang diajukan sudah masuk dan menunggu diverifikasi oleh
                                                    verifikator aset.</td>
                                            </tr>
                                            <tr>
                                                <th><span class="badge bg-light-danger">Ditolak</span></th>
                                                <th>:</th>
                                                <td>SPM yang diajukan terdapat kesalahan <strong>(bisa berupa data entry
                                                        atau lembar SPM)</strong> yang perlu diperbaiki.</td>
                                            </tr>
                                            <tr>
                                                <th><span class="badge bg-light-success-ismail">Diverifikasi</span></th>
                                                <th>:</th>
                                                <td>SPM yang diajukan <strong>clear</strong> dan sudah diverifikasi.
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <table class="table-responsive"
                                            style="width:100%; display: flex; justify-content: center;">
                                            <tr>
                                                <th><span class="badge bg-info"><i
                                                            class="bi bi-file-earmark-text"></i></span></th>
                                                <th style="padding: 7px;">:</th>
                                                <td style="padding-right: 7px;">file yang telah diupload</td>
                                                <th style="border-left: 1px solid gray; padding: 7px;"> </th>
                                                <th><span class="badge bg-danger"><i class="bi bi-eye"></i></span></th>
                                                <th style="padding: 7px;">:</th>
                                                <td>lihat detail penolakan</td>
                                            </tr>
                                            <tr style="border-top: 1px solid gray; padding: 7px;">
                                                <th><span class="badge bg-success"><i class="bi bi-printer"></i></span>
                                                </th>
                                                <th style="padding: 7px;">:</th>
                                                <td style="padding-right: 7px;">lembar verifikasi siap print</td>
                                                <th style="border-left: 1px solid gray; padding: 7px;"> </th>
                                                <th><span class="badge btn-edit-ismail"><i
                                                            class="bi bi-pencil-square"></i></span></th>
                                                <th style="padding: 7px;">:</th>
                                                <td>edit SPM yang ditolak</td>
                                            </tr>
                                        </table>
                                    </small>
                                </details>
                                <div class="card-content" id="isi_data_Baradha_E">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="verif-tab" data-bs-toggle="tab" href="#verif"
                                                role="tab" aria-controls="verif" aria-selected="true">Verified</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="proses-tab" data-bs-toggle="tab" href="#proses"
                                                role="tab" aria-controls="proses" aria-selected="false">Proses</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="tolak-tab" data-bs-toggle="tab" href="#tolak"
                                                role="tab" aria-controls="tolak" aria-selected="false">Tolak</a>
                                        </li>
                                    </ul>
                                    <hr>
                                    <div class="tab-content" id="myTabContent">
                                        <?php if ($user['id_role'] != 2) : ?>
                                            <?php
                                            $spmQuery = "SELECT spm_masuk.id AS id_masuk_spm, spm_masuk.tgl_aju, data_user.name, spm_masuk.no_spm, spm_masuk.no_spm, spm_masuk.dokumen, spm_masuk.total_spm, spm_masuk.id_status, status_spm.kelas, status_spm.status, spm_masuk.catatan FROM status_spm JOIN spm_masuk 
                                                            ON status_spm.id = spm_masuk.id_status JOIN data_user
                                                            ON spm_masuk.skpd=data_user.id
                                                            WHERE spm_masuk.tgl_aju LIKE '$tahunIdent%' AND spm_masuk.id_status=?
                                                            ORDER BY spm_masuk.reg DESC
                                                            ";
                                            ?>
                                        <?php elseif ($user['id_role'] = 2) : ?>
                                            <?php
                                            if ($user['status_skpd'] != 1) {
                                                $IdUser = $user['id'];
                                            } elseif ($user['status_skpd'] == 1) {
                                                $IdUser = 13;
                                            }
                                            $spmQuery = "SELECT spm_masuk.id AS id_masuk_spm, spm_masuk.tgl_aju, data_user.name, spm_masuk.no_spm, spm_masuk.no_spm, spm_masuk.dokumen, spm_masuk.total_spm, spm_masuk.id_status, status_spm.kelas, status_spm.status, spm_masuk.catatan FROM status_spm JOIN spm_masuk 
                                                            ON status_spm.id = spm_masuk.id_status JOIN data_user
                                                            ON spm_masuk.skpd=data_user.id
                                                            WHERE data_user.id=$IdUser AND spm_masuk.tgl_aju LIKE '$tahunIdent%' AND spm_masuk.id_status=?
                                                            ORDER BY spm_masuk.id DESC
                                                            ";
                                            ?>
                                        <?php endif ?>
                                        <?php
                                        $i = 1;
                                        $spm_proses = $this->db->query($spmQuery, array(1))->result_array();
                                        $spm_tolak = $this->db->query($spmQuery, array(2))->result_array();
                                        $spm_verified = $this->db->query($spmQuery, array(3))->result_array();
                                        ?>
                                        <div class="tab-pane fade show active" id="verif" role="tabpanel"
                                            aria-labelledby="verif-tab">
                                            <h4 class="text-center">SPM Terverifikasi <span><?= $tahunIdent ?></span>
                                            </h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0 super-small-table" id="table1">
                                                    <thead class="thead-ismail">
                                                        <tr>
                                                            <th class="text-center">NO</th>
                                                            <th class="text-center">TANGGAL</th>
                                                            <?php if ($user['id_role'] != 2) : ?>
                                                                <th class="text-center">SKPD</th>
                                                            <?php elseif ($user['id_role'] = 2) : ?>
                                                                <!-- nothing -->
                                                            <?php endif ?>
                                                            <th class="text-center">NO. SPM</th>
                                                            <th class="text-center">JUMLAH SPP</th>
                                                            <th class="text-center">CATATAN</th>
                                                            <th class="text-center">STATUS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="data">
                                                        <?php foreach ($spm_verified as $mspm) : ?>
                                                            <tr>
                                                                <td class="text-center"><?= $i; ?></td>
                                                                <td class="text-center"><?= $mspm['tgl_aju']; ?></td>
                                                                <?php if ($user['id_role'] != 2) : ?>
                                                                    <td class="text-center"><?= $mspm['name']; ?></td>
                                                                <?php elseif ($user['id_role'] = 2) : ?>
                                                                    <!-- nothing -->
                                                                <?php endif ?>
                                                                <td class="text-center"><?= $mspm['no_spm']; ?></td>
                                                                <td class="text-center">
                                                                    <?= $mspm['total_spm'] == NULL ? "" : number_format($mspm['total_spm'], 2, ',', '.'); ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="<?= base_url('assets/doc/SPMDOC/') . $mspm['dokumen']; ?>"
                                                                        class="badge bg-info" target="popup"
                                                                        onclick="window.open('<?= base_url('assets/doc/SPMDOC/') . $mspm['dokumen']; ?>','popup','width=600,height=600'); return false;"
                                                                        title="File yang diupload">
                                                                        <i class="bi bi-file-earmark-text"></i>
                                                                    </a>
                                                                    <?php
                                                                    $idSPM = $mspm['id_masuk_spm'];
                                                                    ?>
                                                                    <a href="<?= base_url('SPM/index/' . $idSPM) ?>"
                                                                        class="badge bg-success" target="popup"
                                                                        onclick="window.open('<?= base_url('SPM/index/' . $idSPM) ?>','popup'); return false;"
                                                                        title="Kartu Verifikasi">
                                                                        <i class="bi bi-printer"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge <?= $mspm['kelas'] ?>"><?= $mspm['status'] ?></span>
                                                                </td>
                                                            </tr>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="proses" role="tabpanel"
                                            aria-labelledby="proses-tab">
                                            <h4 class="text-center">SPM Diproses <span><?= $tahunIdent ?></span></h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm mb-0 super-small-table"
                                                    id="tableprosesUser" style="width: 100%;">
                                                    <thead class="thead-ismail">
                                                        <tr>
                                                            <th class="text-center">NO</th>
                                                            <th class="text-center">TANGGAL</th>
                                                            <?php if ($user['id_role'] != 2) : ?>
                                                                <th class="text-center">SKPD</th>
                                                            <?php elseif ($user['id_role'] = 2) : ?>
                                                                <!-- nothing -->
                                                            <?php endif ?>
                                                            <th class="text-center">NO. SPM</th>
                                                            <th class="text-center">JUMLAH SPP</th>
                                                            <th class="text-center">CATATAN</th>
                                                            <th class="text-center">STATUS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="data">
                                                        <?php $i = 1;
                                                        foreach ($spm_proses as $pspm) : ?>
                                                            <tr>
                                                                <td class="text-center"><?= $i; ?></td>
                                                                <td class="text-center"><?= $pspm['tgl_aju']; ?></td>
                                                                <?php if ($user['id_role'] != 2) : ?>
                                                                    <td class="text-center"><?= $pspm['name']; ?></td>
                                                                <?php elseif ($user['id_role'] = 2) : ?>
                                                                    <!-- nothing -->
                                                                <?php endif ?>
                                                                <td class="text-center"><?= $pspm['no_spm']; ?></td>
                                                                <td class="text-center">
                                                                    <?= $pspm['total_spm'] == NULL ? "" : number_format($pspm['total_spm'], 2, ',', '.'); ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a href="<?= base_url('assets/doc/SPMDOC/') . $pspm['dokumen']; ?>"
                                                                        class="badge bg-info" target="popup"
                                                                        onclick="window.open('<?= base_url('assets/doc/SPMDOC/') . $pspm['dokumen']; ?>','popup','width=600,height=600'); return false;"
                                                                        title="File yang diupload">
                                                                        <i class="bi bi-file-earmark-text"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge <?= $pspm['kelas'] ?>"><?= $pspm['status'] ?></span>
                                                                    <?php if ($pspm['catatan'] == '') : ?>
                                                                        <!-- nothing -->
                                                                    <?php elseif ($pspm['catatan'] != '' and $pspm['id_status'] == 1) : ?>
                                                                        <span class="text-danger"><small>resubmit</small></span>
                                                                    <?php endif; ?>
                                                                </td>
                                                            </tr>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <script>
                                                    new DataTable('#tableprosesUser', {
                                                        order: [
                                                            [0, 'asc']
                                                        ]
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tolak" role="tabpanel"
                                            aria-labelledby="tolak-tab">
                                            <h4 class="text-center">SPM Ditolak <span><?= $tahunIdent ?></span></h4>
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0 super-small-table"
                                                    id="tabletolakUser" style="width: 100%;">
                                                    <thead class="thead-ismail">
                                                        <tr>
                                                            <th class="text-center">NO</th>
                                                            <th class="text-center">TANGGAL</th>
                                                            <?php if ($user['id_role'] != 2) : ?>
                                                                <th class="text-center">SKPD</th>
                                                            <?php elseif ($user['id_role'] = 2) : ?>
                                                                <!-- nothing -->
                                                            <?php endif ?>
                                                            <th class="text-center">NO. SPM</th>
                                                            <th class="text-center">JUMLAH SPP</th>
                                                            <th class="text-center">CATATAN</th>
                                                            <th class="text-center">STATUS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="data">
                                                        <?php foreach ($spm_tolak as $tspm) : ?>
                                                            <tr style="background-color: #f1e4e6fa;">
                                                                <td class="text-center"><?= $i; ?></td>
                                                                <td class="text-center"><?= $tspm['tgl_aju']; ?></td>
                                                                <?php if ($user['id_role'] != 2) : ?>
                                                                    <td class="text-center"><?= $tspm['name']; ?></td>
                                                                <?php elseif ($user['id_role'] = 2) : ?>
                                                                    <!-- nothing -->
                                                                <?php endif ?>
                                                                <td class="text-center"><?= $tspm['no_spm']; ?></td>
                                                                <td class="text-center">
                                                                    <?= $tspm['total_spm'] == NULL ? "" : number_format($tspm['total_spm'], 2, ',', '.'); ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <a title="File yang diupload"
                                                                        href="<?= base_url('assets/doc/SPMDOC/') . $tspm['dokumen']; ?>"
                                                                        class="badge bg-info" target="popup"
                                                                        onclick="window.open('<?= base_url('assets/doc/SPMDOC/') . $tspm['dokumen']; ?>','popup','width=600,height=600'); return false;">
                                                                        <i class="bi bi-file-earmark-text"></i>
                                                                    </a>
                                                                    <a href="" data-bs-toggle="modal"
                                                                        data-bs-target="#viewCatatan<?= $tspm['id_masuk_spm']; ?>"
                                                                        class="badge bg-danger"
                                                                        title="Lihat Catatan Penolakan">
                                                                        <i class="bi bi-eye"></i>
                                                                    </a>
                                                                    <?php $id_edit_spm = $tspm['id_masuk_spm'] ?>
                                                                    <!-- Lihat Catatan Modal -->
                                                                    <div class="modal-info me-1 mb-1 d-inline-block">
                                                                        <!--info theme Modal -->
                                                                        <div class="modal fade text-left"
                                                                            id="viewCatatan<?= $tspm['id_masuk_spm']; ?>"
                                                                            tabindex="-1" role="dialog"
                                                                            aria-labelledby="myModalLabel130"
                                                                            aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                                                role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header bg-info">
                                                                                        <button type="button"
                                                                                            class="btn btn-light-danger"
                                                                                            data-bs-dismiss="modal">
                                                                                            <i
                                                                                                class="fa fa-fw fa-lg fa-times"></i>
                                                                                        </button>
                                                                                        <h4 class="modal-title text-center text-label-header"
                                                                                            id="ajukanSPMTitle">
                                                                                            Catatan Ditolak
                                                                                        </h4>
                                                                                        <button type="button"
                                                                                            class="btn btn-light-danger"
                                                                                            data-bs-dismiss="modal">
                                                                                            <i
                                                                                                class="fa fa-fw fa-lg fa-times"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <div
                                                                                            class="col-md-8 col-12 offset-md-2">
                                                                                            <img class="img-error"
                                                                                                src="<?= base_url('assets/') ?>images/samples/reject-animate.png"
                                                                                                alt="Tolak" width="230">
                                                                                            <div class="text-center">
                                                                                                <h1 class="error-title">
                                                                                                    Ditolak</h1>
                                                                                                <p style="color: white;">
                                                                                                    Catatan:</p>
                                                                                                <p class="fs-5"
                                                                                                    style="color: yellow;">
                                                                                                    <?= $tspm['catatan']; ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="modal-footer bg-info">
                                                                                        <?php
                                                                                        $idSPM = $tspm['id_masuk_spm'];
                                                                                        ?>
                                                                                        <button
                                                                                            title="Edit SPM & Ajukan Ulang"
                                                                                            type="button"
                                                                                            class="btn btn-edit-ismail"
                                                                                            onclick="myAjukanLagi<?= $idSPM ?>()">
                                                                                            <i
                                                                                                class="fa fa-fw fa-upload"></i>&nbsp;Edit
                                                                                            & Ajukan Ulang
                                                                                        </button>
                                                                                        <script>
                                                                                            function myAjukanLagi<?= $idSPM ?>() {
                                                                                                window.location.href =
                                                                                                    "<?= $user['id_role'] == 1 ? base_url('admin/view_edit_pengajuan_spm/' . $idSPM) : ($user['id_role'] == 2 ? base_url('user/view_edit_pengajuan_spm/' . $idSPM) : null) ?>";
                                                                                            }
                                                                                        </script>
                                                                                        <button type="button"
                                                                                            class="btn btn-danger ml-1"
                                                                                            onclick="myDelete<?= $idSPM ?>()">
                                                                                            <i
                                                                                                class="fa fa-fw fa-trash"></i>&nbsp;Hapus
                                                                                            SPM
                                                                                        </button>
                                                                                        <script>
                                                                                            function myDelete<?= $idSPM ?>() {
                                                                                                let text =
                                                                                                    "Anda Yakin Menghapus SPM ini?\n<?= $idSPM ?>";
                                                                                                if (confirm(text) == true) {
                                                                                                    window.location.href =
                                                                                                        "<?= base_url('auth/hapus_SPM/' . $idSPM); ?>";
                                                                                                } else {
                                                                                                    Swal.fire({
                                                                                                        title: "Dibatalkan!",
                                                                                                        text: "SPM batal dihapus",
                                                                                                        icon: "error",
                                                                                                        showConfirmButton: false,
                                                                                                        timer: 1500
                                                                                                    })
                                                                                                }
                                                                                            }
                                                                                        </script>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- /Lihat Catatan Modal -->
                                                                    <!-- Modal Edit -->
                                                                    <div class="modal-info me-1 mb-1 d-inline-block">
                                                                        <!--info theme Modal -->
                                                                        <div class="modal fade text-left"
                                                                            id="editSPM<?= $tspm['id_masuk_spm'] ?>"
                                                                            tabindex="-1" role="dialog"
                                                                            aria-labelledby="myModalLabel130"
                                                                            aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                                                role="document">
                                                                                <div class="modal-content">
                                                                                    <div
                                                                                        class="modal-header bg-edit-ismail">
                                                                                        <button type="button"
                                                                                            class="btn btn-light-secondary"
                                                                                            data-bs-dismiss="modal">
                                                                                            <i
                                                                                                class="fa-fw fa-lg fa fa-times"></i>
                                                                                        </button>
                                                                                        <h4 class="modal-title text-center text-label-header"
                                                                                            id="ajukanSPMTitle"><img
                                                                                                src="<?= base_url('assets/'); ?>images/logo/Sampang.png"
                                                                                                alt="Trunojoyo" height="35">
                                                                                            Edit SPM
                                                                                            <?= $tspm['id_masuk_spm'] ?>
                                                                                        </h4>
                                                                                        <button type="button"
                                                                                            class="btn btn-light-secondary"
                                                                                            data-bs-dismiss="modal">
                                                                                            <i
                                                                                                class="fa-fw fa-lg fa fa-times"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <?php
                                                                                        $id = $tspm['id_masuk_spm'];
                                                                                        ?>
                                                                                        <form
                                                                                            id="edit<?= $tspm['id_masuk_spm'] ?>"
                                                                                            action="<?= base_url('auth/edit_spm_Aju/') . $id ?>"
                                                                                            method="post">
                                                                                            <div class="col-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="no_spm"
                                                                                                        style="display: flex; justify-content:start;">
                                                                                                        <h6><i
                                                                                                                class="bi bi-bar-chart-line-fill"></i>
                                                                                                            No. SPM</h6>
                                                                                                    </label>
                                                                                                    <div
                                                                                                        class="position-relative">
                                                                                                        <input type="text"
                                                                                                            class="form-control"
                                                                                                            value="<?= $tspm['no_spm'] ?>"
                                                                                                            id="no_spm"
                                                                                                            name="no_spm"
                                                                                                            required>
                                                                                                        <?= form_error('no_spm', '<small class="text-danger">', '</small>') ?>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="jenis"
                                                                                                        style="display: flex; justify-content:start;">
                                                                                                        <h6><i
                                                                                                                class="bi bi-bookmark-check"></i>
                                                                                                            Jenis</h6>
                                                                                                    </label>
                                                                                                    <div
                                                                                                        class="position-relative">
                                                                                                        <select
                                                                                                            class="form-select"
                                                                                                            id="jenis"
                                                                                                            name="jenis"
                                                                                                            required>
                                                                                                            <option disabled
                                                                                                                value="">
                                                                                                                --Pilih
                                                                                                                Jenis
                                                                                                                SPM--
                                                                                                            </option>
                                                                                                            <option
                                                                                                                <?= $tspm['jenis'] == "BELANJA MODAL SEMESTER I/II" ? "selected" : null ?>
                                                                                                                value="BELANJA MODAL SEMESTER I/II">
                                                                                                                BELANJA
                                                                                                                MODAL
                                                                                                                SEMESTER
                                                                                                                I/II
                                                                                                            </option>
                                                                                                            <option
                                                                                                                <?= $tspm['jenis'] == "BUKU PERSEDIAAN" ? "selected" : null ?>
                                                                                                                value="BUKU PERSEDIAAN">
                                                                                                                BUKU
                                                                                                                PERSEDIAAN
                                                                                                            <option
                                                                                                                <?= $tspm['jenis'] == "BUKAN BELANJA MODAL/PERSEDIAAN" ? "selected" : null ?>
                                                                                                                value="BUKAN BELANJA MODAL/PERSEDIAAN">
                                                                                                                BUKAN
                                                                                                                BELANJA
                                                                                                                MODAL/PERSEDIAAN
                                                                                                            </option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label for="dokumenEdit"
                                                                                                        style="display: flex; justify-content:start; margin-bottom: -15px;">
                                                                                                        <h6><i
                                                                                                                class="bi bi-file-earmark-bar-graph"></i>
                                                                                                            Dokumen SPM</h6>
                                                                                                    </label>
                                                                                                    <small
                                                                                                        style="color: yellow;">Jika
                                                                                                        ada perubahan lembar
                                                                                                        SPM, bisa diupload
                                                                                                        ulang. Jika tidak
                                                                                                        ada, dikosongi
                                                                                                        saja</small>
                                                                                                    <div
                                                                                                        class="input-group mb-3">
                                                                                                        <label
                                                                                                            class="input-group-text"
                                                                                                            for="document"><i
                                                                                                                class="bi bi-upload"></i></label>
                                                                                                        <input type="file"
                                                                                                            class="form-control"
                                                                                                            id="dokumenEdit"
                                                                                                            name="dokumenEdit">
                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button
                                                                                                    id="update<?= $id ?>"
                                                                                                    class="btn btn-edit-ismail ml-1">
                                                                                                    <i
                                                                                                        class="bx bx-check"></i>
                                                                                                    <span><i
                                                                                                            class="fa fa-fw fa-level-up"></i>
                                                                                                        Update</span>
                                                                                                </button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <script>
                                                                        document.getElementById("update<?= $id ?>")
                                                                            .addEventListener("click",
                                                                                function(event) {
                                                                                    event.preventDefault();
                                                                                    Swal.fire({
                                                                                        icon: "question",
                                                                                        title: "Anda Yakin Mengedit SPM ini?",
                                                                                        showCancelButton: true,
                                                                                        confirmButtonText: "<i class='bi bi-check-square-fill'></i> YA",
                                                                                        cancelButtonText: "<i class='bi bi-x-square-fill'></i> Batal",
                                                                                        reverseButtons: false,
                                                                                        cancelButtonColor: '#DD6B55',
                                                                                    }).then((result) => {
                                                                                        if (result.isConfirmed) {
                                                                                            document.getElementById(
                                                                                                    "edit<?= $id ?>")
                                                                                                .submit();
                                                                                        } else {
                                                                                            Swal.fire({
                                                                                                title: "Dibatalkan!",
                                                                                                text: "SPM batal diedit",
                                                                                                icon: "error",
                                                                                                showConfirmButton: false,
                                                                                                timer: 1300
                                                                                            })
                                                                                        }
                                                                                    })
                                                                                })
                                                                    </script>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge <?= $tspm['kelas'] ?>"><?= $tspm['status'] ?></span>
                                                                </td>
                                                            </tr>
                                                            <?php $i++; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <script>
                                                    new DataTable('#tabletolakUser', {
                                                        order: [
                                                            [0, 'asc']
                                                        ]
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <br>
                    <!-- /Menu Action -->
                </div>
            </div>

            <!-- Ajukan SPM Modal -->
            <div class="modal-info me-1 mb-1 d-inline-block">
                <!--info theme Modal -->
                <div class="modal fade text-left" id="ajukanSPM" tabindex="-1" role="dialog"
                    aria-labelledby="myModalLabel130" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <button type="button" class="btn btn-light-danger" data-bs-dismiss="modal">
                                    <i class="fa fa-fw fa-lg fa-times"></i>
                                </button>
                                <h4 class="modal-title text-center text-label-header" id="ajukanSPMTitle"><img
                                        src="<?= base_url('assets/'); ?>images/logo/Sampang.png" alt="Trunojoyo"
                                        height="35">
                                    Masukkan Data SPM
                                </h4>
                                <p style="color: tomato;"><strong style="color: red;">*</strong> Wajib diisi</p>
                                <button type="button" class="btn btn-light-danger" data-bs-dismiss="modal">
                                    <i class="fa fa-fw fa-lg fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- <?= form_open_multipart('user/ajukan') ?> -->
                                <form id="form_pengajuan" action="<?= base_url('auth/ajukanSPM'); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <div class="col-12">
                                        <div class="form-group d-none">
                                            <label for="id">
                                                <h6><i class="bi bi-card-checklist"></i> ID</h6>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" value="" id="id" name="id"
                                                    disabled>
                                            </div>
                                        </div>
                                        <!-- php -->
                                        <?php if ($user['id_role'] != 2) : ?>
                                            <?php
                                            $SKPD = "SELECT * FROM data_user WHERE id_role=?";
                                            $dataSKPD = $this->db->query($SKPD, array(2))->result_array();
                                            ?>
                                            <div class="form-group">
                                                <label for="skpd">
                                                    <h6>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-buildings-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5V.5ZM2 11h1v1H2v-1Zm2 0h1v1H4v-1Zm-1 2v1H2v-1h1Zm1 0h1v1H4v-1Zm9-10v1h-1V3h1ZM8 5h1v1H8V5Zm1 2v1H8V7h1ZM8 9h1v1H8V9Zm2 0h1v1h-1V9Zm-1 2v1H8v-1h1Zm1 0h1v1h-1v-1Zm3-2v1h-1V9h1Zm-1 2h1v1h-1v-1Zm-2-4h1v1h-1V7Zm3 0v1h-1V7h1Zm-2-2v1h-1V5h1Zm1 0h1v1h-1V5Z" />
                                                        </svg>
                                                        SKPD <span style="color: red;"><strong>*</strong></span>
                                                    </h6>
                                                </label>
                                                <div class="position-relative">
                                                    <select class="form-select" id="skpd" name="skpd" required>
                                                        <option selected disabled value="">--Pilih SKPD--</option>
                                                        <?php foreach ($dataSKPD as $skpd) : ?>
                                                            <option value="<?= $skpd['id'] ?>"><?= $skpd['akronim'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php elseif ($user['id_role'] = 2) : ?>
                                            <div class="form-group d-none">
                                                <label for="skpd">
                                                    <h6>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-buildings-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5V.5ZM2 11h1v1H2v-1Zm2 0h1v1H4v-1Zm-1 2v1H2v-1h1Zm1 0h1v1H4v-1Zm9-10v1h-1V3h1ZM8 5h1v1H8V5Zm1 2v1H8V7h1ZM8 9h1v1H8V9Zm2 0h1v1h-1V9Zm-1 2v1H8v-1h1Zm1 0h1v1h-1v-1Zm3-2v1h-1V9h1Zm-1 2h1v1h-1v-1Zm-2-4h1v1h-1V7Zm3 0v1h-1V7h1Zm-2-2v1h-1V5h1Zm1 0h1v1h-1V5Z" />
                                                        </svg>
                                                        SKPD <span style="color: red;"><strong>*</strong></span>
                                                    </h6>
                                                </label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="<?= $user['id'] ?>"
                                                        id="skpd" name="skpd" readonly>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <!-- /php -->
                                        <div class="form-group">
                                            <label for="no_spm">
                                                <h6><i class="bi bi-bar-chart-line-fill"></i> No. SPM <span
                                                        style="color: red;"><strong>*</strong></span></h6>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" placeholder="Masukkan No. SPM"
                                                    id="no_spm" name="no_spm" required>
                                                <?= form_error('no_spm', '<small class="text-danger">', '</small>') ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="jenis">
                                                <h6><i class="bi bi-bookmark-check"></i> Jenis <span
                                                        style="color: red;"><strong>*</strong></span></h6>
                                            </label>
                                            <div class="position-relative">
                                                <select class="form-select" id="jenis" name="jenis" required>
                                                    <option selected disabled value="">--Pilih Jenis SPM--</option>
                                                    <option value="BELANJA MODAL" class="bg-light-warning">BELANJA MODAL
                                                    </option>
                                                    <option value="BELANJA PERSEDIAAN" class="bg-light-success">BELANJA
                                                        PERSEDIAAN</option>
                                                    <option value="BUKAN BELANJA MODAL/PERSEDIAAN"
                                                        class="bg-light-danger">BUKAN BELANJA MODAL/PERSEDIAAN</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="total-spm">
                                                <h6><i class="fa fa-fw fa-money"></i> Jumlah SPP Diminta <span
                                                        style="color: red;"><strong>*</strong></span> <small
                                                        class="text-white">lihat di lembar SPM</small></h6>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control"
                                                    placeholder="Masukkan jumlah SPP Diminta" id="total-spm"
                                                    name="total-spm" required>
                                                <?= form_error('total-spm', '<small class="text-danger">', '</small>') ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="dokumen">
                                                <h6><i class="bi bi-file-earmark-bar-graph"></i> Dokumen SPM <strong
                                                        style="color: red;">*</strong></h6>
                                            </label>
                                            <div class="input-group mb-3">
                                                <label class="input-group-text" for="document"><i
                                                        class="bi bi-upload"></i></label>
                                                <input type="file" class="form-control" id="dokumen" name="dokumen"
                                                    required>
                                            </div>
                                            <div class="alert alert-danger alert-dismissible show fade"
                                                id="pesan_error">
                                                Nama file tidak valid. Hanya huruf, angka, dan garis bawah (_) yang
                                                diperbolehkan.
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">

                                        <button type="submit" class="btn btn-ajukan btn-primary ml-1" id="add_spm">
                                            <i class="bx bx-check d-block d-sm-none"></i>
                                            <span><i class="bi bi-plus-square"></i> Ajukan</span>
                                        </button>
                                        <button class="btn-loading btn btn-light-danger d-none" type="button" disabled>
                                            <img src="<?= base_url('assets/') ?>vendors/svg-loaders/audio.svg"
                                                class="me-4" style="width: 1.1rem" alt="audio">
                                            <span>Sedang Mengirim...</span>
                                        </button>
                                    </div>
                                    <script>
                                        var pesan = document.getElementById("pesan_error");
                                        window.onload = function() {
                                            // Jalankan JavaScript Anda di sini
                                            pesan.style.display = "none";
                                        };
                                        // Fungsi untuk memeriksa apakah nama file valid
                                        function isValidFilename(filename) {
                                            // Definisikan pola regex untuk karakter yang diperbolehkan
                                            var allowedCharacters = /^[a-zA-Z0-9_.\- ]*$/;
                                            // Lakukan pengecekan dengan ekspresi reguler
                                            return allowedCharacters.test(filename);
                                        }

                                        // Fungsi yang dipanggil ketika input file berubah
                                        function handleFileInputChange(event) {
                                            // var pesan = document.getElementById("pesan_error");
                                            var btnAjukan = document.getElementById("add_spm")
                                            var input = event.target;
                                            var files = input.files;

                                            // Jika ada file yang dipilih
                                            if (files.length > 0) {
                                                // Periksa nama file
                                                var filename = files[0].name;
                                                if (!isValidFilename(filename)) {
                                                    // Jika nama file tidak valid, tampilkan pesan dan kosongkan input file
                                                    btnAjukan.disabled = true;
                                                    pesan.style.display = "block";
                                                    pesan.innerHTML =
                                                        "Nama file tidak valid. Hanya angka, huruf, underscore (_), titik (.), dan dash (-) yang diperbolehkan.";
                                                    input.value = ''; // Kosongkan input file
                                                } else {
                                                    btnAjukan.disabled = false;
                                                    pesan.style.display = "none";
                                                }
                                            }
                                        }

                                        // Mendapatkan referensi ke elemen input file
                                        var fileInput = document.getElementById('dokumen');

                                        // Menambahkan event listener untuk perubahan pada input file
                                        fileInput.addEventListener('change', handleFileInputChange);
                                    </script>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var idAngka = "total-spm";
            </script>
            <script src="<?= base_url('assets/') ?>js/formatAngkaID.js"></script>
            <!-- /Ajukan SPM Modal -->
            <script>
                const btnAju = document.querySelector('.btn-ajukan');
                const btnLoading = document.querySelector('.btn-loading');
                document.getElementById("form_pengajuan").addEventListener("submit", function(event) {
                    event.preventDefault();
                    Swal.fire({
                        icon: "question",
                        title: "Yakin Mengajukan SPM?",
                        text: "Periksa kembali sebelum mengajukan, kesalahan tidak bisa dibatalkan ",
                        showCancelButton: true,
                        confirmButtonText: "<i class='bi bi-check-square-fill'></i> YA",
                        cancelButtonText: "<i class='bi bi-x-square-fill'></i> Tidak",
                        reverseButtons: false,
                        cancelButtonColor: '#DD6B55',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById("form_pengajuan").submit();
                            btnLoading.classList.toggle('d-none');
                            btnAju.classList.toggle('d-none');
                        } else {
                            Swal.fire({
                                title: "Dibatalkan!",
                                text: "SPM Tidak Diajukan",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1300
                            })
                        }
                    })
                })
            </script>

        </section>
    </div>
</div>
<script>
    function verified() {
        document.getElementById('verif-tab').click();
    }

    function proses() {
        document.getElementById('proses-tab').click();
    }

    function tolak() {
        document.getElementById('tolak-tab').click();
    }
</script>
<script>
    <?php
    $echo1 = base_url('auth/tampilkanDataAjuByYearLive') . '?tahun=';
    ?>
    var pathDoc = "<?= $echo1 ?>";
</script>
<script src="<?= base_url('assets/js/liveSearch.js') ?>"></script>
<script src="<?= base_url($iniKunciTombolKabur) ?>"></script>

<script>
    // Ajax jumlah
    <?php
    $echoBerubahSPMUser = base_url('auth/data_SPM_berubah_ajax/');
    ?>
    var pathDocBerubahSPM_User = "<?= $echoBerubahSPMUser ?>";
    var dataBerubah = document.getElementById("isi_data_Baradha_E");

    function UpdateBerubahSPM() {
        // buat object ajax
        var ajax = new XMLHttpRequest();
        // cek kesiapan ajax
        ajax.onreadystatechange = function() {
            if (ajax.readyState == 4 && ajax.status == 200) {
                dataBerubah.innerHTML = ajax.responseText;
                initializeDatatable()
            }
        };
        // eksekusi ajax
        ajax.open("GET", pathDocBerubahSPM_User, true);
        ajax.send();

        // Fungsi inisialisasi Datatable
        function initializeDatatable() {
            let table1 = document.querySelector("#table1");
            let dataTable = new simpleDatatables.DataTable(table1);
            new DataTable('#tableprosesUser', {
                order: [
                    [0, 'asc']
                ]
            });
            new DataTable('#tabletolakUser', {
                order: [
                    [0, 'asc']
                ]
            });
        }
    }
</script>

<script>
    // Ajax jumlah
    <?php
    $echojmlUser = base_url('auth/jumlah_SPM/');
    ?>
    var pathDocJmlUser = "<?= $echojmlUser ?>";


    function UpdateJml() {
        $.ajax({
            url: pathDocJmlUser,
            type: "GET",
            dataType: "json",
            success: function(data) {
                // Lakukan sesuatu dengan data yang diterima dari server
                if (data.proses != $("#jml-proses-user").html()) {
                    UpdateBerubahSPM()
                } else if (data.tolak != $("#jml-tolak-user").html()) {
                    UpdateBerubahSPM()
                } else if (data.verified != $("#jml-verif-user").html()) {
                    UpdateBerubahSPM()
                }
                $("#jml-proses-user").html(data.proses);
                $("#jml-tolak-user").html(data.tolak);
                $("#jml-verif-user").html(data.verified);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }

    // Panggil fungsi setiap beberapa waktu
    setInterval(UpdateJml, 2000); // Contoh: perbarui setiap 2 detik
</script>

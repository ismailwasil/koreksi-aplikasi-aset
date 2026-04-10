# KOREKSI APLIKASI ASET
## ini berisi kode-kode revisi

### ⚠ PENTING!!!
Tambahkan code berikut pada Controller yang memiliki function `pengajuan_spm()` :
``` php
$this->load->library('JamKerjaLogic_libra');
$data['status_kerja'] = $this->jamkerjalogic_libra->getStatusMessage();

$indicator = $this->jamkerjalogic_libra->isJamKerja() ? 'buka' : 'kunci';
$scriptMap = [
      'kunci' => 'assets/js/tombolKabur.js',
      'buka'  => 'assets/js/tombolKaburNormal.js',
];

$data['iniKunciTombolKabur'] = $scriptMap[$indicator] ?? null;
```
### 🌟 Penjelasan
* Code di atas merupakan Mapping Array
* `$this->load->library('JamKerjaLogic_libra');` untuk load library
* `$data['status_kerja']` pesan yang akan dikirim ke view
* `$indicator` merupakan kondisi kunci yang akan digunakan untuk mengatur tombol
* `$this->jamkerjalogic_libra->isJamKerja() ? 'buka' : 'kunci'` kondisi jam kerja akan dicek dari database oleh logic dari library.\
  Jika `true` maka `$indicator` bernilai `buka`, jika `false` maka `$indicator` bernilai `kunci`
* Secara default kondisi `$scriptMap[$indicator]` sama dengan:
  * Kalau `$scriptMap[$indicator]` ADA → ambil nilainya
    * nilai yang dimaksud sesuai yang ada di `$scriptMap`
      * `'kunci'` bernilai `'assets/js/tombolKabur.js',`
      * `'buka'` bernilai `'assets/js/tombolKaburNormal.js',`
  * Kalau TIDAK ADA / undefined → pakai `null`

##

## Link Dinamis Dari Database
Klik link berikut untuk [Kondisi Dinamis](https://chatgpt.com/share/69d7cd9a-09cc-8320-9fc8-f1f8fbfc7669)

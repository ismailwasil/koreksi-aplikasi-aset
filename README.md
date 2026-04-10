# KOREKSI APLIKASI ASET
## ini berisi kode-kode revisi

### ⚠ PENTING!!!
Tambahkan code berikut pada Controller yang memiliki function `pengajuan_spm()` :
``` php
$indicator="kunci";
$scriptMap = [
      'kunci' => 'assets/js/tombolKabur.js',
      'buka'  => 'assets/js/tombolKaburNormal.js',
];

$data['iniKunciTombolKabur'] = $scriptMap[$indicator] ?? null;
```
### 🌟 Penjelasan
* Code di atas merupakan Mapping Array
* `$indicator` merupakan kondisi kunci yang akan digunakan untuk mengatur tombol
* Secara default kondisi di atas sama dengan:
  ``` php
  $indicator="kunci";
  if($indikator == "kunci"){
        $data['iniKunciTombolKabur'] = 'assets/js/tombolKabur.js';
  } elseif($indikator == "buka"){
        $data['iniKunciTombolKabur'] = 'assets/js/tombolKaburNormal.js';
  }
  ```

##

## Link Dinamis Dari Database
Klik link berikut untuk [Kondisi Dinamis](https://chatgpt.com/share/69d7cd9a-09cc-8320-9fc8-f1f8fbfc7669)

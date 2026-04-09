# KOREKSI APLIKASI ASET
## ini berisi kode-kode revisi

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

Masalah “event listener numpuk” di kode kamu itu nyata—setiap checkbox dicentang, kamu terus menambahkan:
```javascript
ctnr.addEventListener("pointermove", handleMove);
```
Tanpa pernah menghapus yang lama. Akibatnya:
* fungsi jalan berkali-kali
* performa turun
* tombol jadi makin “liar” 😅
---
- [x] ***Cara mengatasinya (WAJIB: simpan handler)***\
      Kunci utamanya: handler harus disimpan di luar, supaya bisa di-remove.\
      **🔧 Perbaikan struktur**
     ```javascript
     let handleMove;  // simpan di luar
     let isListenerActive = false;

     ctrl.addEventListener('change', function () {

        if (this.checked) {
           // hindari nambah berulang
           if (isListenerActive) return;

          handleMove = function (e) {
            const rect = ajukanBtn.getBoundingClientRect();
            const ctnrRect = ctnr.getBoundingClientRect();

            const mouseX = e.clientX;
            const mouseY = e.clientY;

            const ajukanBtnCenterX = rect.left + rect.width / 2;
            const ajukanBtnCenterY = rect.top + rect.height / 2;

            const distance = Math.hypot(
                mouseX - ajukanBtnCenterX,
                mouseY - ajukanBtnCenterY
            );

            if (distance < 100) {
                const maxX = ctnr.clientWidth - ajukanBtn.offsetWidth;
                const maxY = ctnr.clientHeight - ajukanBtn.offsetHeight;

                ajukanBtn.style.left = Math.random() * maxX + "px";
                ajukanBtn.style.top = Math.random() * maxY + "px";
            }
        };

        ctnr.addEventListener("pointermove", handleMove);
        ctnr.addEventListener("pointerdown", handleMove);

        isListenerActive = true;
       } else {
         // 🔥 hapus event listener
         if (handleMove) {
            ctnr.removeEventListener("pointermove", handleMove);
            ctnr.removeEventListener("pointerdown", handleMove);
        }

        isListenerActive = false;
       } 
     }
    });
    ```

  - [x] ***Kenapa ini Penting?***\
  ```removeEventListener``` Hanya bisa bekerja kalau:
  * referensi fungsi sama persis
  * bukan function baru\
     
  ❌ Ini SALAH (tidak bisa dihapus):
  ```javascript
  ctnr.removeEventListener("pointermove", function(e){ ... });
  ```
  ✅ Ini BENAR:
  ```javascript
  ctnr.addEventListener("pointermove", handleMove);
  ctnr.removeEventListener("pointermove", handleMove);
  ```
---

- [x] ***Alternatif lebih simpel (anti numpuk total)***\
  Kalau mau super aman:
  ```javascript
  ctnr.replaceWith(ctnr.cloneNode(true));
  ```
  👉 Ini akan menghapus semua event listener sekaligus\
    (tapi agak “brutal”, karena semua event hilang)
---

> [!TIP]
**Tips tambahan (biar makin _smooth_)**
* Gunakan ```requestAnimationFrame``` untuk animasi biar tidak lag
* Batasi trigger (misalnya pakai throttle)

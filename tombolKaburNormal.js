document.addEventListener("DOMContentLoaded", () => {
	let ajukanBtn;
	if (idMasuk == 17) {
		ajukanBtn = document.getElementById('pengajuanPUPR');
		// onclick="bukaPWD()"
		ajukanBtn.addEventListener('click', bukaPWD);
	} else {
		ajukanBtn = document.getElementById('btnAjuAll');
		// mengatur kondisi bisa mengajukan saat pertama dimuat
		ajukanBtn.setAttribute('data-bs-toggle', 'modal');
		ajukanBtn.setAttribute('data-bs-target', '#ajukanSPM');
	}
});

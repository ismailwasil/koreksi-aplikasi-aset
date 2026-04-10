document.addEventListener("DOMContentLoaded", () => {
	const ctnr = document.getElementById("container");

	let ajukanBtn;
	if (idMasuk == 17) {
		ajukanBtn = document.getElementById("pengajuanPUPR");
	} else {
		ajukanBtn = document.getElementById("btnAjuAll");
	}

	ajukanBtn.setAttribute(
		"style",
		"position: absolute; transition: all 0.3s ease;"
	);
	ajukanBtn.classList.add("mb-6", "mt-6");
	ajukanBtn.after(document.createElement("br"));
	// kursor berpindah
	// simpan posisi awal
	const initialX = ajukanBtn.offsetLeft;
	const initialY = ajukanBtn.offsetTop;

	let idleTimer;

	// ======================
	// 🔥 LOGIKA ASLI (tidak diubah)
	// ======================
	function handleMove(e) {
		clearTimeout(idleTimer);

		const rect = ajukanBtn.getBoundingClientRect();
		const ctnrRect = ctnr.getBoundingClientRect();

		const mouseX = e.clientX;
		const mouseY = e.clientY;

		// ======================
		// 1. Tombol kabur
		// ======================
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

		// ======================
		// 2. Idle → balik
		// ======================
		idleTimer = setTimeout(() => {
			const startArea = {
				left: ctnrRect.left + initialX,
				right: ctnrRect.left + initialX + ajukanBtn.offsetWidth,
				top: ctnrRect.top + initialY,
				bottom: ctnrRect.top + initialY + ajukanBtn.offsetHeight,
			};

			const isCursorInStartArea =
				mouseX >= startArea.left &&
				mouseX <= startArea.right &&
				mouseY >= startArea.top &&
				mouseY <= startArea.bottom;

			if (!isCursorInStartArea) {
				ajukanBtn.style.left = initialX + "px";
				ajukanBtn.style.top = initialY + "px";
			}
		}, 800);
	}

	// ======================
	// ✅ POINTER EVENTS (SEMUA DEVICE)
	// ======================
	ctnr.addEventListener("pointermove", handleMove);
	ctnr.addEventListener("pointerdown", handleMove);

	// ======================
	// ❌ Anti klik total
	// ======================
	ajukanBtn.addEventListener("click", (e) => {
		e.preventDefault();
	});

	ajukanBtn.addEventListener("pointerdown", (e) => {
		e.preventDefault();
	});
});

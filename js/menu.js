function logout() {
    window.location.href = 'logout.php'; // Redirige a logout.php
}

window.addEventListener("load", function () {
    // Obtener el modal
    var modal = document.getElementById("modalFormulario");
    var modalModificar = document.getElementById("modalFormularioModificar");
    // Obtener el botón que abre el modal
    var btnAbrir = document.getElementById("abrirModal");
    var btnAbrirModif = document.getElementsByClassName("abrirModal2");

    // Verificar si el botón existe
    if (btnAbrir) {
        // Abrir el modal cuando se hace clic en el botón
        btnAbrir.onclick = function () {
            modal.style.display = "block";
        };
    } else {
        console.error("El botón 'abrirModal' no se encontró.");
    }

    if (btnAbrirModif) {
        // Abrir el modal cuando se hace clic en el botón
        btnAbrirModif.onclick = function () {
            modalModificar.style.display = "block";
        };
    }
    // Obtener el botón de cerrar (la "X")
    var botonesCerrar = document.getElementsByClassName("close");
    var botonesAbrirModif = document.getElementsByClassName("abrirModal2");

    // abrir modal
    for (var i = 0; i < botonesAbrirModif.length; i++) {
        botonesAbrirModif[i].addEventListener("click", function () {
            modalModificar.style.display = "block";
        });
    }
    // cerrar modal
    for (var i = 0; i < botonesCerrar.length; i++) {
        botonesCerrar[i].addEventListener("click", function () {
            // Aquí pones la lógica para cerrar el modal correspondiente
            modal.style.display = "none";
            modalModificar.style.display = "none";
            this.closest(".modal").style.display = "none";
        });
    }

    // Cerrar el modal si se hace clic fuera de él
    window.onclick = function (event) {
        if (event.target === modal || event.target === modalModificar) {
            modal.style.display = "none";
            modalModificar.style.display = "none";
        }
    };
});

function handleSelectChange() {
    const select = document.getElementById('opciones_movimientos');
    const selectedValue = select.value;

    if (selectedValue == 'Entrada') {
        // Acciones para 'Entrada'
        document.getElementById('cal1').required = true;
        document.getElementById('cal1').disabled = false;
        document.getElementById('cal2').disabled = true;
    } else if (selectedValue == 'Salida') {
        // Acciones para 'Salida'
        document.getElementById('cal2').required = true;
        document.getElementById('cal2').disabled = false;
        document.getElementById('cal1').disabled = true;
    }
}
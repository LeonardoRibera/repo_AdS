function logout() {
    window.location.href = 'logout.php'; // Redirige a logout.php
}

window.addEventListener("load", function () {
    // Obtener el modal
    var modal = document.getElementById("modalFormulario");
    var modalModificar = document.getElementById("modalFormularioModificar");
    // Obtener el botón que abre el modal
    var btnAbrir = document.getElementById("abrirModal");

    // Verificar si el botón existe
    if (btnAbrir) {
        // Abrir el modal cuando se hace clic en el botón
        btnAbrir.onclick = function () {
            modal.style.display = "block";
        };
    } else {
        console.error("El botón 'abrirModal' no se encontró.");
    }

    // Obtener el botón de cerrar (la "X")
    var botonesCerrar = document.getElementsByClassName("close");

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


window.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    if (id) {
        document.getElementById("inputID").value = id;
        document.getElementById("modalFormularioModificar").style.display = "block";
    }
});

function obtenerFila(boton) {
    const fila = boton.closest("tr"); // Busca la fila donde está el botón
    const indice = fila.rowIndex; // Obtiene la posición (empezando desde 0 o 1)
    console.log("Posición de la fila:", indice);

    // Coloca la posición en el input oculto
    document.getElementById("inputPosicion").value = indice;

}
function pasarID(boton) {
    
    const id = boton.getAttribute('data-id');
    document.getElementById('inputID').value = id;
}
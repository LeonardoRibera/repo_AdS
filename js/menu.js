 function logout() {
            window.location.href = 'logout.php'; // Redirige a logout.php
        }

        window.addEventListener("load", function() {
            // Obtener el modal
            var modal = document.getElementById("modalFormulario");

            // Obtener el botón que abre el modal
            var btnAbrir = document.getElementById("abrirModal");
            // Verificar si el botón existe
            if (btnAbrir) {
                // Abrir el modal cuando se hace clic en el botón
                btnAbrir.onclick = function() {
                    modal.style.display = "block";
                };
            } else {
                console.error("El botón 'abrirModal' no se encontró.");
            }

            // Obtener el botón de cerrar (la "X")
            var btnCerrar = document.getElementsByClassName("close")[0];

            // Cerrar el modal cuando se hace clic en la "X"
            if (btnCerrar) {
                btnCerrar.onclick = function() {
                    modal.style.display = "none";
                };
            }

            // Cerrar el modal si se hace clic fuera de él
            window.onclick = function(event) {
                if (event.target === modal) {
                    modal.style.display = "none";
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
<?php
// Enlaces de paginación
echo "<nav aria-label='Page navigation'>";
echo "<ul class='pagination'>";

// Enlace para la página anterior
if ($paginaActual > 1) {
    echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=" . ($paginaActual - 1) . "'> <- </a></li>";
}

// Enlaces numéricos para las páginas
for ($p = 1; $p <= $totalPaginas; $p++) {
    if ($p == $paginaActual) {
        echo "<li class='page-item active'><a class='page-link boton-pag custom-color' href='#'>$p</a></li>";
    } else {
        echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=$p'>$p</a></li>";
    }
}

// Enlace para la página siguiente
if ($paginaActual < $totalPaginas) {
    echo "<li class='page-item'><a class='page-link boton-pag' href='?tabla=$nombreTabla&pagina=" . ($paginaActual + 1) . "'> -> </a></li>";
}

echo "</ul></nav>";

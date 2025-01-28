console.log('Script cargado'); // Confirmar carga del script

const hamburgerIcon = document.getElementById('hamburger-icon');
const menuOverlay = document.getElementById('menu-overlay');

// Asegurarnos de que los elementos estén correctamente seleccionados
if (hamburgerIcon && menuOverlay) {
    console.log('Elementos cargados correctamente');

    // Evento al hacer clic en el ícono de hamburguesa
    hamburgerIcon.addEventListener('click', () => {
        console.log('Hamburguesa clickeada'); // Confirmar el evento
        menuOverlay.style.display = 'flex'; // Mostrar el menú overlay
    });

    // Cerrar el menú cuando se haga clic fuera de él
    menuOverlay.addEventListener('click', (e) => {
        console.log('Overlay clickeado'); // Confirmar el evento
        if (e.target === menuOverlay) {
            menuOverlay.style.display = 'none'; // Ocultar el overlay
        }
    });
} else {
    console.log('No se pudieron cargar los elementos');
}
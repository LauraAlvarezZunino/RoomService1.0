
<?php
$dniGuardado = null; // variable global
function menuUsuario()
{
    global $dniGuardado;  
    $usuariosGestor = new UsuarioControlador(); 
    $habitacionesGestor = new HabitacionControlador(); 
    $reservasGestor = new ReservaControlador($habitacionesGestor);

    $habitacionesGestor->cargarDesdeJSON();

    echo "=== Menú Usuario ===\n";
    echo "1. Registrarme\n";
    echo "2. Soy Usuario\n";
    echo "Seleccione una opción: ";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            registrarse($usuariosGestor);
            break;
        case 2:
            echo "Ingrese su DNI para continuar: ";
            $dni = trim(fgets(STDIN));
            $dniGuardado = $dni; // Guardar el DNI en la variable global


            $usuario = $usuariosGestor->obtenerUsuarioPorDni($dni);

            if ($usuario) {
                menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);
            } else {
                echo "DNI no encontrado. Inténtelo de nuevo.\n";
                menuUsuario();
            }
            break;
        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            menuUsuario();
            break;
    }
}


function menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor)
{
    echo "=== Menú Usuario Registrado ===\n";
    echo "1. Ver Habitaciones\n";
    echo "2. Crear Reserva\n";
    echo "3. Mostrar Reservas\n";
    echo "4. Modificar Reserva\n";
    echo "5. Eliminar Reserva\n";
    echo "6. Ver mis datos\n";
    echo "7. Modificar mis datos\n";
    echo "8. Salir\n";
    echo "Seleccione una opción: ";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            verHabitaciones();
            break;
        case 2:
            crearReserva($usuario, $habitacionesGestor, $reservasGestor);// me esta cargando el dni como json
            break;
        case 3:
            mostrarReservas($reservasGestor, false, $usuario);
            break;
        case 4:
            modificarReserva($reservasGestor, $habitacionesGestor, false, $usuario);
            break;
        case 5:
            eliminarReserva($reservasGestor, $usuario);
            break;
        case 6:
            mostrarDatosUsuario($usuario);/// no anda ver! capaz esta mal invocado
            exit;
        case 7:
        modificarUsuario($usuario, false);
            exit;    
        case 8:
            echo "Saliendo del sistema...\n";
            exit;
        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }  menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);

}

function verHabitaciones(){
    $habitacionesGestor = new HabitacionControlador();
    $habitacionesGestor->cargarDesdeJSON();
    $habitaciones = $habitacionesGestor->obtenerHabitaciones();
    foreach ($habitaciones as $habitacion) {
        echo $habitacion . "\n";
    }
}

?>
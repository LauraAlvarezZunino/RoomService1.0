<?php

function menuUsuario()
{
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
function registrarse($usuariosGestor)
{
    echo "=== Registro de Usuario ===\n";
    echo "Ingrese el nombre y apellido del usuario: ";
    $nombreApellido = trim(fgets(STDIN));
    echo "Ingrese el DNI del usuario: ";
    $dni = trim(fgets(STDIN));
    if ($usuariosGestor->obtenerUsuarioPorDni($dni)) {
        echo "El DNI ingresado ya está registrado. Intente nuevamente con otro DNI.\n";
        menuUsuario();
        return;
    }
    echo "Ingrese el email del usuario: ";
    $email = trim(fgets(STDIN));
    echo "Ingrese el teléfono del usuario: ";
    $telefono = trim(fgets(STDIN));

    $usuariosGestor->crearUsuario($nombreApellido, $dni, $email, $telefono);
    echo "Usuario agregado exitosamente.\n";

    menuUsuario(); // Volver al menú principal
}

function menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor)
{
    echo "=== Menú Usuario Registrado ===\n";
    echo "1. Ver Habitaciones\n";
    echo "2. Crear Reserva\n";
    echo "3. Mostrar Reservas\n";
    echo "4. Modificar Reserva\n";
    echo "5. Eliminar Reserva\n";
    echo "6. Salir\n";
    echo "Seleccione una opción: ";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            verHabitaciones();
            break;
        case 2:
            crearReserva($usuario, $habitacionesGestor, $reservasGestor);
            break;
        case 6:
            echo "Saliendo del sistema...\n";
            exit;
        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }  menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);

}

function verHabitaciones()
{
    $habitacionesGestor = new HabitacionControlador();
    $habitacionesGestor->cargarDesdeJSON();
    $habitaciones = $habitacionesGestor->obtenerHabitaciones();
    foreach ($habitaciones as $habitacion) {
        echo $habitacion . "\n";
    }
}

function crearReserva($usuario, $habitacionesGestor, $reservasGestor)
{
    echo "Ingrese el tipo de habitación para la reserva: ";
    $tipoHabitacion = trim(fgets(STDIN));

    $habitacionesDisponibles = $habitacionesGestor->buscarPorTipo($tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        $habitacion = $habitacionesDisponibles[0];

        echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
        $fechaInicio = trim(fgets(STDIN));
        echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
        $fechaFin = trim(fgets(STDIN));

        $reservaId = $reservasGestor->generarNuevoId();
        $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacion, 0, $usuario);
        $reserva->calcularCosto($habitacion->getPrecio());

        $reservasGestor->agregarReserva($reserva);
        echo "Reserva creada exitosamente.\n";
    } else {
        echo "No se encontró una habitación disponible de ese tipo.\n";
    }
}
?>

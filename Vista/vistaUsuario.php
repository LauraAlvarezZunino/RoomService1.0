<?php
$dniGuardado = null; // Definir una variable global al inicio del archivo
function menuUsuario()
{
    global $dniGuardado;  // Hacer uso de la variable global
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
        case 3:
            mostrarReservasUsuario($usuario, $reservasGestor);
            break;
        case 4:
            modificarReservaUsuario($usuario, $reservasGestor, $habitacionesGestor);
            break;
        case 5:
            eliminarReservaUsuario($usuario, $reservasGestor, $habitacionesGestor);
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
    global $dniGuardado; // Hacer uso de la variable global

    // Usa $dniGuardado en lugar de $usuario->getDni() si es necesario
    echo "Ingrese el tipo de habitación para la reserva (simple / doble): ";
    $tipoHabitacion = trim(fgets(STDIN));

    $habitacionesDisponibles = $habitacionesGestor->buscarPorTipo($tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        //$habitacion = $habitacionesDisponibles[0];
        // Mostrar las habitaciones disponibles
        echo "Habitaciones disponibles:\n";
        foreach ($habitacionesDisponibles as $index => $habitacion) {
            echo "Número: " . $habitacion->getNumero() . " - Precio por noche: " . $habitacion->getPrecio() . "\n"; // Ajusta según tu método de obtener el nombre
        }

        echo "Seleccione una habitación (número): ";
        $eleccionHabitacion = trim(fgets(STDIN));

        echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
        $fechaInicio = trim(fgets(STDIN));
        echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
        $fechaFin = trim(fgets(STDIN));

        $costo = calcularCostoReserva($fechaInicio, $fechaFin, $habitacion->getPrecio());
        $reservaId = $reservasGestor->generarNuevoId();
        $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacion, $costo, $dniGuardado);
        //$reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacion, 0, $usuario);
        //$reserva->calcularCosto($habitacion->getPrecio());

        $reservasGestor->agregarReserva($reserva);
    } else {
        echo "No se encontró una habitación disponible de ese tipo.\n";
    }
}

function calcularCostoReserva($fechaInicio, $fechaFin, $precioPorNoche) {
    $fechaInicio = new DateTime($fechaInicio);
    $fechaFin = new DateTime($fechaFin);
    $diferencia = $fechaInicio->diff($fechaFin);
    return $diferencia->days * $precioPorNoche;
}
    
//NUEVO LUZ*
    function mostrarReservasUsuario($usuario, $reservasGestor)
{
    global $dniGuardado;
    $reservas = $reservasGestor->obtenerReservas();
    $tieneReservas = false;

    foreach ($reservas as $reserva) {
        if ($reserva->getUsuarioDni() === $dniGuardado) {
            echo "-------------------------\n";
            echo "ID: " . $reserva->getId() . "\n";
            echo "Fecha Inicio: " . $reserva->getFechaInicio() . "\n";
            echo "Fecha Fin: " . $reserva->getFechaFin() . "\n";
            echo "Habitación: " . $reserva->getHabitacion()->getNumero() . " (" . $reserva->getHabitacion()->getTipo() . ")\n";
            echo "Costo Total: \$" . $reserva->getCosto() . "\n";
            echo "-------------------------\n";
            $tieneReservas = true;
        }
    }

    if (!$tieneReservas) {
        echo "No tienes reservas registradas.\n";
    }
}

function modificarReservaUsuario($usuario, $reservasGestor, $habitacionesGestor)
{
    global $dniGuardado;
    echo "Ingrese el ID de la reserva que desea modificar: ";
    $id = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($id);

    if (!$reserva || $reserva->getUsuarioDni() !== $dniGuardado) {
        echo "Reserva no encontrada o no pertenece a este usuario.\n";
        return;
    }

    echo "Modificando Reserva ID: {$reserva->getId()}\n";
    echo "Fecha Inicio actual: " . $reserva->getFechaInicio() . "\n";
    echo "Fecha Fin actual: " . $reserva->getFechaFin() . "\n";
    echo "Habitación actual: " . $reserva->getHabitacion()->getNumero() . "\n";
    echo "Costo actual: $" . $reserva->getCosto() . "\n";

    echo "Ingrese la nueva fecha de inicio (YYYY-MM-DD) o deje vacío para mantener la actual: ";
    $nuevaFechaInicio = trim(fgets(STDIN));
    $nuevaFechaInicio = $nuevaFechaInicio ?: $reserva->getFechaInicio();

    echo "Ingrese la nueva fecha de fin (YYYY-MM-DD) o deje vacío para mantener la actual: ";
    $nuevaFechaFin = trim(fgets(STDIN));
    $nuevaFechaFin = $nuevaFechaFin ?: $reserva->getFechaFin();

    echo "Ingrese el nuevo número de habitación o deje vacío para mantener la actual: ";
    $nuevoNumeroHabitacion = trim(fgets(STDIN));
    if ($nuevoNumeroHabitacion) {
        $nuevaHabitacion = $habitacionesGestor->buscarHabitacionPorNumero($nuevoNumeroHabitacion);
        if (!$nuevaHabitacion) {
            echo "Habitación no encontrada.\n";
            return;
        }
    } else {
        $nuevaHabitacion = $reserva->getHabitacion();
    }

    // Calcular nuevo costo
    $reserva->setFechaInicio($nuevaFechaInicio);
    $reserva->setFechaFin($nuevaFechaFin);
    $reserva->setHabitacion($nuevaHabitacion);
    $reserva->calcularCosto($nuevaHabitacion->getPrecio());

    $reservasGestor->guardarEnJSON();
    echo "Reserva actualizada correctamente.\n";
}

function eliminarReservaUsuario($usuario, $reservasGestor, $habitacionesGestor)
{
    echo "Ingrese el ID de la reserva que desea eliminar: ";
    $idEliminar = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($idEliminar);

    if (!$reserva || $reserva->getUsuarioDni() !== $usuario->getDni()) {
        echo "Reserva no encontrada o no pertenece a este usuario.\n";
        return;
    }

    $reservasGestor->eliminarReserva($idEliminar);
}

?>

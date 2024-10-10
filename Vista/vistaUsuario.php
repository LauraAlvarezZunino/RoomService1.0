
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
            crearReserva($usuario, $habitacionesGestor, $reservasGestor);
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
            mostrarDatosUsuario($usuario);
            exit;
        case 7:
            modificarUsuario($reservasGestor, $usuario);
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
function solicitarTipoHabitacion()
{
    echo "Ingrese el tipo de habitación para la reserva (simple / doble): ";
    return trim(fgets(STDIN));
}

function mostrarHabitacionesDisponibles($habitaciones)
{
    echo "Habitaciones disponibles:\n";
    foreach ($habitaciones as $index => $habitacion) {
        echo $index . ". Número: " . $habitacion->getNumero() . " - Precio por noche: " . $habitacion->getPrecio() . "\n";
    }
}

function seleccionarHabitacion($habitaciones)
{
    echo "Seleccione una habitación (número): ";
    $eleccionHabitacion = trim(fgets(STDIN));

    foreach ($habitaciones as $habitacion) {
        if ($habitacion->getNumero() == $eleccionHabitacion) {
            return $habitacion;
        }
    }
    echo "No se encontró una habitación con ese número.\n";
    return null;
}

function solicitarFechasReserva()
{
    $fechaValida = false;
    while (!$fechaValida) {
        echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
        $fechaInicio = trim(fgets(STDIN));
        $fechaActual = date('Y-m-d');

        if (strtotime($fechaInicio) > strtotime($fechaActual)) {
            $fechaValida = true;
        } else {
            echo "La fecha de inicio debe ser posterior a la fecha actual. Por favor, ingrese una fecha válida.\n";
        }
    }

    echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
    $fechaFin = trim(fgets(STDIN));

    return [$fechaInicio, $fechaFin];
}

function crearReserva($dniGuardado,$habitacionesGestor, $reservasGestor)
{
   // global $dniGuardado;

    $tipoHabitacion = solicitarTipoHabitacion();
    $habitacionesDisponibles = $habitacionesGestor->buscarPorTipo($tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        mostrarHabitacionesDisponibles($habitacionesDisponibles);
        $habitacionSeleccionada = seleccionarHabitacion($habitacionesDisponibles);

        if ($habitacionSeleccionada) {
            list($fechaInicio, $fechaFin) = solicitarFechasReserva();
            $costo = calcularCostoReserva($fechaInicio, $fechaFin, $habitacionSeleccionada->getPrecio());
            $reservaId = $reservasGestor->generarNuevoId();
            $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacionSeleccionada, $costo, $dniGuardado);

            $reservasGestor->agregarReserva($reserva);
            echo "Reserva creada con éxito.\n";
        }
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
    

function mostrarReservasUsuario($usuario, $reservasGestor){
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
function modificarReservaUsuario($usuario,$reservasGestor, $habitacionesGestor)
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

    // Calcular el nuevo costo utilizando la función calcularCostoReserva()
    $nuevoCosto = calcularCostoReserva($nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion->getPrecio());

    // Actualizar la reserva con los nuevos valores
    $reserva->setFechaInicio($nuevaFechaInicio);
    $reserva->setFechaFin($nuevaFechaFin);
    $reserva->setHabitacion($nuevaHabitacion);
    $reserva->setCosto($nuevoCosto);

    $reservasGestor->guardarEnJSON();
    echo "Reserva actualizada correctamente. Nuevo costo: $" . $nuevoCosto . "\n";
}


function eliminarReservaUsuario($usuario, $reservasGestor)
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
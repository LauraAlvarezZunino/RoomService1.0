<?php
function crearReserva($dniGuardado,$habitacionesGestor, $reservasGestor)
{
   global $dniGuardado;

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
function solicitarTipoHabitacion()
{
    echo "Ingrese el tipo de habitación para la reserva (simple / doble): ";
    return trim(fgets(STDIN));
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


//usuario
function mostrarDatosUsuario($usuariosGestor)
{
    global $dniGuardado;

    // Obtener la lista completa de usuarios
    $usuarios = $usuariosGestor->obtenerUsuarioporDni();///  VEEEEEEEEEEEEEEEEEEEEEEERRRRRR

    // Buscar al usuario con el DNI guardado
    foreach ($usuarios as $usuario) {
        if ($usuario->getDni() === $dniGuardado) {
            echo "-------------------------\n";
            echo "DNI: " . $usuario->getDni() . "\n";
            echo "Nombre: " . $usuario->getNombre() . "\n";
            echo "Correo electrónico: " . $usuario->getEmail() . "\n";
            echo "Teléfono: " . $usuario->getTelefono() . "\n";
            echo "Dirección: " . $usuario->getDireccion() . "\n";
            echo "-------------------------\n";
            return; // Salimos del bucle después de encontrar y mostrar al usuario
        }
    }

    // Si no se encuentra el usuario, mostramos un mensaje
    echo "No se encontraron datos para el usuario con el DNI proporcionado.\n";
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
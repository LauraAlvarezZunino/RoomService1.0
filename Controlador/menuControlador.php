<?php
//Reservas
function modificarReserva($reservasGestor, $habitacionesGestor, $esAdmin = false, $usuario = null)
{
    global $dniGuardado;
    echo "Ingrese el ID de la reserva que desea modificar: ";
    $id = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($id);

    // Verificar si la reserva existe y si el usuario es el dueño, a menos que sea un administrador
    if (!$reserva || (!$esAdmin && $reserva->getUsuarioDni() !== $dniGuardado)) {
        echo "Reserva no encontrada o no tiene permisos para modificar esta reserva.\n";
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

 
    $nuevoCosto = calcularCostoReserva($nuevaFechaInicio, $nuevaFechaFin, $nuevaHabitacion->getPrecio());

    // Actualizar la reserva con los nuevos valores
    $reserva->setFechaInicio($nuevaFechaInicio);
    $reserva->setFechaFin($nuevaFechaFin);
    $reserva->setHabitacion($nuevaHabitacion);
    $reserva->setCosto($nuevoCosto);

    $reservasGestor->guardarEnJSON();
    echo "Reserva actualizada correctamente. Nuevo costo: $" . $nuevoCosto . "\n";
}



function mostrarReservas($reservasGestor, $esAdmin = false, $usuario = null)
{
    global $dniGuardado;
    $reservas = $reservasGestor->obtenerReservas();
    $tieneReservas = false;

    foreach ($reservas as $reserva) {
        // Si no es administrador, mostramos solo las reservas del usuario actual
        if ($esAdmin || ($usuario && $reserva->getUsuarioDni() === $dniGuardado)) {
            echo "-------------------------\n";
            echo "ID: " . $reserva->getId() . "\n";
            echo "Fecha Inicio: " . $reserva->getFechaInicio() . "\n";
            echo "Fecha Fin: " . $reserva->getFechaFin() . "\n";
            echo "Habitación: " . $reserva->getHabitacion()->getNumero() . " (" . $reserva->getHabitacion()->getTipo() . ")\n";
            echo "Costo Total: \$" . $reserva->getCosto() . "\n";
            echo "Usuario DNI: " . $reserva->getUsuarioDni() . "\n";
            echo "-------------------------\n";
            $tieneReservas = true;
        }
    }

    if (!$tieneReservas) {
        echo $esAdmin ? "No hay reservas registradas.\n" : "No tienes reservas registradas.\n";
    }
}

function eliminarReserva($reservasGestor, $usuario = null, $esAdmin = false)
{
    echo "Ingrese el ID de la reserva que desea eliminar: ";
    $idEliminar = trim(fgets(STDIN));
    $reserva = $reservasGestor->buscarReservaPorId($idEliminar);

    // Si no es administrador, verificamos que la reserva pertenezca al usuario
    if (!$reserva || (!$esAdmin && $reserva->getUsuarioDni() !== $usuario->getDni())) {
        echo "Reserva no encontrada o no pertenece a este usuario.\n";
        return;
    }

    $reservasGestor->eliminarReserva($idEliminar);
    echo "Reserva eliminada con éxito.\n";
}


//Usuarios
function mostrarDatosUsuario($usuariosGestor)
{
    global $dniGuardado;

    // Obtener la lista completa de usuarios
    $usuarios = $usuariosGestor->obtenerUsuarios();

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




function modificarUsuario($usuariosGestor, $esAdministrador = false)
{
    global $dniGuardado;

    if ($esAdministrador) {
        echo 'Ingrese el ID del usuario que quiere modificar: ';
        $id = trim(fgets(STDIN));
    } else {
        // Si no es administrador, buscar el usuario por el DNI global
        $usuario = $usuariosGestor->obtenerUsuarioPorDni($dniGuardado);
        if (!$usuario) {
            echo "Usuario no encontrado o no autorizado.\n";
            return false;
        }
        $id = $usuario->getId();
    }

    $usuario = $usuariosGestor->obtenerUsuarioPorId($id);

    if (!$usuario) {
        echo "Usuario no encontrado.\n";
        return false;
    }

    echo "Modificando al usuario con ID: {$usuario->getId()}\n";
    echo "Nombre actual: " . $usuario->getNombreApellido() . "\n";
    echo "DNI actual: " . $usuario->getDni() . "\n";
    echo "Email actual: " . $usuario->getEmail() . "\n";
    echo "Teléfono actual: " . $usuario->getTelefono() . "\n";

    echo "Introduce el nuevo nombre (deja vacío para mantener el actual): ";
    $nombreApellido = trim(fgets(STDIN)); 

    echo "Introduce el nuevo DNI (deja vacío para mantener el actual): ";
    $dni = trim(fgets(STDIN));

    echo "Introduce el nuevo email (deja vacío para mantener el actual): ";
    $email = trim(fgets(STDIN));

    echo "Introduce el nuevo teléfono (deja vacío para mantener el actual): ";
    $telefono = trim(fgets(STDIN));

    $nuevosDatos = [
        'nombre' => $nombreApellido ?: null,  
        'dni' => $dni ?: null,
        'email' => $email ?: null,
        'telefono' => $telefono ?: null,
    ];

    if ($usuariosGestor->actualizarUsuario($id, $nuevosDatos)) {
        echo "Usuario actualizado correctamente.\n";
    } else {
        echo "No se pudo actualizar el usuario.\n";
    }
}

//habitaciones

//function verHabitaciones(){
  //  $habitacionesGestor = new HabitacionControlador();
    //$habitacionesGestor->cargarDesdeJSON();
    //$habitaciones = $habitacionesGestor->obtenerHabitaciones();
    //foreach ($habitaciones as $habitacion) {
      //  echo $habitacion . "\n";
    //}
//}

function mostrarHabitacionesDisponibles($habitaciones)
{
    echo "Habitaciones disponibles:\n";
    foreach ($habitaciones as $index => $habitacion) {
        echo $index . ". Número: " . $habitacion->getNumero() . " - Precio por noche: " . $habitacion->getPrecio() . "\n";
    }
}
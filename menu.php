
<?php
//cliente
// preguntarle si es nuevo o si ya esta registrado
// si esta registrado ingresar con dni


include 'usuarios.php';
include_once 'habitaciones.php';
include 'reserva.php';
include 'reservasGestor.php';





while (true) {
    $clave = 111;
    echo "===Bienvenido===\n";
    echo "1. Administrador\n";
    echo "2. Usuario\n";
    echo "3. Salir\n";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            // admin pido la clave
            echo "Ingrese la clave: ";
            $claveAdmin = trim(fgets(STDIN));
            if ($clave == $claveAdmin) {
                menuAdmin();
            } else
                echo "Clave Erronea.\n";

            break;

        case 2:
            menuUsuario();
            break;
        case 3:
            echo "Saliendo del sistema...\n";
            exit;

        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }
}

function menuUsuario()
{
    $usuariosGestor = new Usuarios(); // Clase para manejar usuarios
    $habitacionesGestor = new Habitacion(); // Clase para manejar habitaciones
    $reservasGestor = new ReservasGestor(); // Clase para manejar reservas

    $habitacionesGestor->cargarDesdeJSON();


    echo "=== Menú Usuario ===\n";
    echo "1. Registrarme\n"; //separarlo de lo demas y dsp llamar a menu usuario
    echo "2. Soy Usuario\n";
    echo "3. Ver Habitaciones\n";
    echo "4. Crear Reserva\n";
    echo "5. Mostrar Reservas\n";
    echo "6. Modificar Reserva\n";
    echo "7. Eliminar Reserva\n";
    echo "8. Salir\n";
    echo "Seleccione una opción: ";

    $opcion = trim(fgets(STDIN));

    switch ($opcion) {
        case 1:
            // Agregar Usuario
            echo "Ingrese el nombre y apellido del usuario: ";
            $nombreApellido = trim(fgets(STDIN));
            echo "Ingrese el DNI del usuario: ";
            $dni = trim(fgets(STDIN));
            echo "Ingrese el email del usuario: ";
            $email = trim(fgets(STDIN));
            echo "Ingrese el teléfono del usuario: ";
            $telefono = trim(fgets(STDIN));
            $usuariosGestor->crearUsuario($nombreApellido, $dni, $email, $telefono);
            echo "Usuario agregado exitosamente.\n";
            break;
        case 2:
            //soy usuario
            break;
        case 3:
            $habitaciones = $habitacionesGestor->obtenerHabitaciones(); //ver habs
            foreach ($habitaciones as $habitacion) {
                echo $habitacion . "\n";
            }
            break;

        case 4:
            // Crear Reserva
            echo "Ingrese el ID del usuario: ";
            $usuarioId = trim(fgets(STDIN));
            $usuario = $usuariosGestor->obtenerUsuarioPorId($usuarioId);

            if ($usuario) {
                echo "Ingrese el tipo de habitación para la reserva: ";
                $tipoHabitacion = trim(fgets(STDIN));

                // Buscar habitaciones disponibles del tipo indicado
                $habitacionesDisponibles = $habitacionesGestor->buscarPorDisponibilidad($tipoHabitacion);

                if (!empty($habitacionesDisponibles)) {
                    // Si hay habitaciones disponibles,la primera
                    $habitacion = $habitacionesDisponibles[0];

                    echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
                    $fechaInicio = trim(fgets(STDIN));
                    echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
                    $fechaFin = trim(fgets(STDIN));

                    $reservaId = $reservasGestor->generarNuevoId();

                    // Crear la reserva
                    $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, 'Reservado', 0);

                    // Asignar la habitación a la reserva
                    $reserva->setHabitacion($habitacion);

                    // Calcular el costo de la reserva en función del precio de la habitación
                    $reserva->calcularCosto($habitacion->getPrecio());

                    // Agregar la reserva al gestor de reservas
                    $reservasGestor->agregarReserva($reserva);
                    //verrrr que tenemos que cambiar ahora la disponibilidaad de la habitacion !!!
                    echo "Reserva creada exitosamente.\n";
                } else {
                    echo "No se encontró una habitación disponible de ese tipo.\n";
                }
            } else {
                echo "Usuario no encontrado.\n";
            }

            break;
        case 5: //mostrar reserva  buscarReservaPorId($id)
            break;
        case 6: //modificar reserva  
            break;
        case 7: //eliminar reserva eliminarReserva($id)
            break;
        case 8:
            echo "Saliendo del sistema...\n";
            exit;

        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }
}

function menuAdmin()
{
    while (true) {
        echo "=== Menú Principal ===\n";
        echo "1. Administrar Usuarios\n";
        echo "2. Administrar Habitaciones\n";
        echo "3. Administrar Reservas\n";
        echo "4. Salir\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                menuUsuarios();
                break;
            case 2:
                menuHabitaciones();
                break;
            case 3:
                menuReservas();
                break;
            case 4:
                echo "Saliendo del sistema...\n";
                exit;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function menuUsuarios()
{
    $usuariosGestor = new Usuarios(); // Clase para manejar usuarios
    while (true) {
        echo "=== Menú de Usuarios ===\n";
        echo "1. Mostrar Usuarios\n";
        echo "2. Modificar Usuario\n";
        echo "3. Eliminar Usuario\n";
        echo "4. Volver al Menú Principal\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                $usuarios = $usuariosGestor->obtenerUsuarios();
                foreach ($usuarios as $usuario) {
                    echo $usuario . "\n";
                }
                break;
            case 2:

                echo 'Ingrese el ID del usuario que quiere modificar: ';
                $id= trim(fgets(STDIN));
                $usuario= $usuariosGestor->obtenerUsuarioPorId($id);

                if (!$usuario) {
                    echo "Usuario no encontrado.\n";
                    return false;
                }
            
                // Mostrar información actual del usuario
                echo "Modificando al usuario con ID: {$usuario->getId()}\n";
                echo "Nombre actual: " . $usuario->getNombreApellido() . "\n";
                echo "DNI actual: " . $usuario->getDni() . "\n";
                echo "Email actual: " . $usuario->getEmail() . "\n";
                echo "Teléfono actual: " . $usuario->getTelefono() . "\n";
            
                // Pedir nuevos datos (o mantener los actuales si no se ingresan)
                echo "Introduce el nuevo nombre (deja vacío para mantener el actual): ";
                $nombreApellido = trim(fgets(STDIN)); // Capturar entrada del usuario
            
                echo "Introduce el nuevo DNI (deja vacío para mantener el actual): ";
                $dni = trim(fgets(STDIN));
            
                echo "Introduce el nuevo email (deja vacío para mantener el actual): ";
                $email = trim(fgets(STDIN));
            
                echo "Introduce el nuevo teléfono (deja vacío para mantener el actual): ";
                $telefono = trim(fgets(STDIN));
            
                // Preparar array de nuevos datos
                $nuevosDatos = [
                    'nombre' => $nombreApellido ?: null,  // Si está vacío, se deja null
                    'dni' => $dni ?: null,
                    'email' => $email ?: null,
                    'telefono' => $telefono ?: null,
                ];
            
                // Llamar a la función para actualizar el usuario
                if ($usuariosGestor->actualizarUsuario($id, $nuevosDatos)) {
                    echo "Usuario actualizado correctamente.\n";
                } else {
                    echo "No se pudo actualizar el usuario.\n";
                }
            
                break;
            case 3:
                echo 'Ingrese el ID a eliminar: ';
                $idEliminado= trim(fgets(STDIN));
                $usuariosGestor->eliminarUsuario($idEliminado);
                //  eliminar usuario
                echo "Usuario {$idEliminado} eliminado correctamente.\n";
                break;
            case 4:
                return; // Vuelve al menú principal
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function menuHabitaciones()
{
    $habitacionesGestor = new Habitacion();
    $habitacionesGestor->cargarDesdeJSON();

    while (true) {
        echo "=== Menú de Habitaciones ===\n";
        echo "1. Mostrar Habitaciones\n";
        echo "2. Agregar Habitación\n";
        echo "3. Modificar Habitación\n";
        echo "4. Eliminar Habitación\n";
        echo "5. Volver al Menú Principal\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1: //mostrar habs
                $habitaciones = $habitacionesGestor->obtenerHabitaciones();
                foreach ($habitaciones as $habitacion) {
                    echo $habitacion . "\n";
                }
                break;
            case 2:
                // agregar una habitacion
                echo "Ingrese el número de la habitación: ";
                $numero = trim(fgets(STDIN));
                echo "Ingrese el tipo de habitación: ";
                $tipo = trim(fgets(STDIN));
                echo "Ingrese el precio por noche: ";
                $precio = trim(fgets(STDIN));
                $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio, true));
                echo "Habitación agregada exitosamente.\n";
                break;
            case 3:
                //  modificar habitacion
                break;
            case 4:
                // eliminar habitacion
                break;
            case 5:
                return;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function menuReservas()
{
    $reservasGestor = new ReservasGestor();

    while (true) {
        echo "=== Menú de Reservas ===\n";
        echo "1. Mostrar Reservas\n";
        echo "2. Volver al Menú Principal\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                $reservas = $reservasGestor->obtenerReservas();
                foreach ($reservas as $reserva) {
                    echo $reserva . "\n";
                }
                break;
            case 2:
                return;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}

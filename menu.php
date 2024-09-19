
<?php


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

            // Buscar usuario por DNI
            $usuario = $usuariosGestor->obtenerUsuarioPorDni($dni);

            if ($usuario) {
                menuUsuarioRegistrado($usuario, $usuariosGestor, $habitacionesGestor, $reservasGestor);
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

// Función separada para registrar al usuario
function registrarse($usuariosGestor)
{
    echo "=== Registro de Usuario ===\n";
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

    menuUsuario(); // Volver al menú principal
}

function menuUsuarioRegistrado($usuario,$usuariosGestor, $habitacionesGestor, $reservasGestor)
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
            verHabitaciones();// falta que muestre cuando esta ocupado
            break;
        case 2:
            crearReserva($usuario,$usuariosGestor, $habitacionesGestor, $reservasGestor);
            break;
        case 3:
            // Mostrar reservas
            break;
        case 4:
            // Modificar reserva
            break;
        case 5:
            // Eliminar reserva
            break;
        case 6:
            echo "Saliendo del sistema...\n";
            exit;
        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            menuUsuarioRegistrado($usuario,$usuariosGestor, $habitacionesGestor, $reservasGestor);
            break;
    }
}

// Función para ver habitaciones .....
function verHabitaciones(){

$habitacionesGestor = new Habitacion();
$habitacionesGestor->cargarDesdeJSON();
$habitaciones = $habitacionesGestor->obtenerHabitaciones();
foreach ($habitaciones as $habitacion) {
    echo $habitacion . "\n";
}
}

{
    $habitaciones = $habitacionesGestor->obtenerHabitaciones(); // Obtener habitaciones

        echo "=== Habitaciones Disponibles ===\n";
foreach ($habitaciones as $habitacion) {
        echo $habitacion . "\n";
    }
} $habitaciones = $habitacionesGestor->obtenerHabitaciones();
foreach ($habitaciones as $habitacion) {
    echo $habitacion . "\n";
}

// Función para crear reserva
function crearReserva($usuariosGestor, $habitacionesGestor, $reservasGestor)
{
    // Paso 1: Pedir DNI del usuario
    echo "Ingrese su DNI para realizar la reserva: ";
    $dniUsuario = trim(fgets(STDIN));

    // Paso 2: Buscar el usuario por su DNI
    $usuario = $usuariosGestor->buscarUsuarioPorDni($dniUsuario);
    if (!$usuario) {
        echo "No se encontró un usuario con ese DNI.\n";
        return; // Salir si no se encuentra el usuario
    }

    // Paso 3: Continuar con el proceso de reserva
    echo "Ingrese el tipo de habitación para la reserva: ";
    $tipoHabitacion = trim(fgets(STDIN));

    // Buscar habitaciones disponibles del tipo indicado
    $habitacionesDisponibles = $habitacionesGestor->buscarPorDisponibilidad($tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        // Si hay habitaciones disponibles, usar la primera
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

        // Asignar el DNI del usuario a la reserva
        $reserva->setUsuarioDni($dniUsuario);

        // Calcular el costo de la reserva en función del precio de la habitación
        $reserva->calcularCosto($habitacion->getPrecio());

        // Agregar la reserva al gestor de reservas
        $reservasGestor->agregarReserva($reserva);

        // Cambiar la disponibilidad de la habitación
        $habitacion->setDisponible("Reservado");

        echo "Reserva creada exitosamente.\n";
    } else {
        echo "No se encontró una habitación disponible de ese tipo.\n";
    }

    // Volver al menú de usuario registrado
    menuUsuarioRegistrado($usuario,$usuariosGestor, $habitacionesGestor, $reservasGestor);
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
                menuAdminUsuarios();
                break;
            case 2:
                menuAdminHabitaciones();
                break;
            case 3:
                menuAdminReservas();
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


function menuAdminUsuarios()
{
    $usuariosGestor = new Usuarios(); // Clase para manejar usuarios
    while (true) {
        echo "=== Menú Administrar Usuarios ===\n";
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
            
                //  array de nuevos datos
                $nuevosDatos = [
                    'nombre' => $nombreApellido ?: null,  // Si está vacío, se deja null
                    'dni' => $dni ?: null,
                    'email' => $email ?: null,
                    'telefono' => $telefono ?: null,
                ];
            
                // llama a la función para actualizar el usuario
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
                echo "Usuario {$idEliminado} eliminado correctamente.\n";
                break;
            case 4:
                return;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function menuAdminHabitaciones()
{
    $habitacionesGestor = new Habitacion();
    $habitacionesGestor->cargarDesdeJSON();

    while (true) {
        echo "=== Menú Administrar Habitaciones ===\n";
        echo "1. Mostrar Habitaciones\n";
        echo "2. Agregar Habitación\n";
        echo "3. Modificar Habitación\n";
        echo "4. Eliminar Habitación\n";
        echo "5. Volver al Menú Principal\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1: //mostrar habs
                verHabitaciones();
                break;
            case 2:
                // agregar una habitacion
                echo "Ingrese el número de la habitación: ";
                $numero = trim(fgets(STDIN));
                echo "Ingrese el tipo de habitación: ";
                $tipo = trim(fgets(STDIN));
                echo "Ingrese el precio por noche: ";
                $precio = trim(fgets(STDIN));
                $disponibilidad= "Disponible";
                $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio, $disponibilidad));
                echo "Habitación agregada exitosamente.\n";
                break;
            case 3:
                echo "Ingrese el número de la habitación que desea modificar: ";
                $numero = trim(fgets(STDIN));
                
                // Verificar si la habitación existe
                $habitacion = null;
                foreach ($habitacionesGestor->obtenerHabitaciones() as $h) {
                    if ($h->getNumero() == $numero) {
                        $habitacion = $h;
                        break;
                    }
                }
                
                if ($habitacion) {
                    echo "Modificando habitación número: $numero\n";
                    
                    echo "Ingrese el nuevo tipo de habitación (deje vacío para mantener el actual: {$habitacion->getTipo()}): ";
                    $nuevoTipo = trim(fgets(STDIN));
                    
                    echo "Ingrese el nuevo precio (deje vacío para mantener el actual: {$habitacion->getPrecio()}): ";
                    $nuevoPrecio = trim(fgets(STDIN));
                    
                    echo "¿Está disponible? (1 para Sí, 0 para No, deje vacío para mantener el actual): ";
                    $nuevaDisponibilidad = trim(fgets(STDIN));

                    // Actualizar los datos de la habitación
                    $nuevosDatos = [
                        'tipo' => $nuevoTipo ?: $habitacion->getTipo(),
                        'precio' => $nuevoPrecio ?: $habitacion->getPrecio(),
                        'disponibilidad' => $nuevaDisponibilidad !== '' ? (bool) $nuevaDisponibilidad : $habitacion->getDisponibilidad(),
                    ];

                    if ($habitacionesGestor->actualizarHabitacion($numero, $nuevosDatos)) {
                        echo "Habitación actualizada correctamente.\n";
                    } else {
                        echo "Error al actualizar la habitación.\n";
                    }
                } else {
                    echo "Habitación no encontrada.\n";
                }
                break;
            case 4:
                echo "Ingrese el número de la habitación que desea eliminar: ";
                $numero = trim(fgets(STDIN));
                if ($habitacionesGestor->eliminarHabitacion($numero)) {
                    echo "Habitación eliminada correctamente.\n";
                } else {
                    echo "Error al eliminar la habitación.\n";
                }
                break;
            case 5:
                return;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function menuAdminReservas()

{
    $reservasGestor = new ReservasGestor();

    while (true) {
        echo "=== Menú Administrar Reservas ===\n";
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

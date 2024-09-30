
<?php


include_once 'usuarios.php';
include_once 'habitacionesGestor.php';
include_once 'reserva.php';
include_once 'reservasGestor.php';

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
    $habitacionesGestor = new HabitacionGestor(); // Clase para manejar habitaciones
    $reservasGestor = new ReservasGestor($habitacionesGestor); // Clase para manejar reservas

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
                menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);
            } else {
                echo "DNI no encontrado. Inténtelo de nuevo.\n";
                menuUsuario($usuario);
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
            verHabitaciones();// falta que muestre cuando esta ocupado
            break;
        case 2:
            crearReserva($usuario, $habitacionesGestor, $reservasGestor);
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
            menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);
            break;
    }
}

function verHabitaciones(){

$habitacionesGestor = new HabitacionGestor();
$habitacionesGestor->cargarDesdeJSON();
$habitaciones = $habitacionesGestor->obtenerHabitaciones();
foreach ($habitaciones as $habitacion) {

    echo $habitacion . "\n";
}
}



function crearReserva($usuario,$habitacionesGestor, $reservasGestor)
{
    echo "Ingrese el tipo de habitación para la reserva: ";
    $tipoHabitacion = trim(fgets(STDIN));
    $disponibilidad= "Disponible";
    $habitacionesDisponibles = $habitacionesGestor->buscarPorDisponibilidadYTipo($disponibilidad,$tipoHabitacion);

    if (!empty($habitacionesDisponibles)) {
        // Si hay habitaciones disponibles, usar la primera
        $habitacion = $habitacionesDisponibles[0];

        echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
        $fechaInicio = trim(fgets(STDIN));
        echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
        $fechaFin = trim(fgets(STDIN));

        $reservaId = $reservasGestor->generarNuevoId();
        $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, $habitacion,0,$usuario);
        $reserva->setHabitacion($habitacion);
        $usuario->getDni();
        $reserva->setUsuarioDni($usuario);
        $reserva->calcularCosto($habitacion->getPrecio());

      
        $reservasGestor->agregarReserva($reserva);

        $habitacion->setDisponibilidad("Reservada");
     
        echo "Reserva creada exitosamente.\n";
    } else {
        echo "No se encontró una habitación disponible de ese tipo.\n";
    }

    menuUsuarioRegistrado($usuario, $habitacionesGestor, $reservasGestor);
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
    $usuariosGestor = new Usuarios(); 
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
    $habitacionesGestor = new HabitacionGestor();
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
            case 1: 
                verHabitaciones();
                break;
            case 2:
           
                echo "Ingrese el número de la habitación: ";
                $numero = trim(fgets(STDIN));
                foreach ($habitacionesGestor->obtenerHabitaciones() as $h) {
                    if ($h->getNumero() == $numero) {
                        echo "La habitación con el número $numero ya existe. No se puede duplicar.\n";
                        break 2;   //esto sale del for y vuelve al menu de adm habs
                    }
                }
                echo "Ingrese el tipo de habitación: ";
                $tipo = trim(fgets(STDIN));
                echo "Ingrese el precio por noche: ";
                $precio = trim(fgets(STDIN));
                $disponibilidad= "Disponible";
                $diasReservado=[];
                $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio, $disponibilidad,$diasReservado));
                echo "Habitación agregada exitosamente.\n";
                break;
            case 3:
                echo "Ingrese el número de la habitación que desea modificar: ";
                $numero = trim(fgets(STDIN));
                
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
                    
                    echo "¿Está disponible? (Deje vacío para mantener el actual): ";
                    $nuevaDisponibilidad = trim(fgets(STDIN));

                    $nuevosDatos = [
                        'tipo' => $nuevoTipo ?: $habitacion->getTipo(), //?:revisa si es null
                        'precio' => $nuevoPrecio ?: $habitacion->getPrecio(),
                        'disponibilidad' => $nuevaDisponibilidad ?: $habitacion->getDisponibilidad(),
                    ];

                    if ($habitacionesGestor->actualizarHabitacion($numero, $nuevosDatos)) {
                        echo "Habitación actualizada correctamente.\n";
                    } else {
                        echo "Error al actualizar la habitación.\n";
                   
                } 
                break;
                }
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
    $habitacionesGestor = new HabitacionGestor();
    $habitacionesGestor->cargarDesdeJSON();
    $reservasGestor = new ReservasGestor($habitacionesGestor);

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

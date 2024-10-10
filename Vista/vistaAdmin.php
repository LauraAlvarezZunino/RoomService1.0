<?php

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
    $usuariosGestor = new UsuarioControlador(); 
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
                mostrarUsuarios($usuariosGestor);
                break;
            case 2:
                modificarUsuario($usuariosGestor,true);
               /* echo 'Ingrese el ID del usuario que quiere modificar: ';
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
            */
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
    $habitacionesGestor = new HabitacionControlador();
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
               
              
                $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio));
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
                    

                    $nuevosDatos = [
                        'tipo' => $nuevoTipo ?: $habitacion->getTipo(), //?:revisa si es null
                        'precio' => $nuevoPrecio ?: $habitacion->getPrecio(),
                       
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
    $habitacionesGestor = new HabitacionControlador();
    $habitacionesGestor->cargarDesdeJSON();
    $reservasGestor = new ReservaControlador($habitacionesGestor);

    while (true) {
        echo "=== Menú Administrar Reservas ===\n";
        echo "1. Mostrar Reservas\n";
        echo "2. Modificar Reserva\n";
        echo "3. Eliminar Reserva\n";
        echo "4. Volver al Menú Principal\n";
        echo "Seleccione una opción: ";

        $opcion = trim(fgets(STDIN));

        switch ($opcion) {
            case 1:
                $reservas = $reservasGestor->obtenerReservas();
                foreach ($reservas as $reserva) {
                    echo $reserva . "\n";
                }
                break;
                case 2://modificar reserva
                break;    
                case 3:
                    //eliminar reserva
                    return;
                case 4:
                return;
            default:
                echo "Opción no válida. Inténtelo de nuevo.\n";
                break;
        }
    }
}


function mostrarUsuarios($usuariosGestor){
$usuarios = $usuariosGestor->obtenerUsuarios(); // aca podemos hacerla funcion y que solo la llame arriba 
    foreach ($usuarios as $usuario) {
        echo $usuario . "\n";
    }
}
?>

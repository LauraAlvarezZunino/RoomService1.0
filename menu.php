//aca ponemos las opciones que deberia tener el menu
// bienvenida y elegir entre cliente o administrativo
//cliente
// preguntarle si es nuevo o si ya esta registrado
// si esta registrado ingresar con dni
// si no esta registrado solicitar los datos para llenar el json
// una vez que ingreso preguntarle que tipo de habitación esta buscando
//simple
//familiar
//doble
// (acá creo que la estoy delirando mucho) una vez seleccionada consultar en que fecha quiere reservar
//se chequea disponibiliad si esta disponible se informa y se le pregunta si quiere confirmar la reserva por un valor X por X dias.
//si no esta disponible se le informa y se le pregunta si quiere elegir otra habitación u otra fecha
// una vez confirmada la reserva se le pregunta si quiere volver a realizar alguna operacion o si desea salir.
//Administrativo
//se le consulta sobre que seccion quiere operar
//habitaciones
//clientes
//reservas
//se le consulta que accion quiere realizar sobre la seccion
//mostrar
//modificar
//crear
//eliminar
//una vez elegida la accion se ejecuta y una vez realizada se pregunta:
//hacer más cambios (vuelve al menu habitacioens, clientes, reservas)
//salir del sistema
<?php

include 'usuarios.php';
include_once 'habitaciones.php';
include 'reserva.php';
include 'reservasGestor.php';

$usuariosGestor = new Usuarios(); // Clase para manejar usuarios
$habitacionesGestor = new Habitacion(); // Clase para manejar habitaciones
$reservasGestor = new ReservasGestor(); // Clase para manejar reservas

$habitacionesGestor->cargarDesdeJSON();




while (true) {
    echo "=== Menú Principal ===\n";
    echo "1. Agregar Usuario\n";
    echo "2. Mostrar Usuarios\n";
    echo "3. Agregar Habitación\n";
    echo "4. Mostrar Habitaciones\n";
    echo "5. Crear Reserva\n";
    echo "6. Mostrar Reservas\n";
    echo "7. Salir\n";
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
            // Mostrar Usuarios
            $usuarios = $usuariosGestor->obtenerUsuarios();
            foreach ($usuarios as $usuario) {
                echo $usuario . "\n";
            }
            break;

        case 3:
            // Agregar Habitación
            echo "Ingrese el número de la habitación: ";
            $numero = trim(fgets(STDIN));
            echo "Ingrese el tipo de habitación: ";
            $tipo = trim(fgets(STDIN));
            echo "Ingrese el precio por noche: ";
            $precio = trim(fgets(STDIN));
            $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio, true));
            echo "Habitación agregada exitosamente.\n";
            break;

        case 4:
            // Mostrar Habitaciones
            $habitaciones = $habitacionesGestor->obtenerHabitaciones();
            foreach ($habitaciones as $habitacion) {
                echo $habitacion . "\n";
            }
            break;

        case 5:
            // Crear Reserva
            echo "Ingrese el ID del usuario: ";
            $usuarioId = trim(fgets(STDIN));
            $usuario = $usuariosGestor->obtenerUsuarioPorId($usuarioId);

            if ($usuario) {
                echo "Ingrese el tipo de habitación para la reserva: ";
                $tipoHabitacion = trim(fgets(STDIN));
                $habitacion = $habitacionesGestor->buscarPorDisponibilidad($tipoHabitacion);

                if ($habitacion) {
                    echo "Ingrese la fecha de inicio (YYYY-MM-DD): ";
                    $fechaInicio = trim(fgets(STDIN));
                    echo "Ingrese la fecha de fin (YYYY-MM-DD): ";
                    $fechaFin = trim(fgets(STDIN));
                    $reservaId = $reservasGestor->generarNuevoId();
                    $reserva = new Reserva($reservaId, $fechaInicio, $fechaFin, 'Reservado', 0);
                    $reserva->setHabitacion($habitacion); // Asignar la habitación a la reserva
                    $reserva->calcularCosto($this->habitacion->getPrecio()); // Calcular costo basado en el precio de la habitación
                    $reservasgestor->agregarReserva($reserva);
                    echo "Reserva creada exitosamente.\n";
                } else {
                    echo "No se encontró una habitación disponible de ese tipo.\n";
                }
            } else {
                echo "Usuario no encontrado.\n";
            }
            break;

        case 6:
            // Mostrar Reservas
            $reservas = $reservasGestor->obtenerReservas();
            foreach ($reservas as $reserva) {
                echo $reserva . "\n";
            }
            break;

        case 7:
            // Salir
            echo "Saliendo del sistema...\n";
            exit;

        default:
            echo "Opción no válida. Inténtelo de nuevo.\n";
            break;
    }
}
?>
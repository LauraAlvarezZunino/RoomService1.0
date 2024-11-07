<?php

class VistaAdmin
{
    private $menuAdminControlador;

    public function __construct($habitacionesGestor, $usuariosGestor)
    {
        $this->menuAdminControlador = new menuAdminControlador($habitacionesGestor, $usuariosGestor);
    }
    public function menuAdmin()
    {
        while (true) {
            echo "=== Menú Principal ===\n";
            echo "1. Administrar Usuarios\n";
            echo "2. Administrar Habitaciones\n";
            echo "3. Administrar Reservas\n";
            echo "4. Salir\n";
            echo 'Seleccione una opción: ';

            $opcion = trim(fgets(STDIN));

            switch ($opcion) {
                case 1:
                    $this->menuAdminUsuarios();
                    break;
                case 2:
                    $this->menuAdminHabitaciones();
                    break;
                case 3:
                    $this->menuAdminReservas();
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

    private function menuAdminUsuarios()
    {
        //$usuariosGestor = new UsuarioControlador;
        while (true) {
            echo "=== Menú Administrar Usuarios ===\n";
            echo "1. Mostrar Usuarios\n";
            echo "2. Modificar Usuario\n";
            echo "3. Eliminar Usuario\n";
            echo "4. Volver al Menú Principal\n";
            echo 'Seleccione una opción: ';

            $opcion = trim(fgets(STDIN));

            switch ($opcion) {
                case 1:
                    $this->menuAdminControlador->mostrarUsuarios();
                    break;
                case 2:
                    modificarUsuario(true);
                    break;
                case 3:
                    $this->menuAdminControlador->eliminarUsuario();
                    break;
                case 4:
                    return;
                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }

    private function menuAdminHabitaciones()
    {
      //  $habitacionesGestor = new HabitacionControlador;
      //  $habitacionesGestor->cargarDesdeJSON();

        while (true) {
            echo "\n=== Menú Administrar Habitaciones ===\n";
            echo "1. Mostrar Habitaciones\n";
            echo "2. Agregar Habitación\n";
            echo "3. Modificar Habitación\n";
            echo "4. Eliminar Habitación\n";
            echo "5. Volver al Menú Principal\n";
            echo 'Seleccione una opción: ';

            $opcion = trim(fgets(STDIN));

            switch ($opcion) {
                case 1:
                    verHabitaciones();
                    break;
                case 2:
                    $this->menuAdminControlador->agregarHabitacion();
                    break;
                case 3:
                    $this->menuAdminControlador->modificarHabitacion();
                    break;
                case 4:
                    $this->menuAdminControlador->eliminarHabitacion();
                    break;
                case 5:
                    return;
                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }

    private function menuAdminReservas()
    {
        $habitacionesGestor = new HabitacionControlador;
//        $habitacionesGestor->cargarDesdeJSON();
       $reservasGestor = new ReservaControlador($habitacionesGestor);

        while (true) {
            echo "=== Menú Administrar Reservas ===\n";
            echo "1. Mostrar Reservas\n";
            echo "2. Modificar Reserva\n";
            echo "3. Eliminar Reserva\n";
            echo "4. Volver al Menú Principal\n";
            echo 'Seleccione una opción: ';

            $opcion = trim(fgets(STDIN));

            switch ($opcion) {
                case 1:
                    $reservas = $reservasGestor->obtenerReservas();
                    foreach ($reservas as $reserva) {
                        echo $reserva . "\n";
                    }
                    break;
                case 2:
                    modificarReserva($reservasGestor, $habitacionesGestor, true);
                    break;
                case 3:
                    eliminarReserva($reservasGestor, $habitacionesGestor, true);
                    break;
                case 4:
                    return;
                default:
                    echo "Opción no válida. Inténtelo de nuevo.\n";
                    break;
            }
        }
    }
}

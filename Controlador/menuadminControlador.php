<?php

class menuAdminControlador
{
    private $habitacionesGestor;
    private $usuariosGestor;

    public function __construct($habitacionesGestor, $usuariosGestor)
    {
        $this->habitacionesGestor = $habitacionesGestor;
        $this->usuariosGestor = $usuariosGestor;
    }

    
    //admin hab
    public function agregarHabitacion()
    {
        echo 'Ingrese el número de la habitación: ';
        $numero = trim(fgets(STDIN));

        foreach ($this->habitacionesGestor->obtenerHabitaciones() as $habitacion) {
            if ($habitacion->getNumero() == $numero) {
                echo "La habitación con el número $numero ya existe. No se puede duplicar.\n";
                return;
            }
        }

        echo 'Ingrese el tipo de habitación: ';
        $tipo = trim(fgets(STDIN));
        echo 'Ingrese el precio por noche: ';
        $precio = trim(fgets(STDIN));

        $this->habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio));
        echo "Habitación agregada exitosamente.\n";
    }

    public function modificarHabitacion()
    {
        echo 'Ingrese el número de la habitación que desea modificar: ';
        $numero = trim(fgets(STDIN));

        $habitacion = $this->buscarHabitacionPorNumero($numero);

        if ($habitacion) {
            echo "Modificando habitación número: $numero\n";
            echo "Ingrese el nuevo tipo de habitación (deje vacío para mantener el actual: {$habitacion->getTipo()}): ";
            $nuevoTipo = trim(fgets(STDIN));
            echo "Ingrese el nuevo precio (deje vacío para mantener el actual: {$habitacion->getPrecio()}): ";
            $nuevoPrecio = trim(fgets(STDIN));

            $nuevosDatos = [
                'tipo' => $nuevoTipo ?: $habitacion->getTipo(),
                'precio' => $nuevoPrecio ?: $habitacion->getPrecio(),
            ];

            if ($this->habitacionesGestor->actualizarHabitacion($numero, $nuevosDatos)) {
                echo "Habitación actualizada correctamente.\n";
            } else {
                echo "Error al actualizar la habitación.\n";
            }
        } else {
            echo "La habitación con número $numero no existe.\n";
        }
    }

    public function eliminarHabitacion()
    {
        echo 'Ingrese el número de la habitación que desea eliminar: ';
        $numero = trim(fgets(STDIN));

        if ($this->habitacionesGestor->eliminarHabitacion($numero)) {
            echo "Habitación eliminada correctamente.\n";
        } else {
            echo "Error al eliminar la habitación.\n";
        }
    }

    private function buscarHabitacionPorNumero($numero)
    {
        foreach ($this->habitacionesGestor->obtenerHabitaciones() as $habitacion) {
            if ($habitacion->getNumero() == $numero) {
                return $habitacion;
            }
        }
        return null;
    }

    // admin usuario

    public function mostrarUsuarios()
    {
        $usuarios = $this->usuariosGestor->obtenerUsuarios();
        foreach ($usuarios as $usuario) {
            echo $usuario . "\n";
        }
    }

    public function eliminarUsuario()
    {
        echo 'Ingrese el ID a eliminar: ';
        $idEliminado = trim(fgets(STDIN));

        if ($this->usuariosGestor->eliminarUsuario($idEliminado)) {
            echo "Usuario {$idEliminado} eliminado correctamente.\n";
        } else {
            echo "No se pudo eliminar el usuario {$idEliminado}. Puede que no exista.\n";
        }
    }
}
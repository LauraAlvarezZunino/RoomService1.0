<?php

//admin habitacion
function agregarHabitacion($habitacionesGestor)
{
    echo 'Ingrese el número de la habitación: ';
    $numero = trim(fgets(STDIN));

    foreach ($habitacionesGestor->obtenerHabitaciones() as $h) {
        if ($h->getNumero() == $numero) {
            echo "La habitación con el número $numero ya existe. No se puede duplicar.\n";

            return;
        }
    }

    echo 'Ingrese el tipo de habitación: ';
    $tipo = trim(fgets(STDIN));
    echo 'Ingrese el precio por noche: ';
    $precio = trim(fgets(STDIN));

    $habitacionesGestor->agregarHabitacion(new Habitacion($numero, $tipo, $precio));
    echo "Habitación agregada exitosamente.\n";
}

function modificarHabitacion($habitacionesGestor)
{
    echo 'Ingrese el número de la habitación que desea modificar: ';
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
            'tipo' => $nuevoTipo ?: $habitacion->getTipo(),
            'precio' => $nuevoPrecio ?: $habitacion->getPrecio(),
        ];

        if ($habitacionesGestor->actualizarHabitacion($numero, $nuevosDatos)) {
            echo "Habitación actualizada correctamente.\n";
        } else {
            echo "Error al actualizar la habitación.\n";
        }
    } else {
        echo "La habitación con número $numero no existe.\n";
    }
}

function eliminaHabitacion($habitacionesGestor)
{
    echo 'Ingrese el número de la habitación que desea eliminar: ';
    $numero = trim(fgets(STDIN));

    if ($habitacionesGestor->eliminarHabitacion($numero)) {
        echo "Habitación eliminada correctamente.\n";
    } else {
        echo "Error al eliminar la habitación.\n";
    }
}

//admin usuarios
function mostrarUsuarios($usuariosGestor)
{
    $usuarios = $usuariosGestor->obtenerUsuarios();
    foreach ($usuarios as $usuario) {
        echo $usuario . "\n";
    }
}

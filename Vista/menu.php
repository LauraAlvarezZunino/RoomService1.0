
mostrarReservas($reservasGestor, false, $usuario);
mostrarReservas($reservasGestor, true);
--------------------------------------

modificarReserva($reservasGestor, $habitacionesGestor, false, $usuario);
modificarReserva($reservasGestor, $habitacionesGestor, true);
--------------------------------------------------
modificarUsuario($reservasGestor, $true);

'''''' function mostrarHabitacionesDisponibles($habitaciones)
{
    echo "Habitaciones disponibles:\n";
    foreach ($habitaciones as $index => $habitacion) {
        echo $index . ". Número: " . $habitacion->getNumero() . " - Precio por noche: " . $habitacion->getPrecio() . "\n";
    }
}

  -------------------------------------------------------------  



---------------------------------------------------------------------------

---------------------------------------------------------------------------
*/
//funciones usadas opr ambos ver habs 
//ver reservas uno con filtro otro sin
//modificar reservas uno con filtro de dni otro sin
//eliminar reserva por id uno con dni otro sin
//
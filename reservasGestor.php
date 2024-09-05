<?php

class ReservasGestor {
    private $reservas = [];
    private $reservaJson = 'reservas.json';
    private $id = 1; // ID inicial

    public function __construct() {
        $this->cargarDesdeJSON();
    }

    public function generarNuevoId() {
        return $this->id++;
    }

    public function agregarReserva(Reserva $reserva) {
        $this->reservas[] = $reserva;
        $this->guardarEnJSON();
    }

    public function obtenerReservas() {
        return $this->reservas;
    }

    private function guardarEnJSON() {
        $reservasArray = [];
    
        foreach ($this->reservas as $reserva) {
            $reservasArray[] = [
                'id' => $reserva->getId(),
                'fecha_inicio' => $reserva->getFechaInicio(),
                'fecha_fin' => $reserva->getFechaFin(),
                'estado' => $reserva->getEstado(),
                'costo' => $reserva->getCosto()
            ];
        }
    
        file_put_contents($this->reservaJson, json_encode($reservasArray, JSON_PRETTY_PRINT));
    }
    
    private function cargarDesdeJSON() {
        if (file_exists($this->reservaJson)) {
            $json = file_get_contents($this->reservaJson);
            $reservasArray = json_decode($json, true);

            foreach ($reservasArray as $reservaData) {
                $reserva = new Reserva(
                    $reservaData['id'],
                    $reservaData['fecha_inicio'],
                    $reservaData['fecha_fin'],
                    $reservaData['estado'],
                    $reservaData['costo']
                );
                $this->reservas[] = $reserva;
                if ($this->id < $reserva->getId() + 1) {
                    $this->id = $reserva->getId() + 1;
                }
            }
        }
    }
}
?>
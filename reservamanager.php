<?php

class ReservaManager {
    private $reservas = [];
    private $filePath = 'reservas.json';
    private $nextId = 1; // ID inicial

    public function __construct() {
        $this->cargarDesdeJSON();
    }

    public function generarNuevoId() {
        return $this->nextId++;
    }

    public function agregarReserva(Reserva $reserva) {
        $this->reservas[] = $reserva;
        $this->guardarEnJSON();
    }

    public function obtenerReservas() {
        return $this->reservas;
    }

    private function guardarEnJSON() {
        $reservasArray = array_map(function($reserva) {
            return [
                'id' => $reserva->getId(),
                'fecha_inicio' => $reserva->getFechaInicio(),
                'fecha_fin' => $reserva->getFechaFin(),
                'estado' => $reserva->getEstado(),
                'costo' => $reserva->getCosto()
            ];
        }, $this->reservas);

        file_put_contents($this->filePath, json_encode($reservasArray, JSON_PRETTY_PRINT));
    }

    private function cargarDesdeJSON() {
        if (file_exists($this->filePath)) {
            $json = file_get_contents($this->filePath);
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
                $this->nextId = max($this->nextId, $reserva->getId() + 1);
            }
        }
    }
}
?>
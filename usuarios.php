<?php

class Usuarios
{
    private $usuarios = [];
    private $usuarioJson = 'usuario.json';

    public function __construct()
    {
        $this->cargarDesdeJSON();
    }

    // Cargar datos desde JSON
    private function cargarDesdeJSON()
    {
        if (file_exists($this->usuarioJson)) {
            $jsonUsuarios = file_get_contents($this->usuarioJson);

            $data = json_decode($jsonUsuarios, true);

            if (isset($data['usuarios'])) {  // Verifica si existe la clave 'usuarios'
                $usuariosArray = $data['usuarios'];

                foreach ($usuariosArray as $usuarioData) {
                    $usuario = new Usuario(
                        $usuarioData['id'],
                        $usuarioData['nombre_apellido'],
                        $usuarioData['dni'],
                        $usuarioData['email'],
                        $usuarioData['telefono']
                    );
                    $this->usuarios[] = $usuario;
                }
            } else {
                echo "No se encontró la clave 'usuarios' en el archivo JSON.\n";
            }
        } else {
            echo "No se encontró el archivo JSON.\n";
        }
    }

    // Guardar datos en JSON
    private function guardarEnJSON()
    {
        $usuariosArray = array_map([$this, 'usuarioToArray'], $this->usuarios);
        $jsonUsuario = json_encode(['usuarios' => $usuariosArray], JSON_PRETTY_PRINT);
        file_put_contents($this->usuarioJson, $jsonUsuario);
    }

    // Pasasr Usuario en array
    private function usuarioToArray($usuario)
    {
        return [
            'id' => $usuario->getId(),
            'nombre_apellido' => $usuario->getNombreApellido(),
            'dni' => $usuario->getDni(),
            'email' => $usuario->getEmail(),
            'telefono' => $usuario->getTelefono()
        ];
    }

    // Crear (Agregar) un nuevo usuario con ID autogenerado
    public function crearUsuario($nombre_apellido, $dni, $email, $telefono)
    {
        $nuevoId = $this->generarNuevoId();
        $usuario = new Usuario($nuevoId, $nombre_apellido, $dni, $email, $telefono);
        $this->usuarios[] = $usuario;
        $this->guardarEnJSON();
    }

    // Generar un nuevo ID basado en el último ID existente
    private function generarNuevoId()
    {
        if (empty($this->usuarios)) {
            return 1; // Si no hay usuarios, el primer ID es 1
        } else {
            $ultimoUsuario = end($this->usuarios);
            return $ultimoUsuario->getId() + 1;
        }
    }


    // Leer (Obtener) todos los usuarios
    public function obtenerUsuarios()
    {
        return $this->usuarios;
    }

    // Leer (Obtener) un usuario por ID
    public function obtenerUsuarioPorId($id)
    {
        foreach ($this->usuarios as $usuario) {
            if ($usuario->getId() == $id) {
                return $usuario;
            }
        }
        return null;
    }

    // Actualizar un usuario existente
    public function actualizarUsuario($id, $nuevosDatos)
    {
        foreach ($this->usuarios as &$usuario) {
            if ($usuario->getId() == $id) {
                $usuario->setNombreApellido($nuevosDatos['nombre_apellido'] ?? $usuario->getNombreApellido());
                $usuario->setDni($nuevosDatos['dni'] ?? $usuario->getDni());
                $usuario->setEmail($nuevosDatos['email'] ?? $usuario->getEmail());
                $usuario->setTelefono($nuevosDatos['telefono'] ?? $usuario->getTelefono());
                $this->guardarEnJSON();
                return true;
            }
        }
        return false;
    }

    // Eliminar un usuario
    public function eliminarUsuario($id)
    {
        foreach ($this->usuarios as $key => $usuario) {
            if ($usuario->getId() == $id) {
                unset($this->usuarios[$key]);
                $this->usuarios = array_values($this->usuarios);
                $this->guardarEnJSON();
                return true;
            }
        }
        return false;
    }
}

class Usuario
{
    private $id;
    private $nombre_apellido;
    private $dni;
    private $email;
    private $telefono;

    public function __construct($id, $nombre_apellido, $dni, $email, $telefono)
    {
        $this->id = $id;
        $this->nombre_apellido = $nombre_apellido;
        $this->dni = $dni;
        $this->email = $email;
        $this->telefono = $telefono;
    }

    public function __toString()
    {
        return "ID: " . $this->id . ", Nombre: " . $this->nombre_apellido . ", DNI: " . $this->dni . ", Email: " . $this->email . ", Teléfono: " . $this->telefono;
    }

    // Getters y Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombreApellido()
    {
        return $this->nombre_apellido;
    }

    public function setNombreApellido($nombre_apellido)
    {
        $this->nombre_apellido = $nombre_apellido;
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }
}

<?php

class DestinosModel
{
    private $id;
    private $nombre;
    private $descripcion;
    private $conn = null;

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setdescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }


    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getdescripcion()
    {
        return $this->descripcion;
    }


    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM destino");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM destino WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $usuario = $stmt->fetch();
        return $usuario;
    }

    public function getDestinosInIds($ids)
    {
        $placeholders = implode(",", array_fill(0, count($ids), "?"));
        $stmt = $this->conn->prepare("SELECT * FROM destino WHERE id IN ($placeholders)");
        foreach ($ids as $i => $id)
        {
            $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("INSERT INTO destino(nombre_destino, descripcion) VALUES (:nombre,:descripcion)");
        $nombre = $this->getNombre();
        $descripcion = $this->getdescripcion();

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);

        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE destino SET nombre_destino=:nombre, descripcion=:descripcion WHERE id = :id");
        $id = $this->getId();
        $nombre = $this->getNombre();
        $descripcion = $this->getdescripcion();


        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);

        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM   destino WHERE id=:id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0)
        {
            return true;
        }
        return false;
    }
}

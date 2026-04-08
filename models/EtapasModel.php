<?php

class EtapasModel
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
        $stmt = $this->conn->prepare("SELECT * FROM etapas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM etapas WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $usuario = $stmt->fetch();
        return $usuario;
    }



    public function crear()
    {
        $stmt = $this->conn->prepare("INSERT INTO etapas(nombre, descripcion) VALUES (:nombre,:descripcion)");
        $nombre = $this->getNombre();
        $descripcion = $this->getdescripcion();

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);

        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE etapas SET nombre=:nombre, descripcion=:descripcion WHERE id = :id");
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
        $stmt = $this->conn->prepare("DELETE FROM etapas WHERE id=:id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}

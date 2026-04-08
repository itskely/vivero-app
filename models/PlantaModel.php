<?php

class PlantaModel
{
    private $id;
    private $nombre_cientifico;
    private $nombre_comun;
    private $imagen;
    private $descripcion;

    // Variables de busqueda y paginación
    private $busqueda;
    private $limit;
    private $offset;
    private $conn = null;

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($nombre_cientifico)
    {
        $this->nombre_cientifico = $nombre_cientifico;
    }
    public function setNombreComun($nombre_comun)
    {
        $this->nombre_comun = $nombre_comun;
    }
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
    public function setBusqueda($busqueda)
    {
        $this->busqueda = $busqueda;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }


    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombreCientifico()
    {
        return $this->nombre_cientifico;
    }

    public function getNombreComun()
    {
        return $this->nombre_comun;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getImagen()
    {
        return $this->imagen;
    }
    public function getBusqueda()
    {
        return $this->busqueda;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getOffset()
    {
        return $this->offset;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getCount()
    {
        $busqueda = $this->getBusqueda();
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM plantas WHERE nombre_comun LIKE :busqueda OR descripcion LIKE :busqueda");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $busqueda = $this->getBusqueda();
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $stmt = $this->conn->prepare("SELECT * FROM plantas WHERE nombre_comun LIKE :busqueda OR descripcion LIKE :busqueda LIMIT :lim OFFSET :offs");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM plantas WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page;
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("INSERT INTO plantas(nombre_cientifico, nombre_comun, imagen, descripcion) VALUES (:nombre_cientifico,:nombre_comun,:imagen,:descripcion)");
        $nombre_cientifico = $this->getNombreCientifico();
        $nombre_comun = $this->getNombreComun();
        $imagen = $this->getImagen();
        $descripcion = $this->getDescripcion();
        $stmt->bindParam(":nombre_cientifico", $nombre_cientifico);
        $stmt->bindParam(":nombre_comun", $nombre_comun);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":descripcion", $descripcion);
        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE plantas SET nombre_cientifico=:nombre_cientifico, nombre_comun=:nombre_comun, imagen=:imagen, descripcion=:descripcion WHERE id = :id");
        $id = $this->getId();
        $nombre_cientifico = $this->getNombreCientifico();
        $nombre_comun = $this->getNombreComun();
        $imagen = $this->getImagen();
        $descripcion = $this->getDescripcion();




        $stmt->bindParam(":nombre_cientifico", $nombre_cientifico);
        $stmt->bindParam(":nombre_comun", $nombre_comun);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM plantas WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}

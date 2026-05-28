<?php

class InventarioModel
{
    private $id;
    private $lote_id;
    private $etapa_id;
    private $ubicacion_id;
    private $cantidad_actual;
    private $conn = null;

    // Variables de busqueda y paginación
    private $busqueda;
    private $limit;
    private $offset;

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

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLoteId($lote_id)
    {
        $this->lote_id = $lote_id;
    }

    public function setEtapaId($etapa_id)
    {
        $this->etapa_id = $etapa_id;
    }

    public function setUbicacionId($ubicacion_id)
    {
        $this->ubicacion_id = $ubicacion_id;
    }

    public function setCantidadActual($cantidad_actual)
    {
        $this->cantidad_actual = $cantidad_actual;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getLoteId()
    {
        return $this->lote_id;
    }

    public function getEtapaId()
    {
        return $this->etapa_id;
    }

    public function getUbicacionId()
    {
        return $this->ubicacion_id;
    }

    public function getCantidadActual()
    {
        return $this->cantidad_actual;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM inventario");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM inventario WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $page = $stmt->fetch();
        return $page;
    }

    public function getByLoteId()
    {
        $stmt = $this->conn->prepare("SELECT * FROM inventario WHERE lote_id = :lote_id");
        $lote_id = $this->getLoteId();
        $stmt->bindParam(":lote_id", $lote_id);
        $stmt->execute();
        $inventarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $inventarios;
    }

    public function getCount()
    {
        $busqueda = $this->getBusqueda();
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM inventario i
            JOIN lotes l ON i.lote_id = l.id
            JOIN plantas p ON l.planta_id = p.id
            JOIN etapas e ON i.etapa_id = e.id
            JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE p.nombre_comun LIKE :busqueda OR p.nombre_cientifico LIKE :busqueda OR e.nombre LIKE :busqueda OR u.nombre LIKE :busqueda
        ");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCompleto()
    {
        $busqueda = $this->getBusqueda();
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $stmt = $this->conn->prepare("
            SELECT i.*, p.nombre_comun, p.nombre_cientifico, e.nombre as etapa, u.nombre as ubicacion, l.unidad_medida
            FROM inventario i
            LEFT JOIN lotes l ON i.lote_id = l.id
            LEFT JOIN plantas p ON l.planta_id = p.id
            LEFT JOIN etapas e ON i.etapa_id = e.id
            LEFT JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE p.nombre_comun LIKE :busqueda OR p.nombre_cientifico LIKE :busqueda OR e.nombre LIKE :busqueda OR u.nombre LIKE :busqueda
            ORDER BY i.ultima_actualizacion DESC
            LIMIT :lim OFFSET :offs
        ");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

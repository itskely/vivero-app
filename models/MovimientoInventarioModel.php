<?php

class MovimientoInventarioModel
{
    private $id;
    private $lote_id;
    private $etapa_id;
    private $ubicacion_id;
    private $cantidad;
    private $motivo;

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
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
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

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getMotivo()
    {
        return $this->motivo;
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
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM movimientos_inventario WHERE lote_id LIKE :busqueda OR etapa_id LIKE :busqueda OR ubicacion_id LIKE :busqueda OR cantidad LIKE :busqueda OR motivo LIKE :busqueda");
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
        $stmt = $this->conn->prepare("SELECT * FROM movimientos_inventario WHERE lote_id LIKE :busqueda OR etapa_id LIKE :busqueda OR ubicacion_id LIKE :busqueda OR cantidad LIKE :busqueda OR motivo LIKE :busqueda LIMIT :lim OFFSET :offs");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM movimientos_inventario WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page;
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("CALL sp_ajuste_auditoria(:lote_id, :etapa_id, :ubicacion_id, :cantidad, :usuario_id, :motivo)");
        $lote_id = $this->getLoteId();
        $etapa_id = $this->getEtapaId();
        $ubicacion_id = $this->getUbicacionId();
        $cantidad = $this->getCantidad();
        $usuario_id = $_SESSION['usuario']['id'];
        $observaciones = $this->getMotivo();

        $stmt->bindParam(":lote_id", $lote_id);
        $stmt->bindParam(":etapa_id", $etapa_id);
        $stmt->bindParam(":ubicacion_id", $ubicacion_id);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":motivo", $observaciones);
        return $stmt->execute();
    }
}
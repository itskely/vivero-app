<?php

class LoteModel
{
    private $id;
    private $planta_id;

    private $codigo_lote;
    private $unidad_medida;
    private $cantidad;
    private $etapa_id;
    private $ubicacion_id;
    private $origen;
    private $observaciones;

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

    public function setPlantaId($planta_id)
    {
        $this->planta_id = $planta_id;
    }
    public function setCodigoLote($codigo_lote)
    {
        $this->codigo_lote = $codigo_lote;
    }
    public function setUnidadMedida($unidad_medida)
    {
        $this->unidad_medida = $unidad_medida;
    }
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function setEtapaId($etapa_id)
    {
        $this->etapa_id = $etapa_id;
    }
    public function setUbicacionId($ubicacion_id)
    {
        $this->ubicacion_id = $ubicacion_id;
    }
    public function setOrigen($origen)
    {
        $this->origen = $origen;
    }
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
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

    public function getPlantaId()
    {
        return $this->planta_id;
    }

    public function getCodigoLote()
    {
        return $this->codigo_lote;
    }

    public function getUnidadMedida()
    {
        return $this->unidad_medida;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getEtapaId()
    {
        return $this->etapa_id;
    }

    public function getUbicacionId()
    {
        return $this->ubicacion_id;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }
    public function getOrigen()
    {
        return $this->origen;
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
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lotes WHERE codigo_lote LIKE :busqueda OR observaciones LIKE :busqueda");
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
        $stmt = $this->conn->prepare("SELECT l.* FROM lotes l LEFT JOIN plantas p ON l.planta_id = p.id WHERE l.codigo_lote LIKE :busqueda OR l.observaciones LIKE :busqueda OR p.nombre_comun LIKE :busqueda OR p.nombre_cientifico LIKE :busqueda OR p.descripcion LIKE :busqueda ORDER BY l.id DESC LIMIT :lim OFFSET :offs");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM lotes WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        return $page;
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("CALL sp_registrar_nuevo_lote(:planta_id, :codigo_lote, :unidad_medida, :cantidad, :etapa_id, :ubicacion_id, :usuario_id, :observaciones, :origen)");
        $planta_id = $this->getPlantaId();
        $codigo_lote = $this->getCodigoLote();
        $unidad_medida = $this->getUnidadMedida();
        $cantidad = $this->getCantidad();
        $etapa_id = $this->getEtapaId();
        $ubicacion_id = $this->getUbicacionId();
        $origen = $this->getOrigen();
        $usuario_id = $_SESSION['usuario']['id'];
        $observaciones = $this->getObservaciones();
        $stmt->bindParam(":planta_id", $planta_id);
        $stmt->bindParam(":codigo_lote", $codigo_lote);
        $stmt->bindParam(":unidad_medida", $unidad_medida);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":etapa_id", $etapa_id);
        $stmt->bindParam(":ubicacion_id", $ubicacion_id);
        $stmt->bindParam(":origen", $origen);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":observaciones", $observaciones);
        return $stmt->execute();
    }
}

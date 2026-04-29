<?php

class CambioEtapaModel
{
    private $id;
    private $lote_id;
    private $etapa_origen_id;
    private $ubi_origen_id;
    private $etapa_destino_id;
    private $ubi_destino_id;
    private $cantidad_salida;
    private $cantidad_entrada;
    private $observaciones;
    private $unidad_medida;

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
    public function setUnidadMedida($unidad_medida)
    {
        $this->unidad_medida = $unidad_medida;
    }
    public function setLoteId($lote_id)
    {
        $this->lote_id = $lote_id;
    }
    public function setEtapaOrigenId($etapa_origen_id)
    {
        $this->etapa_origen_id = $etapa_origen_id;
    }
    public function setUbiOrigenId($ubi_origen_id)
    {
        $this->ubi_origen_id = $ubi_origen_id;
    }
    public function setEtapaDestinoId($etapa_destino_id)
    {
        $this->etapa_destino_id = $etapa_destino_id;
    }
    public function setUbiDestinoId($ubi_destino_id)
    {
        $this->ubi_destino_id = $ubi_destino_id;
    }
    public function setCantidadSalida($cantidad_salida)
    {
        $this->cantidad_salida = $cantidad_salida;
    }
    public function setCantidadEntrada($cantidad_entrada)
    {
        $this->cantidad_entrada = $cantidad_entrada;
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

    public function getUnidadMedida()
    {
        return $this->unidad_medida;
    }

    public function getLoteId()
    {
        return $this->lote_id;
    }

    public function getEtapaOrigenId()
    {
        return $this->etapa_origen_id;
    }

    public function getUbiOrigenId()
    {
        return $this->ubi_origen_id;
    }

    public function getEtapaDestinoId()
    {
        return $this->etapa_destino_id;
    }

    public function getUbiDestinoId()
    {
        return $this->ubi_destino_id;
    }

    public function getCantidadSalida()
    {
        return $this->cantidad_salida;
    }

    public function getCantidadEntrada()
    {
        return $this->cantidad_entrada;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
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
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM lotes WHERE id LIKE :busqueda OR observaciones LIKE :busqueda");
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
        $stmt = $this->conn->prepare("SELECT * FROM lotes WHERE id LIKE :busqueda OR observaciones LIKE :busqueda LIMIT :lim OFFSET :offs");
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
        $stmt = $this->conn->prepare("CALL sp_cambiar_etapa_lote(:lote_id, :etapa_origen_id, :ubi_origen_id, :etapa_destino_id, :ubi_destino_id, :cantidad_salida, :cantidad_entrada, :usuario_id, :observaciones, :unidad_medida)");
        $lote_id = $this->getLoteId();
        $etapa_origen_id = $this->getEtapaOrigenId();
        $ubi_origen_id = $this->getUbiOrigenId();
        $etapa_destino_id = $this->getEtapaDestinoId();
        $ubi_destino_id = $this->getUbiDestinoId();
        $cantidad_salida = $this->getCantidadSalida();
        $cantidad_entrada = $this->getCantidadEntrada();
        $usuario_id = $_SESSION['usuario']['id'];
        $observaciones = $this->getObservaciones();
        $unidad_medida = $this->getUnidadMedida();

        $stmt->bindParam(":lote_id", $lote_id);
        $stmt->bindParam(":etapa_origen_id", $etapa_origen_id);
        $stmt->bindParam(":ubi_origen_id", $ubi_origen_id);
        $stmt->bindParam(":etapa_destino_id", $etapa_destino_id);
        $stmt->bindParam(":ubi_destino_id", $ubi_destino_id);
        $stmt->bindParam(":cantidad_salida", $cantidad_salida);
        $stmt->bindParam(":cantidad_entrada", $cantidad_entrada);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":observaciones", $observaciones);
        $stmt->bindParam(":unidad_medida", $unidad_medida);
        return $stmt->execute();
    }

    public function transformar()
    {
        $stmt = $this->conn->prepare("CALL sp_transformar_lote_etapa(:lote_padre_id, :etapa_origen_id, :ubi_origen_id, :etapa_destino_id, :ubi_destino_id, :cant_salida_padre, :cant_entrada_hijo, :usuario_id, :observaciones, :unidad_medida)");
        $lote_padre_id = $this->getLoteId();
        $etapa_origen_id = $this->getEtapaOrigenId();
        $ubi_origen_id = $this->getUbiOrigenId();
        $etapa_destino_id = $this->getEtapaDestinoId();
        $ubi_destino_id = $this->getUbiDestinoId();
        $cant_salida_padre = $this->getCantidadSalida();
        $cant_entrada_hijo = $this->getCantidadEntrada();
        $usuario_id = $_SESSION['usuario']['id'];
        $observaciones = $this->getObservaciones();
        $unidad_medida = $this->getUnidadMedida();

        $stmt->bindParam(":lote_padre_id", $lote_padre_id);
        $stmt->bindParam(":etapa_origen_id", $etapa_origen_id);
        $stmt->bindParam(":ubi_origen_id", $ubi_origen_id);
        $stmt->bindParam(":etapa_destino_id", $etapa_destino_id);
        $stmt->bindParam(":ubi_destino_id", $ubi_destino_id);
        $stmt->bindParam(":cant_salida_padre", $cant_salida_padre);
        $stmt->bindParam(":cant_entrada_hijo", $cant_entrada_hijo);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":observaciones", $observaciones);
        $stmt->bindParam(":unidad_medida", $unidad_medida);
        return $stmt->execute();
    }
}
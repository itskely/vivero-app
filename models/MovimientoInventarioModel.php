<?php

class MovimientoInventarioModel
{
    private $id;
    private $lote_id;
    private $etapa_id;
    private $ubicacion_id;
    private $cantidad;
    private $tipo_movimiento;
    private $motivo;
    private $destino_id;

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
    public function setTipoMovimiento($tipo_movimiento)
    {
        $this->tipo_movimiento = $tipo_movimiento;
    }
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }
    public function setDestinoId($destino_id)
    {
        $this->destino_id = $destino_id;
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
    public function getTipoMovimiento()
    {
        return $this->tipo_movimiento;
    }
    public function getMotivo()
    {
        return $this->motivo;
    }
    public function getDestinoId()
    {
        return $this->destino_id;
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
        $stmt = $this->conn->prepare("SELECT mi.id, l.id AS lote_id, l.unidad_medida, p.nombre_comun, mi.tipo_movimiento, mi.cantidad, e.nombre AS nombre_etapa, u.nombre AS nombre_ubicacion, o.nombre_origen, d.nombre_destino, mi.motivo, mi.fecha, mi.estado FROM movimientos_inventario AS mi 
        INNER JOIN lotes AS l ON mi.lote_id = l.id 
        INNER JOIN plantas AS p ON l.planta_id = p.id 
        INNER JOIN etapas AS e ON mi.etapa_id = e.id
        INNER JOIN ubicaciones AS u ON mi.ubicacion_id = u.id 
        INNER JOIN usuarios AS us ON mi.usuario_id = us.id 
        INNER JOIN origen AS o ON o.id = l.origen_id
        LEFT JOIN destino AS d ON d.id = mi.destino_id
        WHERE l.id LIKE :busqueda OR p.nombre_comun LIKE :busqueda OR mi.tipo_movimiento LIKE :busqueda OR e.nombre LIKE :busqueda OR u.nombre LIKE :busqueda OR mi.estado LIKE :busqueda ORDER BY mi.id DESC LIMIT :lim OFFSET :offs;");
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

    public function anular()
    {
        $stmt = $this->conn->prepare("CALL sp_anular_movimiento(:movimiento_id, :usuario_id)");
        $id = $this->getId();
        $usuario_id = $_SESSION['usuario']['id'];

        $stmt->bindParam(":movimiento_id", $id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        return $stmt->execute();
    }

    public function store()
    {
        $stmt = $this->conn->prepare("INSERT INTO movimientos_inventario(lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo, destino_id) VALUES (:lote_id, :etapa_id, :ubicacion_id, :usuario_id, :tipo_movimiento, :cantidad, :motivo, :destino_id)");
        $lote_id = $this->getLoteId();
        $etapa_id = $this->getEtapaId();
        $ubicacion_id = $this->getUbicacionId();
        $tipo_movimiento = $this->getTipoMovimiento();
        $cantidad = $this->getCantidad();
        $usuario_id = $_SESSION['usuario']['id'];
        $observaciones = $this->getMotivo();
        $destino_id = $this->getDestinoId();

        $stmt->bindParam(":lote_id", $lote_id);
        $stmt->bindParam(":etapa_id", $etapa_id);
        $stmt->bindParam(":ubicacion_id", $ubicacion_id);
        $stmt->bindParam(":tipo_movimiento", $tipo_movimiento);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":motivo", $observaciones);
        $stmt->bindParam(":destino_id", $destino_id);
        return $stmt->execute();
    }

    public function movimientosMes($etapa_id, $year)
    {
        // Hacer concat de php de ', etapa_id' en el where en caso de etapa_id ser diferente de null o 'all'
        $stmt = $this->conn->prepare("SELECT 
        tipo_movimiento, 
        SUM(cantidad) as total, 
        CONCAT(YEAR(fecha), '-', MONTH(fecha)) as ano_mes 
        FROM movimientos_inventario 
        WHERE YEAR(fecha) = :year
        " . ($etapa_id !== 'all' ? " AND etapa_id = :etapa_id" : "") . "
        GROUP BY YEAR(fecha), MONTH(fecha), tipo_movimiento
        ORDER BY ano_mes ASC");
        if ($etapa_id !== 'all') {
            $stmt->bindParam(":etapa_id", $etapa_id);
        }
        $stmt->bindParam(":year", $year);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function years()
    {
        $stmt = $this->conn->prepare("SELECT YEAR(fecha) as ano FROM movimientos_inventario GROUP BY YEAR(fecha)");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function movimientosDestino()
    {
        $stmt = $this->conn->prepare("
        SELECT 
            d.nombre_destino AS nombre, 
            SUM(cantidad) AS total 
        FROM movimientos_inventario AS mi 
        INNER JOIN destino AS d ON mi.destino_id = d.id 
        WHERE destino_id IS NOT NULL 
        GROUP BY destino_id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salidasDestinosMes(
        $year,
        $mes
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            d.nombre_destino AS nombre,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN destino d
            ON d.id = mi.destino_id

        WHERE mi.destino_id IS NOT NULL 

        AND YEAR(mi.fecha) = :year
        AND MONTH(mi.fecha) = :mes

        GROUP BY mi.destino_id
    ");
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mes", $mes);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salidasDestinosCuatrimestre(
        $year,
        $mesInicio,
        $mesFin
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            d.nombre_destino AS nombre,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN destino d
            ON d.id = mi.destino_id

        WHERE mi.destino_id IS NOT NULL 

        AND YEAR(mi.fecha) = :year
        AND MONTH(mi.fecha) BETWEEN :mesInicio AND :mesFin

        GROUP BY mi.destino_id
    ");
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mesInicio", $mesInicio);
        $stmt->bindParam(":mesFin", $mesFin);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function salidasDestinosAnio(
        $year
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            d.nombre_destino AS nombre,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN destino d
            ON d.id = mi.destino_id

        WHERE mi.destino_id IS NOT NULL 

        AND YEAR(mi.fecha) = :year

        GROUP BY mi.destino_id
    ");
        $stmt->bindParam(":year", $year);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function destino()
    {
        $stmt = $this->conn->prepare("SELECT tipo_movimiento, COUNT(*) AS total FROM movimientos_inventario GROUP BY YEAR(CURRENT_DATE), MONTH(CURRENT_DATE), tipo_movimiento");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function semillasRecolectadasMes(
        $year,
        $mes,
        $unidad
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total_gramos

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
        AND mi.motivo = 'Registro inicial'
        AND mi.etapa_id = 2
        AND l.unidad_medida = :unidad

        AND YEAR(mi.fecha) = :year
        AND MONTH(mi.fecha) = :mes

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total_gramos DESC
    ");
        $stmt->bindParam(":unidad", $unidad);
        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mes", $mes);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function semillasRecolectadasCuatrimestre(
        $year,
        $mesInicio,
        $mesFin,
        $unidad
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total_gramos

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
        AND mi.motivo = 'Registro inicial'
        AND mi.etapa_id = 2
        AND l.unidad_medida = :unidad

        AND YEAR(mi.fecha) = :year
        AND MONTH(mi.fecha)
            BETWEEN :mesInicio
            AND :mesFin

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total_gramos DESC
    ");

        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mesInicio", $mesInicio);
        $stmt->bindParam(":mesFin", $mesFin);
        $stmt->bindParam(":unidad", $unidad);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function semillasRecolectadasAnio(
        $year,
        $unidad
    ) {

        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total_gramos

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
        AND mi.motivo = 'Registro inicial'
        AND mi.etapa_id = 2
        AND l.unidad_medida = :unidad

        AND YEAR(mi.fecha) = :year

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total_gramos DESC
    ");

        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":unidad", $unidad);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSalidasUsuario()
    {
        $usuario_id = $_SESSION['usuario']['id'];

        $sql = "SELECT
                p.nombre_comun AS planta,
                mi.cantidad,
                d.nombre_destino AS destino,
                mi.motivo,
                mi.fecha
            FROM movimientos_inventario mi
            INNER JOIN lotes l ON mi.lote_id = l.id
            INNER JOIN plantas p ON l.planta_id = p.id
            LEFT JOIN destino d ON mi.destino_id = d.id
            WHERE mi.usuario_id = :usuario_id
            AND mi.tipo_movimiento = 'salida'
            AND mi.estado = 'activo'
            AND mi.destino_id IS NOT NULL
            AND DATE(mi.fecha) = CURDATE()
            ORDER BY mi.fecha DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalSalidasHoyUsuario()
    {
        $usuario_id = $_SESSION['usuario']['id'];

        $sql = "SELECT COALESCE(SUM(cantidad),0) AS total
            FROM movimientos_inventario
            WHERE usuario_id = :usuario_id
            AND tipo_movimiento = 'salida'
            AND estado = 'activo'
            AND destino_id IS NOT NULL
            AND DATE(fecha) = CURDATE()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function plantulasRecolectadasAnio($year)
    {
        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
          AND mi.motivo = 'Registro inicial'
          AND mi.etapa_id = 4
          AND l.tipo_material = 'plantula'

          AND YEAR(mi.fecha) = :year

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total DESC
    ");

        $stmt->bindParam(":year", $year);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function plantulasRecolectadasMes($year, $mes)
    {
        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
          AND mi.motivo = 'Registro inicial'
          AND mi.etapa_id = 4
          AND l.tipo_material = 'plantula'
          AND YEAR(mi.fecha) = :year
          AND MONTH(mi.fecha) = :mes

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total DESC
    ");

        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mes", $mes);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function plantulasRecolectadasCuatrimestre($year, $mesInicio, $mesFin)
    {
        $stmt = $this->conn->prepare("
        SELECT
            p.nombre_cientifico,
            SUM(mi.cantidad) AS total

        FROM movimientos_inventario mi

        INNER JOIN lotes l
            ON l.id = mi.lote_id

        INNER JOIN plantas p
            ON p.id = l.planta_id

        WHERE mi.tipo_movimiento = 'entrada'
          AND mi.motivo = 'Registro inicial'
          AND mi.etapa_id = 4
          AND l.tipo_material = 'plantula'

          AND YEAR(mi.fecha) = :year
          AND MONTH(mi.fecha) BETWEEN :mesInicio AND :mesFin

        GROUP BY p.id, p.nombre_cientifico

        ORDER BY total DESC
    ");

        $stmt->bindParam(":year", $year);
        $stmt->bindParam(":mesInicio", $mesInicio);
        $stmt->bindParam(":mesFin", $mesFin);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

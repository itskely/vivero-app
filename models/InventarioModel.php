<?php

class InventarioModel
{
    private $id;
    private $lote_id;
    private $etapa_id;
    private $ubicacion_id;
    private $cantidad_actual;
    private $conn = null;

    private $etapa;
    private $ubicacion;
    private $unidad;


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

    public function setEtapa($etapa)
    {
        $this->etapa = $etapa;
    }
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }
    public function setUnidad($unidad)
    {
        $this->unidad = $unidad;
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
    public function getEtapa()
    {
        return $this->etapa;
    }

    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    public function getUnidad()
    {
        return $this->unidad;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("
            SELECT 
                i.id,
                i.lote_id,
                l.unidad_medida,
                i.etapa_id,
                i.ubicacion_id,
                i.cantidad_actual,
                p.id AS planta_id,
                e.nombre AS etapa,
                p.nombre_comun,
                p.nombre_cientifico,
                u.nombre AS ubicacion,
                e.puede_alterar_unidad
            FROM inventario i
            INNER JOIN lotes l ON i.lote_id = l.id 
            INNER JOIN plantas p ON l.planta_id = p.id
            INNER JOIN etapas e ON i.etapa_id = e.id
            INNER JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE 
                i.cantidad_actual > 0
                AND (
                    i.lote_id LIKE :busqueda
                    OR p.nombre_comun LIKE :busqueda
                    OR p.nombre_cientifico LIKE :busqueda
                    OR e.nombre LIKE :busqueda
                    OR u.nombre LIKE :busqueda
                )
            ORDER BY
                e.nombre,
                p.nombre_comun,
                u.nombre
            LIMIT :lim OFFSET :offs");
        $busqueda = "%" . $this->getBusqueda() . "%";
        $limit = $this->getLimit();
        $offset = $this->getOffset();
        $stmt->bindParam(":busqueda", $busqueda);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCount()
    {
        $busqueda = $this->getBusqueda();
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM inventario i
            LEFT JOIN lotes l ON i.lote_id = l.id
            LEFT JOIN plantas p ON l.planta_id = p.id
            LEFT JOIN etapas e ON i.etapa_id = e.id
            LEFT JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE 
                i.cantidad_actual > 0
                AND (
                    p.nombre_comun LIKE :busqueda
                    OR p.nombre_cientifico LIKE :busqueda
                    OR e.nombre LIKE :busqueda
                    OR u.nombre LIKE :busqueda
                )");
        $param = "%" . $busqueda . "%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
    public function getCountCompleto()
    {
        $busqueda = $this->getBusqueda();
        $etapa = $this->getEtapa();
        $ubicacion = $this->getUbicacion();
        $unidad = $this->getUnidad();


        $stmt = $this->conn->prepare("
        SELECT COUNT(*) AS total
        FROM (
            SELECT 1
            FROM inventario i
            INNER JOIN lotes l ON i.lote_id = l.id
            INNER JOIN plantas p ON l.planta_id = p.id
            INNER JOIN etapas e ON i.etapa_id = e.id
            INNER JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE
                (
                    i.lote_id LIKE :busqueda
                    OR p.nombre_comun LIKE :busqueda
                    OR p.nombre_cientifico LIKE :busqueda
                    OR e.nombre LIKE :busqueda
                    OR u.nombre LIKE :busqueda
                )
                AND (:etapa = '' OR e.id = :etapa)
                AND (:ubicacion = '' OR u.id = :ubicacion)
                AND (:unidad = '' OR l.unidad_medida = :unidad)
                AND i.cantidad_actual > 0
            GROUP BY
                e.id,
                p.id,
                u.id,
                l.unidad_medida
        ) AS conteo
    ");

        $param = "%$busqueda%";

        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":etapa", $etapa);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":unidad", $unidad);


        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getTotales()
    {
        $busqueda = $this->getBusqueda();
        $etapa = $this->getEtapa();
        $ubicacion = $this->getUbicacion();
        $unidad = $this->getUnidad();


        $stmt = $this->conn->prepare("
        SELECT
            l.unidad_medida,
            SUM(i.cantidad_actual) AS total
        FROM inventario i
        INNER JOIN lotes l ON i.lote_id = l.id
        INNER JOIN plantas p ON l.planta_id = p.id
        INNER JOIN etapas e ON i.etapa_id = e.id
        INNER JOIN ubicaciones u ON i.ubicacion_id = u.id
        WHERE
            (
                i.lote_id LIKE :busqueda
                OR p.nombre_comun LIKE :busqueda
                OR p.nombre_cientifico LIKE :busqueda
                OR e.nombre LIKE :busqueda
                OR u.nombre LIKE :busqueda
            )
            AND (:etapa = '' OR e.id = :etapa)
            AND (:ubicacion = '' OR u.id = :ubicacion)
            AND (:unidad = '' OR l.unidad_medida = :unidad)
            AND i.cantidad_actual > 0
        GROUP BY l.unidad_medida
    ");

        $param = "%$busqueda%";

        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":etapa", $etapa);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":unidad", $unidad);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            WHERE i.lote_id LIKE :busqueda OR p.nombre_comun LIKE :busqueda OR p.nombre_cientifico LIKE :busqueda OR e.nombre LIKE :busqueda OR u.nombre LIKE :busqueda
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
        $etapa = $this->getEtapa();
        $ubicacion = $this->getUbicacion();
        $unidad = $this->getUnidad();
        $stmt = $this->conn->prepare("
            SELECT 
                e.nombre AS etapa,
                p.nombre_comun,
                p.nombre_cientifico,
                u.nombre AS ubicacion,
                l.unidad_medida,
                COUNT(DISTINCT l.id) AS numero_lotes,
                SUM(i.cantidad_actual) AS total_unidades
            FROM inventario i
            INNER JOIN lotes l ON i.lote_id = l.id
            INNER JOIN plantas p ON l.planta_id = p.id
            INNER JOIN etapas e ON i.etapa_id = e.id
            INNER JOIN ubicaciones u ON i.ubicacion_id = u.id
            WHERE 
               (i.lote_id LIKE :busqueda
                OR p.nombre_comun LIKE :busqueda
                OR p.nombre_cientifico LIKE :busqueda
                OR e.nombre LIKE :busqueda
                OR u.nombre LIKE :busqueda)
                AND (:etapa = '' OR e.id = :etapa)
                AND (:ubicacion = '' OR u.id = :ubicacion)
                AND (:unidad = '' OR l.unidad_medida = :unidad)
               AND i.cantidad_actual > 0
            GROUP BY
                e.id,
                p.id,
                u.id,
                l.unidad_medida
            ORDER BY
                e.id,
                CASE
                    WHEN u.nombre = 'Tingua' THEN 2
                    WHEN u.nombre = 'Arrieros' THEN 3
                    ELSE 4
                END,
                p.nombre_comun,
                l.unidad_medida
            LIMIT :lim OFFSET :offs;
        ");
        $param = "%$busqueda%";
        $stmt->bindParam(":busqueda", $param);
        $stmt->bindParam(":etapa", $etapa);
        $stmt->bindParam(":ubicacion", $ubicacion);
        $stmt->bindParam(":unidad", $unidad);
        $stmt->bindParam(":lim", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offs", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function inventarioRustificacion($ubicacion_id)
    {
        $sql = "SELECT 
                p.nombre_comun,
                SUM(i.cantidad_actual) AS cantidad
            FROM inventario i
            INNER JOIN lotes l ON l.id = i.lote_id
            INNER JOIN plantas p ON p.id = l.planta_id
            WHERE i.ubicacion_id = :ubicacion
              AND i.etapa_id = 6
              AND i.cantidad_actual > 0
            GROUP BY p.id, p.nombre_comun";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ubicacion', $ubicacion_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

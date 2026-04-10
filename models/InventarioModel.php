<?php

class InventarioModel
{
    private $id;
    private $lote_id;
    private $etapa_id;
    private $ubicacion_id;
    private $cantidad_actual;
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

    public function getIsHomePage()
    {
        $stmt = $this->conn->prepare("SELECT * FROM pages WHERE is_home = 1");
        $stmt->execute();
        $page = $stmt->fetch();
        return $page;
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("INSERT INTO roles(nombre) VALUES (:name)");
        $name = $this->getName();
        $stmt->bindParam(":name", $name);
        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE roles SET nombre=:name WHERE id = :id");
        $id = $this->getId();
        $name = $this->getName();

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $name);
        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM roles WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0)
        {
            return true;
        }
        return false;
    }

    public function getRolePages($role_id)
    {
        $stmt = $this->conn->prepare("SELECT page_id, role_id FROM role_pages WHERE role_id = :role_id");
        $stmt->bindParam(":role_id", $role_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insPageRole($page_id, $role_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO role_pages(page_id, role_id) VALUES (:page_id, :role_id)");
        $stmt->bindParam(":page_id", $page_id);
        $stmt->bindParam(":role_id", $role_id);
        return $stmt->execute();
    }

    public function delPageRole($role_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM role_pages WHERE role_id = :role_id");
        $stmt->bindParam(":role_id", $role_id);
        return $stmt->execute();
    }
}

<?php

class RoleModel
{
    private $id;
    private $nombre;
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

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM roles");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $page = $stmt->fetch();
        return $page;
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
        $stmt = $this->conn->prepare("INSERT INTO roles(nombre) VALUES (:nombre)");
        $nombre = $this->getNombre();
        $stmt->bindParam(":nombre", $nombre);
        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE roles SET nombre=:nombre WHERE id = :id");
        $id = $this->getId();
        $nombre = $this->getNombre();

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM roles WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
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

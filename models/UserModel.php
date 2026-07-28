<?php

class UserModel
{
    private $id;
    private $nombre_completo;
    private $cedula;
    private $password;
    private $rol;
    private $is_active;
    private $conn = null;

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombreCompleto($nombre_completo)
    {
        $this->nombre_completo = $nombre_completo;
    }

    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    }

    public function setPassword($pass)
    {
        $this->password = $pass;
    }

    public function setRol($rol)
    {
        $this->rol = $rol;
    }

    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getNombreCompleto()
    {
        return $this->nombre_completo;
    }

    public function getCedula()
    {
        return $this->cedula;
    }
    public function getRol()
    {
        return $this->rol;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT u.id AS id, u.nombre_completo AS nombre_completo, u.cedula AS cedula, u.id_rol AS id_rol, u.is_active AS is_active, u.fecha_registro AS fecha_registro, rol.nombre AS rol_nombre FROM usuarios AS u INNER JOIN roles AS rol ON u.id_rol = rol.id;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $usuario = $stmt->fetch();
        return $usuario;
    }

    public function getDocumento()
    {
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE cedula = :cedula");
        $cedula = $this->getCedula();
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();
        $usuario = $stmt->fetch();
        return $usuario;
    }

    public function crear()
    {
        $stmt = $this->conn->prepare("INSERT INTO usuarios(nombre_completo, cedula,password, id_rol, is_active) VALUES (:nombre_completo,:cedula,:password,:id_rol,:is_active)");
        $nombre = $this->getNombreCompleto();
        $cedula = $this->getCedula();
        $pass = $this->getPassword();
        $rol = $this->getRol();
        $is_active = $this->getIsActive();
        $stmt->bindParam(":nombre_completo", $nombre);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":password", $pass);
        $stmt->bindParam(":id_rol", $rol);
        $stmt->bindParam(":is_active", $is_active);
        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE usuarios SET nombre_completo=:nombre_completo, cedula=:cedula, password=:password, id_rol=:id_rol, is_active=:is_active WHERE id = :id");
        $id = $this->getId();
        $nombre = $this->getNombreCompleto();
        $cedula = $this->getCedula();
        $pass = $this->getPassword();
        $rol = $this->getRol();
        $is_active = $this->getIsActive();

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre_completo", $nombre);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":password", $pass);
        $stmt->bindParam(":id_rol", $rol);
        $stmt->bindParam(":is_active", $is_active);
        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM usuarios WHERE id=:id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}

<?php

class PageModel
{
    private $id;
    private $name;
    private $route;
    private $ord;
    private $icon;
    private $is_home;
    private $description;
    private $conn = null;

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    public function setRoute($route)
    {
        $this->route = $route;
    }
    public function setOrd($ord)
    {
        $this->ord = $ord;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    public function setIsHome($is_home)
    {
        $this->is_home = $is_home;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getOrd()
    {
        return $this->ord;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getIsHome()
    {
        return $this->is_home;
    }


    public function getDescription()
    {
        return $this->description;
    }

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("SELECT * FROM pages");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne()
    {
        $stmt = $this->conn->prepare("SELECT * FROM pages WHERE id = :id");
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
        $stmt = $this->conn->prepare("INSERT INTO pages(name, route, ord, icon, is_home, description) VALUES (:name,:route,:ord,:icon,:is_home,:description)");
        $name = $this->getName();
        $route = $this->getRoute();
        $ord = $this->getOrd();
        $icon = $this->getIcon();
        $is_home = $this->getIsHome();
        $description = $this->getDescription();
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":route", $route);
        $stmt->bindParam(":ord", $ord);
        $stmt->bindParam(":icon", $icon);
        $stmt->bindParam(":is_home", $is_home);
        $stmt->bindParam(":description", $description);
        return $stmt->execute();
    }

    public function update()
    {
        $stmt = $this->conn->prepare("UPDATE pages SET name=:name, route=:route, ord=:ord, icon=:icon, is_home=:is_home, description=:description WHERE id = :id");
        $id = $this->getId();
        $name = $this->getName();
        $route = $this->getRoute();
        $ord = $this->getOrd();
        $icon = $this->getIcon();
        $is_home = $this->getIsHome();
        $description = $this->getDescription();

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":route", $route);
        $stmt->bindParam(":ord", $ord);
        $stmt->bindParam(":icon", $icon);
        $stmt->bindParam(":is_home", $is_home);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete()
    {
        $stmt = $this->conn->prepare("DELETE FROM pages WHERE id = :id");
        $id = $this->getId();
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function validatePages($role_id)
    {
        $stmt = $this->conn->prepare("SELECT p.id AS id, p.name AS name, p.ord AS ord, p.icon as icon, p.is_home AS is_home, p.route AS route, p.description AS description FROM `roles` AS r INNER JOIN role_pages AS rp ON r.id = rp.role_id INNER JOIN pages AS p ON rp.page_id = p.id WHERE r.id = :role_id ORDER BY p.ord ASC");
        $stmt->bindParam(":role_id", $role_id);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($data) > 0) {
            return $data;
        }
        return null;
    }
}

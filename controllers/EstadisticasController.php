<?php
include __DIR__ . "/../models/EtapasModel.php";
$etapasModel = new EtapasModel();
$allEtapas = $etapasModel->getAll();

<?php
include __DIR__ . "/../models/PageModel.php";

$page = new PageModel();

$page_accessed_id = $_GET['page_id'] ?? null;

$pagesRoleUser = $page->validatePages($_SESSION['usuario']['id_rol']);
$pageFound = array_filter($pagesRoleUser, fn($pg) => "$pg[id]" === "$page_accessed_id");
$pageAccessed = reset($pageFound) ?: null;
$principalPage = $page->getIsHomePage();

<?php
include __DIR__ . "/../controllers/PlantaController.php";
?>




<section class="py-12 flex justify-center">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-4xl text-center flex flex-col items-center gap-6">


        <i class="fa-brands fa-pagelines"></i>
        <h1 class="text-4xl font-bold text-green-800 mb-2">¡Bienvenido a ViveroApp!</h1>
        <p class="text-lg text-black/80">
            ViveroApp es tu sistema integral para gestionar y conocer todas las plantas de tu vivero de manera eficiente y organizada.
            En esta plataforma, podrás registrar nuevas especies con información detallada, llevar control de inventario
            de cada etapa de crecimiento, registrar movimientos internos entre etapas, y gestionar entradas y salidas de manera segura.
            Además, podrás consultar estadísticas y generar informes.

        </p>

    </div>
</section>
<div class="mt-10 mb-4 text-center">
    <h2 class="text-3xl font-bold text-green-800">
        Catálogo de plantas – Vivero Ecoparque Sabana
    </h2>
    <p class="text-green-600 text-sm mt-2">
        Listado de especies registradas en el sistema del vivero
    </p>
</div>
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">

    <div>
        <form action="/home.php" method="get" class="p-4 max-w-sm">
            <input type="hidden" name="page_id" value="<?= $pageAccessed['id'] ?>">
            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 inset-s-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="search" id="search" name="busqueda" value="<?= $_GET['busqueda'] ?? "" ?>" class="block w-full p-6 ps-9  input-component placeholder=" Buscar..." />
                <button type="submit" class="absolute inset-e-1.5 bottom-1.5 text-primary-foreground bg-primary hover:bg-green-700 box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded text-xs px-3 py-1.5 focus:outline-none">Buscar</button>
            </div>
        </form>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
        <?php
        foreach ($allPlants as $pg) {

        ?>
            <div class="bg-neutral-primary-soft block max-w-sm border border-default rounded-base shadow-xs">
                <div class="max-h-40 h-full relative flex overflow-hidden">
                    <img class="rounded-t-base object-cover object-center w-full h-full" src="<?= $pg['imagen'] ? "/assets/uploads/$pg[imagen]" : "https://placehold.co/600x400" ?>" alt="" />
                </div>
                <div class="p-6 text-left">
                    <span class="inline-flex items-center bg-brand-softer border border-brand-subtle text-green-800 text-xs font-medium px-1.5 py-0.5 rounded-sm">
                        <svg class="w-3 h-3 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.122 17.645a7.185 7.185 0 0 1-2.656 2.495 7.06 7.06 0 0 1-3.52.853 6.617 6.617 0 0 1-3.306-.718 6.73 6.73 0 0 1-2.54-2.266c-2.672-4.57.287-8.846.887-9.668A4.448 4.448 0 0 0 8.07 6.31 4.49 4.49 0 0 0 7.997 4c1.284.965 6.43 3.258 5.525 10.631 1.496-1.136 2.7-3.046 2.846-6.216 1.43 1.061 3.985 5.462 1.754 9.23Z" />
                        </svg>
                        <?= $pg['nombre_cientifico'] ?>
                    </span>
                    <div>
                        <h5 class="mt-3 mb-6 text-2xl font-semibold tracking-tight text-heading"><?= $pg['nombre_comun'] ?></h5>
                        <p class="text-sm text-muted-foreground"><?= $pg['descripcion'] ?></p>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

    </div>
    <?php
    if ($totalPaginas > 1):
    ?>
        <div>
            <nav aria-label="Page navigation example" class="w-full flex p-4">
                <ul class="flex -space-x-px text-sm mx-auto">
                    <?php
                    if ($page <= 1) {
                    ?>
                        <button class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-s-base text-sm px-3 h-9 focus:outline-none disabled:opacity-50" disabled>
                            Previous
                        </button>
                    <?php
                    } else {
                    ?>
                        <a href="/home.php?page_id=<?= $pageAccessed['id']; ?>&page=<?= $page - 1 ?>" class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-s-base text-sm px-3 h-9 focus:outline-none">Previous</a>
                    <?php
                    }
                    ?>
                    <?php
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                    ?>
                        <li>
                            <?php
                            if ($i === $page) {
                            ?>
                                <a href="/home.php?page_id=<?= $pageAccessed['id']; ?>&page=<?= $i ?>" aria-current="page" class="flex items-center justify-center text-fg-brand bg-neutral-tertiary-medium box-border border border-default-medium hover:text-fg-brand font-medium text-sm w-9 h-9 focus:outline-none"><?= $i ?></a>
                            <?php
                            } else {
                            ?>
                                <a href="/home.php?page_id=<?= $pageAccessed['id']; ?>&page=<?= $i ?>" class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none"><?= $i ?></a>
                            <?php
                            }
                            ?>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <?php
                        if ($page >= $totalPaginas) {
                        ?>
                            <button class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-e-base text-sm px-3 h-9 focus:outline-none disabled:opacity-50" disabled>
                                Next
                            </button>
                        <?php
                        } else {
                        ?>
                            <a href="/home.php?page_id=<?= $pageAccessed['id']; ?>&page=<?= $page + 1 ?>" class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-e-base text-sm px-3 h-9 focus:outline-none">Next</a>
                        <?php
                        }
                        ?>
                    </li>
                </ul>
            </nav>
        </div>
    <?php
    endif;
    ?>
</div>
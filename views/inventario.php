<?php
include __DIR__ . "/../controllers/InventarioController.php";
?>

<div>
    <div class="p-4">
        <?php include("./views/layouts/header.php"); ?>

        <div class="mt-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <form method="GET" action="/home.php" class="w-full md:w-1/3 flex gap-2">
                <input type="hidden" name="page_id" value="<?= $pageAccessed['id'] ?>">
                <input type="text" name="busqueda" placeholder="Buscar especie, etapa, ubicación..."
                    value="<?= htmlspecialchars($busqueda ?? '') ?>" class="input-component w-full">
                <button type="submit" class="btn btn-default btn-size-default">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>
            </form>

            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>" class="btn btn-outline btn-size-default">
                Limpiar
            </a>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-xs rounded-base border mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>

                    <th scope="col" class="px-6 py-3 font-medium">Lote ID</th>
                    <th scope="col" class="px-6 py-3 font-medium">Planta</th>
                    <th scope="col" class="px-6 py-3 font-medium">Etapa</th>
                    <th scope="col" class="px-6 py-3 font-medium">Ubicación</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Cantidad</th>
                    <th scope="col" class="px-6 py-3 font-medium">Última actualización</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($allInventarios) > 0): ?>
                    <?php foreach ($allInventarios as $inv): ?>
                        <tr class="rounded-xl bg-card border hover:bg-neutral-secondary-hover transition-colors">

                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                #<?= htmlspecialchars($inv['lote_id']) ?>
                            </th>
                            <td class="px-6 py-4">
                                <div class="font-medium text-heading"><?= htmlspecialchars($inv['nombre_comun']) ?></div>
                                <div class="text-xs text-muted-foreground italic"><?= htmlspecialchars($inv['nombre_cientifico']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                    <?= htmlspecialchars($inv['etapa']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($inv['ubicacion']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold">
                                <?= htmlspecialchars(number_format($inv['cantidad_actual'], 2)) ?>
                                <span class="text-xs font-normal text-muted-foreground"><?= htmlspecialchars($inv['unidad_medida']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?= htmlspecialchars(date('d/m/Y H:i', strtotime($inv['ultima_actualizacion']))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-muted-foreground">
                            No se encontraron registros de inventario que coincidan con la búsqueda.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <div class="flex justify-between items-center mt-4 px-4">
            <span class="text-sm text-muted-foreground">
                Mostrando <?= min($offset + 1, $totalRegistros) ?> a <?= min($offset + $limit, $totalRegistros) ?> de <?= $totalRegistros ?> resultados
            </span>
            <div class="flex gap-1">
                <?php if ($page > 1): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page - 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-outline btn-size-small">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $i ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-size-small <?= $i === $page ? 'bg-brand text-white border-brand' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPaginas): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page + 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-outline btn-size-small">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
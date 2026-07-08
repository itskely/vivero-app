<?php
include __DIR__ . "/../controllers/InventarioController.php";
?>

<div>
    <div class="p-4">
        <?php include("./views/layouts/header.php"); ?>

        <div class="mt-4">

            <div class="bg-white rounded-lg shadow p-5">

                <form method="GET" action="/home.php" class="space-y-4">

                    <input type="hidden" name="page_id" value="<?= $pageAccessed['id'] ?>">
                    <div>
                        <input type="text"
                            name="busqueda"
                            placeholder="Buscar especie, etapa, ubicación..."
                            value="<?= htmlspecialchars($busqueda ?? '') ?>"
                            class="input-component w-full">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block mb-1 font-medium">
                                Etapa
                            </label>

                            <select name="etapa" class="input-component w-full">

                                <option value="">Todas las etapas</option>

                                <?php foreach ($allEtapas as $e): ?>

                                    <option value="<?= $e['id'] ?>"
                                        <?= (($_GET['etapa'] ?? '') == $e['id']) ? 'selected' : '' ?>>

                                        <?= $e['nombre'] ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div>

                            <label class="block mb-1 font-medium">
                                Ubicación
                            </label>

                            <select name="ubicacion" class="input-component w-full">

                                <option value="">Todas las ubicaciones</option>

                                <?php foreach ($allUbicaciones as $u): ?>

                                    <option value="<?= $u['id'] ?>"
                                        <?= (($_GET['ubicacion'] ?? '') == $u['id']) ? 'selected' : '' ?>>

                                        <?= $u['nombre'] ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="block mb-1 font-medium">
                                Unidad de medida
                            </label>

                            <select name="unidad" class="input-component w-full">

                                <option value="">Todas</option>

                                <option value="gramos"
                                    <?= (($_GET['unidad'] ?? '') == 'gramos') ? 'selected' : '' ?>>
                                    Gramos
                                </option>

                                <option value="unidades"
                                    <?= (($_GET['unidad'] ?? '') == 'unidades') ? 'selected' : '' ?>>
                                    Unidades
                                </option>

                            </select>

                        </div>

                        <div class="flex items-end">

                            <label class="flex items-center gap-2">

                                <input type="checkbox"
                                    name="disponibles"
                                    value="1"
                                    <?= isset($_GET['disponibles']) ? 'checked' : '' ?>>

                                Ocultar registros con cantidad 0

                            </label>

                        </div>

                    </div>
                    <div class="flex gap-3">

                        <button type="submit"
                            class="btn btn-default btn-size-default">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            Buscar

                        </button>

                        <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>"
                            class="btn btn-outline btn-size-default">

                            Limpiar

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <div class="relative overflow-x-auto shadow-xs rounded-base border mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>

                    <th scope="col" class="px-6 py-3 font-medium">etapa </th>
                    <th scope="col" class="px-6 py-3 font-medium">especie</th>
                    <th scope="col" class="px-6 py-3 font-medium">Ubicación</th>
                    <th scope="col" class="px-6 py-3 font-medium">Unidad de medida </th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">lotes</th>
                    <th scope="col" class="px-6 py-3 font-medium">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($allInventarios) > 0): ?>
                    <?php foreach ($allInventarios as $inv): ?>
                        <tr class="rounded-xl bg-card border hover:bg-neutral-secondary-hover transition-colors">
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                <?= htmlspecialchars($inv['etapa']) ?>
                            </th>
                            <td class="px-6 py-4">
                                <div class="font-medium text-heading"><?= htmlspecialchars($inv['nombre_comun']) ?></div>
                                <span class="text-xs text-muted-foreground"><?= htmlspecialchars($inv['nombre_cientifico']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                    <?= htmlspecialchars($inv['ubicacion']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($inv['unidad_medida']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold">
                                <?= htmlspecialchars($inv['numero_lotes']) ?>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <?= htmlspecialchars(number_format($inv['total_unidades'])) ?>
                                <span class="text-xs font-normal text-muted-foreground"><?= htmlspecialchars($inv['unidad_medida']) ?></span>
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
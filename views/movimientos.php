<?php
include __DIR__ . "/../controllers/AuditoriaController.php";
?>

<div>
    <!-- SUCCESS -->

    <?php
    if (isset($_SESSION["success"])) {
    ?>
        <script>
            toast({
                message: "<?= $_SESSION["success"] ?>",
                position: "top-right"
            });
        </script>
    <?php
        unset($_SESSION["success"]);
    }
    ?>

    <?php
    if (isset($_SESSION["error"])) {
    ?>
        <script>
            toast({
                message: "<?= $_SESSION["error"] ?>",
                position: "top-right"
            });
        </script>
    <?php
        unset($_SESSION["error"]);
    }
    ?>
    <div class="p-4 space-y-4">
        <?php include("./views/layouts/header.php"); ?>

        <div class="mt-4">

            <div class="bg-white rounded-lg shadow p-5">

                <form id="formBusqueda" method="GET" action="/home.php" class="space-y-4">
                    <input type="hidden" name="page_id" value="<?= $pageAccessed['id'] ?>">
                    <div>
                        <input type="text"
                            name="busqueda"
                            placeholder="Buscar especie, etapa, ubicación, motivo..."
                            value="<?= htmlspecialchars($busqueda ?? '') ?>"
                            class="input-component w-full">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block mb-1 font-medium">
                                Fecha inicio
                            </label>

                            <input type="date"
                                name="fecha_inicio"
                                value="<?= $_GET['fecha_inicio'] ?? '' ?>"
                                class="input-component w-full">
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">
                                Fecha fin
                            </label>

                            <input type="date"
                                name="fecha_fin"
                                value="<?= $_GET['fecha_fin'] ?? '' ?>"
                                class="input-component w-full">
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="block mb-1 font-medium">
                                Tipo de movimiento
                            </label>

                            <select name="tipo" class="input-component w-full">

                                <option value="">Todos</option>

                                <option value="entrada"
                                    <?= (($_GET['tipo'] ?? '') == 'entrada') ? 'selected' : '' ?>>
                                    Entrada
                                </option>

                                <option value="salida"
                                    <?= (($_GET['tipo'] ?? '') == 'salida') ? 'selected' : '' ?>>
                                    Salida
                                </option>

                            </select>

                        </div>

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

                    </div>

                    <div>

                        <label class="flex items-center gap-2">

                            <input type="checkbox"
                                name="salidas_vivero"
                                value="1"
                                <?= isset($_GET['salidas_vivero']) ? 'checked' : '' ?>>

                            <span>Solo salidas del vivero</span>

                        </label>

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

                        <button class="btn btn-default btn-size-default" onclick="submitForm()">
                            Excel
                        </button>

                    </div>

                </form>

                <script>
                    const boton = document.getElementById("miBoton");

                    function submitForm() {
                        const formBusqueda = document.getElementById("formBusqueda");
                        formBusqueda.addEventListener("submit", (e) => {
                            e.preventDefault();

                            const formData = new FormData(formBusqueda);

                            const data = Object.fromEntries(formData);

                            // Armar linea get para enviar a /api/excel.php
                            const url = new URLSearchParams(data);
                            window.location.href = "/api/excel.php?" + url.toString();
                        }, {
                            once: true
                        });
                    }
                </script>

            </div>

        </div>

    </div>


    <!-- <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <div>
            <h1 class="text-xl font-semibold">Movimientos de Inventario</h1>
            <p class="text-muted-foreground text-sm">Historial completo de entradas, salidas y ajustes</p>
        </div>
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th class="px-6 py-3">Lote</th>
                    <th class="px-6 py-3">Tipo</th>
                    <th class="px-6 py-3">Cantidad</th>
                    <th class="px-6 py-3">Etapa</th>
                    <th class="px-6 py-3">Ubicación</th>
                    <th class="px-6 py-3">Motivo</th>
                    <th class="px-6 py-3">Fecha</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos ?? [] as $m): ?>
                    <tr class="border-b">
                        <td class="px-6 py-4"><?= $m['lote'] ?></td>
                        <td class="px-6 py-4"><?= $m['etapa'] ?></td>
                        <td class="px-6 py-4"><?= $m['ubicacion'] ?></td>
                        <td class="px-6 py-4"><?= $m['fecha'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> -->

    <div class="rounded-md border">
        <div class="overflow-x-auto custom-scrollbar">
            <div style="min-width: 100%; display: table;">
                <table class="w-full">
                    <thead class="bg-accent">
                        <tr>
                            <th class="p-4 text-left font-medium text-muted-foreground">Lote</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Tipo</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Tipo material</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Cantidad</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Etapa</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Ubicación</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Origen</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Destino</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Motivo</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Fecha</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Usuario</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allMovimientosInventario as $m):
                        ?>
                            <tr
                                class="border-b transition-colors hover:bg-muted/50 <?= $m["estado"] == "anulado" ? "opacity-60 bg-destructive/10 [&>*]:line-through" : "" ?>">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <?php if ($m['tipo_movimiento'] == 'salida'): ?>
                                            <!-- Salida -->
                                            <div class="p-2 rounded-full text-red-600 bg-red-50"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-circle-arrow-up h-4 w-4" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <path d="m16 12-4-4-4 4"></path>
                                                    <path d="M12 16V8"></path>
                                                </svg>
                                            </div>
                                        <?php elseif ($m['tipo_movimiento'] == 'entrada'): ?>
                                            <!-- Entrada -->
                                            <div class="p-2 rounded-full text-emerald-600 bg-emerald-50"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-circle-arrow-down h-4 w-4" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <path d="M12 8v8"></path>
                                                    <path d="m8 12 4 4 4-4"></path>
                                                </svg>
                                            </div>
                                        <?php else: ?>
                                            <!-- Otro (ajuste) -->
                                            <div class="p-2 rounded-full text-amber-600 bg-amber-50"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-refresh-cw h-4 w-4" aria-hidden="true">
                                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                                    <path d="M21 3v5h-5"></path>
                                                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                                    <path d="M8 16H3v5"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-medium">Lote #<?= $m['lote_id'] ?></p>
                                            <p class="text-xs text-muted-foreground"><?= $m['nombre_comun'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">

                                    <?php if ($m['tipo_movimiento'] == "entrada"): ?>
                                        <span data-slot="badge" data-variant="secondary"
                                            class="group/badge inline-flex h-5 w-fit shrink-0 items-center justify-center gap-1 overflow-hidden rounded-4xl border border-transparent px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-all focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&amp;&gt;svg]:pointer-events-none [&amp;&gt;svg]:size-3! bg-secondary text-secondary-foreground [a]:hover:bg-secondary/80">
                                            <?= ucfirst($m['tipo_movimiento']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span data-slot="badge" data-variant="outline"
                                            class="group/badge inline-flex h-5 w-fit shrink-0 items-center justify-center gap-1 overflow-hidden rounded-4xl border px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-all focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&amp;&gt;svg]:pointer-events-none [&amp;&gt;svg]:size-3! border-border text-foreground [a]:hover:bg-muted [a]:hover:text-muted-foreground">
                                            <?= ucfirst($m['tipo_movimiento']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $m['tipo_material'] ?>
                                </td>
                                <td class="p-4"><span
                                        class="font-semibold tabular-nums <?= $m['tipo_movimiento'] == 'salida' ? 'text-red-600' : 'text-primary' ?>"><?= $m['tipo_movimiento'] == 'salida' ? "-" : "+" ?><?= (floor($m['cantidad']) == $m['cantidad']) ? intval($m['cantidad']) : $m['cantidad'] ?>
                                        <?= $m['unidad_medida'] ?></span></td>
                                <td class="p-4"><span
                                        class="inline-flex items-center font-medium rounded-full border border-primary bg-primary/10 text-primary px-2 py-0.5 text-xs"><span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 bg-primary"></span><?= $m['nombre_etapa'] ?></span>
                                </td>
                                <td class="p-4"><span class="text-sm"><?= $m['nombre_ubicacion'] ?></span></td>
                                <td class="p-4"><span class="text-sm">
                                        <?= $m['nombre_origen'] ?>
                                    </span>
                                </td>
                                <td class="p-4"><span class="text-sm">
                                        <?= $m['nombre_destino'] ?? "<span class='italic text-muted-foreground text-xs'>N/A</span>" ?>
                                    </span>
                                </td>
                                <td class="p-4"><span class="text-sm text-muted-foreground"><?= $m['motivo'] ?></span>
                                </td>
                                <td class="p-4"><span class="text-sm text-muted-foreground"><?= $m['fecha'] ?></span>
                                </td>
                                <td class="p-4">
                                    <span class="text-sm">
                                        <?= htmlspecialchars($m['usuario']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <button id="dropdownDefaultButton-<?= $m['id'] ?>"
                                        data-dropdown-toggle="dropdown-<?= $m['id'] ?>"
                                        class="btn btn-ghost btn-size-default" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-ellipsis h-4 w-4"
                                            aria-hidden="true">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg>
                                    </button>

                                    <!-- Dropdown menu -->
                                    <div id="dropdown-<?= $m['id'] ?>"
                                        class="z-10 hidden bg-card border rounded shadow-lg w-48">
                                        <ul class="p-2 text-sm text-muted-foreground font-medium"
                                            aria-labelledby="dropdownDefaultButton-<?= $m['id'] ?>">
                                            <form method="post">
                                                <input type="hidden" name="anulacion_id" value="<?= $m['id'] ?>">
                                                <li>
                                                    <button type="submit"
                                                        class="inline-flex items-center w-full p-2 hover:bg-destructive/30 hover:text-destructive rounded">
                                                        <i class="fa-solid fa-ban mr-2"></i>
                                                        Anular movimiento
                                                    </button>
                                                </li>
                                            </form>
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Paginación -->
<div class="flex justify-between items-center mt-4 px-4">
    <span class="text-sm text-muted-foreground">
        Mostrando <?= min($offset + 1, $totalRegistros) ?> a <?= min($offset + $limit, $totalRegistros) ?> de <?= $totalRegistros ?> resultados
    </span>
    <div class="flex gap-1">
        <?php if ($page > 1): ?>
            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page - 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?><?= $tipo ? '&tipo=' . urlencode($tipo) : '' ?><?= $etapa_id ? '&etapa=' . urlencode($etapa_id) : '' ?><?= $fecha_inicio ? '&fecha_inicio=' . urlencode($fecha_inicio) : '' ?><?= $fecha_fin ? '&fecha_fin=' . urlencode($fecha_fin) : '' ?><?= $salidas_vivero ? '&salidas_vivero=' . urlencode($salidas_vivero) : '' ?>"
                class="btn btn-outline btn-size-default">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $i ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?><?= $tipo ? '&tipo=' . urlencode($tipo) : '' ?><?= $etapa_id ? '&etapa=' . urlencode($etapa_id) : '' ?><?= $fecha_inicio ? '&fecha_inicio=' . urlencode($fecha_inicio) : '' ?><?= $fecha_fin ? '&fecha_fin=' . urlencode($fecha_fin) : '' ?><?= $salidas_vivero ? '&salidas_vivero=' . urlencode($salidas_vivero) : '' ?>"
                class="btn btn-size-default <?= $i === $page ? 'btn-default' : 'btn-outline' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPaginas): ?>
            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page + 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?><?= $tipo ? '&tipo=' . urlencode($tipo) : '' ?><?= $etapa_id ? '&etapa=' . urlencode($etapa_id) : '' ?><?= $fecha_inicio ? '&fecha_inicio=' . urlencode($fecha_inicio) : '' ?><?= $fecha_fin ? '&fecha_fin=' . urlencode($fecha_fin) : '' ?><?= $salidas_vivero ? '&salidas_vivero=' . urlencode($salidas_vivero) : '' ?>"
                class="btn btn-outline btn-size-default">Siguiente</a>
        <?php endif; ?>
    </div>
</div>


<script src="/assets/scripts/auditoria.js"></script>
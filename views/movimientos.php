<?php
include __DIR__ . "/../controllers/AuditoriaController.php";
?>

<div>
    <!-- SUCCESS -->

    <?php
    if (isset($_SESSION["success"]))
    {
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
    if (isset($_SESSION["error"]))
    {
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
                            <th class="p-4 text-left font-medium text-muted-foreground">Cantidad</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Etapa</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Ubicación</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Origen</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Motivo</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Fecha</th>
                            <th class="p-4 text-left font-medium text-muted-foreground">Estado</th>
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
                                            <p class="font-medium"><?= $m['codigo_lote'] ?></p>
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
                                <td class="p-4"><span
                                        class="font-semibold tabular-nums <?= $m['tipo_movimiento'] == 'salida' ? 'text-red-600' : 'text-primary' ?>"><?= $m['tipo_movimiento'] == 'salida' ? "-" : "+" ?><?= $m['cantidad'] ?>
                                        <?= $m['unidad_medida'] ?></span></td>
                                <td class="p-4"><span
                                        class="inline-flex items-center font-medium rounded-full border border-primary bg-primary/10 text-primary px-2 py-0.5 text-xs"><span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5 bg-primary"></span><?= $m['nombre_etapa'] ?></span>
                                </td>
                                <td class="p-4"><span class="text-sm"><?= $m['nombre_ubicacion'] ?></span></td>
                                <td class="p-4"><span class="text-sm">
                                        <?= $m['nombre_origen'] ?>
                                    </span></td>
                                <td class="p-4"><span class="text-sm text-muted-foreground"><?= $m['motivo'] ?></span>
                                </td>
                                <td class="p-4"><span class="text-sm text-muted-foreground"><?= $m['fecha'] ?></span>
                                </td>
                                <td class="p-4">
                                    <?php if ($m['estado'] == "activo"): ?>
                                        <span data-slot="badge" data-variant="outline"
                                            class="group/badge inline-flex h-5 w-fit shrink-0 items-center justify-center gap-1 overflow-hidden rounded-4xl border px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-all focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&amp;&gt;svg]:pointer-events-none [&amp;&gt;svg]:size-3! [a]:hover:bg-muted [a]:hover:text-muted-foreground text-emerald-600 border-emerald-300 bg-emerald-50">
                                            <?= $m['estado'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span data-slot="badge" data-variant="destructive"
                                            class="group/badge inline-flex h-5 w-fit shrink-0 items-center justify-center overflow-hidden rounded-4xl border border-transparent px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-all focus-visible:border-ring focus-visible:ring-[3px] has-data-[icon=inline-end]:pr-1.5 has-data-[icon=inline-start]:pl-1.5 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&amp;&gt;svg]:pointer-events-none [&amp;&gt;svg]:size-3! bg-destructive/10 text-destructive focus-visible:ring-destructive/20 dark:bg-destructive/20 dark:focus-visible:ring-destructive/40 [a]:hover:bg-destructive/20 gap-1"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="lucide lucide-ban h-3 w-3" aria-hidden="true">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M4.929 4.929 19.07 19.071"></path>
                                            </svg>
                                            <?= $m['estado'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4"><button data-slot="dropdown-menu-trigger" data-variant="ghost"
                                        data-size="icon"
                                        class="group/button inline-flex shrink-0 items-center justify-center rounded-lg border border-transparent bg-clip-padding text-sm font-medium whitespace-nowrap transition-all outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:not-aria-[haspopup]:translate-y-px disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 [&amp;_svg]:pointer-events-none [&amp;_svg]:shrink-0 [&amp;_svg:not([class*='size-'])]:size-4 hover:bg-muted hover:text-foreground aria-expanded:bg-muted aria-expanded:text-foreground dark:hover:bg-muted/50 size-8 h-8 w-8"
                                        type="button" id="radix-_r_f_" aria-haspopup="menu" aria-expanded="false"
                                        data-state="closed"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-ellipsis h-4 w-4" aria-hidden="true">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg></button></td>
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

<script src="/assets/scripts/auditoria.js"></script>
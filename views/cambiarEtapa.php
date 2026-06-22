<?php
include __DIR__ . "/../controllers/CambioEtapasController.php";
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
    <div class="p-4 space-y-4" x-data="{ open: false }">
        <?php include("./views/layouts/header.php"); ?>

        <div class="max-w-4xl w-full p-6 rounded-xl bg-card border space-y-6">
            <form method="POST" class="space-y-6" id="form-cambiar-etapa">


                <?= Form::input("hidden", "lote_id", "lote_id", "", "", "") ?>
                <?= Form::input("hidden", "inventario_id", "inventario_id", "", "", "") ?>

                <!-- Lote -->
                <div class=" relative">
                    <button id="lote_button"
                        class="btn btn-outline px-2.5 h-auto min-h-10 text-muted-foreground w-full justify-between py-2"
                        role="combobox" aria-expanded="false" type="button" aria-haspopup="dialog"
                        aria-controls="radix-_r_j8_" data-state="closed">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-search h-4 w-4" aria-hidden="true">
                                <path d="m21 21-4.34-4.34"></path>
                                <circle cx="11" cy="11" r="8"></circle>
                            </svg>
                            <div class="flex flex-col items-start justify-baseline">
                                <span data-title>Buscar lote por código...</span>
                                <span data-subtitle class="text-xs"></span>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevrons-up-down ml-2 h-4 w-4 shrink-0 opacity-50" aria-hidden="true">
                            <path d="m7 15 5 5 5-5"></path>
                            <path d="m7 9 5-5 5 5"></path>
                        </svg>
                    </button>

                    <div
                        class="bg-card border rounded-xl p-2 absolute top-full left-0 right-0 mt-2 max-w-md space-y-2 hidden max-h-96 overflow-y-auto custom-scrollbar">
                        <div class="sticky top-0 z-10 bg-card">
                            <div class="flex items-center gap-2 relative">

                                <input id="lotes-search-input" type="text" placeholder="Buscar lote..."
                                    class="input-component bg-muted!">

                                <!-- Spinner -->
                                <div id="lotes-spinner"
                                    class="hidden size-4 rounded-full border-2 border-primary/50 border-t-transparent animate-spin absolute right-2 ">
                                </div>
                            </div>
                        </div>

                        <div id="lotes-container" class="space-y-2">
                            <template id="template-option">
                                <div data-slot="lote-item"
                                    class="group/command-item relative cursor-pointer gap-2 rounded-sm px-2 text-sm outline-hidden select-none in-data-[slot=dialog-content]:rounded-lg! data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50 data-selected:bg-muted data-selected:text-foreground [&amp;_svg]:pointer-events-none [&amp;_svg]:shrink-0 [&amp;_svg:not([class*='size-'])]:size-4 data-selected:*:[svg]:text-foreground flex items-center justify-between py-3 hover:bg-muted"
                                    role="option" aria-selected="true" data-disabled="false" data-value>
                                    <div class="flex items-center gap-3">
                                        <svg data-selected xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-check h-4 w-4 opacity-0" aria-hidden="true">
                                            <path d="M20 6 9 17l-5-5"></path>
                                        </svg>
                                        <div class="flex flex-col"><span class="font-medium" data-title></span><span
                                                class="text-xs text-muted-foreground" data-subtitle></span></div>
                                    </div>
                                    <!-- <div class="flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center font-medium rounded-full border bg-emerald-100 text-emerald-700 border-emerald-300 px-2 py-0.5 text-xs">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-emerald-500"></span>
                                                <span data-etapa></span>
                                            </span>
                                            <span class="text-sm font-medium tabular-nums" data-cantidad>430</span>
                                        </div> -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-check ml-auto opacity-0 group-has-data-[slot=command-shortcut]/command-item:hidden group-data-[checked=true]/command-item:opacity-100"
                                        aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5"></path>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div id="available-stock" class="p-4 rounded-lg bg-muted/50 border space-y-3 hidden">
                    <h4 class="font-medium text-sm">Stock disponible por ubicación:</h4>
                    <div class="grid gap-2" id="stock-container">
                        <template id="template-stock">
                            <button data-stock-item type="button"
                                class="flex items-center justify-between p-3 rounded-lg border transition-colors border-border hover:border-primary/50">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center font-medium rounded-full border bg-emerald-100 text-emerald-700 border-emerald-300 px-2 py-0.5 text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-emerald-500" data-dot></span>
                                        <span data-etapa></span>
                                    </span>
                                    <span class="text-sm text-muted-foreground" data-ubicacion></span>
                                </div>
                                <span class="font-semibold tabular-nums" data-cantidad></span>
                            </button>
                        </template>
                    </div>
                </div>
                <div class="space-y-2 border-l pl-4 ">
                    <button type="button" class="btn btn-ghost btn-size-default whitespace-normal" x-on:click="open = ! open"
                        x-text="open ? 'Ocultar' : 'Agregar unidad de medida para la nueva etapa'">
                    </button>
                    <!-- Select con unidades de mnedidas (kilogramos, gramos y unidades) -->
                    <div x-show="open" class="space-y-2">
                        <label for="unidad_medida">Unidad de medida</label>
                        <select name="unidad_medida" id="unidad_medida" class="input-component">
                            <option value="" selected disabled>Seleccione una unidad de medida</option>
                            <option value="kilogramos">Kilogramos</option>
                            <option value="gramos">Gramos</option>
                            <option value="unidades">Unidades</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="p-4 border rounded-xl bg-green-50 dark:bg-green-600/20 space-y-3 mt-2">
                        <p class="text-green-600 font-medium"> Destino (Entra)</p>
                        <div>
                            <label class="block mb-2 text-sm">Etapa destino</label>
                            <select name="etapa_destino_id" class="input-component" id="etapa_destino_id" required>
                                <option disabled selected>Seleccionar...</option>
                                <?php foreach ($allEtapas as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= $e['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm">Ubicacion destino</label>
                            <select name="ubi_destino_id" class="input-component" id="ubi_destino_id" required>
                                <option disabled selected>Seleccionar...</option>
                                <?php foreach ($allUbicaciones as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= $e['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="space-y-2">
                        <?= Form::input("number", "cantidad_salida", "cantidad_salida", "0", "Cantidad a mover(sale)") ?>
                        <p class="text-muted-foreground text-sm">Máximo: <span data-max-salida></span></p>
                    </div>

                    <div class="space-y-2">

                        <?= Form::input("number", "cantidad_entrada", "cantidad_entrada", "0", "Cantidad que sobrevive(entra)") ?>
                        <p class="text-muted-foreground text-sm">Si hay merma, ingresa
                            un valor menor</p>
                    </div>

                </div>

                <div id="alert-success"
                    class="hidden p-4 rounded-lg border bg-emerald-50 border-emerald-200 dark:bg-emerald-600/20 dark:border-emerald-600">
                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-check h-5 w-5 text-emerald-600 shrink-0 mt-0.5"
                            aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-medium text-sm text-emerald-700">Sin Merma</h4>
                            <div class="mt-2 space-y-1">
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Cantidad
                                        que
                                        sale:</span><span class="font-medium tabular-nums" data-stock-out></span>
                                </div>
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Cantidad
                                        que
                                        entra:</span><span class="font-medium tabular-nums" data-stock-in></span>
                                </div>
                                <div class="border-t border-dashed mt-2 pt-2">
                                    <div class="flex justify-between text-sm"><span
                                            class="font-medium text-emerald-700">Merma:</span><span
                                            class="font-bold tabular-nums text-emerald-700">0 Unidades
                                            (0.0%)</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="alert-merma"
                    class="hidden p-4 rounded-lg border bg-amber-50 border-amber-200 dark:bg-amber-600/20 dark:border-amber-600">
                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-trending-down h-5 w-5 text-amber-600 shrink-0 mt-0.5"
                            aria-hidden="true">
                            <path d="M16 17h6v-6"></path>
                            <path d="m22 17-8.5-8.5-5 5L2 7"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-medium text-sm text-amber-700">Merma Detectada</h4>
                            <div class="mt-2 space-y-1">
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Cantidad
                                        que
                                        sale:</span><span class="font-medium tabular-nums" data-stock-out></span>
                                </div>
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Cantidad
                                        que
                                        entra:</span><span class="font-medium tabular-nums" data-stock-in></span>
                                </div>
                                <div class="border-t border-dashed mt-2 pt-2">
                                    <div class="flex justify-between text-sm"><span
                                            class="font-medium text-amber-700">Merma:</span><span
                                            class="font-bold tabular-nums text-amber-700" data-stock-merma></span>
                                    </div>
                                </div>
                                <div class="h-2 bg-amber-400 rounded-full mt-2" data-stock-merma-porcentaje
                                    style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="alert-error"
                    class="hidden p-4 rounded-lg border bg-red-50 border-red-200 dark:bg-red-600/20 dark:border-red-600">
                    <div class="flex items-start gap-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-triangle-alert h-5 w-5 text-red-600 shrink-0 mt-0.5"
                            aria-hidden="true">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3">
                            </path>
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-medium text-sm text-red-700">Valores Inválidos</h4>
                            <p class="text-sm text-red-600 mt-1">La cantidad que entra no puede ser mayor a la que
                                sale ni negativa.</p>
                        </div>
                    </div>
                </div>

                <div id="alert-stock-error"
                    class="group/alert hidden relative gap-2 w-full rounded-lg border px-2.5 py-2 text-left text-sm has-data-[slot=alert-action]:relative has-data-[slot=alert-action]:pr-18 has-[&gt;svg]:grid-cols-[auto_1fr] has-[&gt;svg]:gap-x-2 *:[svg]:row-span-2 *:[svg]:translate-y-0.5 *:[svg:not([class*='size-'])]:size-4 bg-card text-destructive *:data-[slot=alert-description]:text-destructive/90 *:[svg]:text-current">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-circle-alert h-4 w-4" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" x2="12" y1="8" y2="12"></line>
                        <line x1="12" x2="12.01" y1="16" y2="16"></line>
                    </svg>
                    <div data-slot="alert-description"
                        class="text-sm text-balance text-muted-foreground md:text-pretty [&amp;_a]:underline [&amp;_a]:underline-offset-3 [&amp;_a]:hover:text-foreground [&amp;_p:not(:last-child)]:mb-4">
                        La cantidad a mover (<span data-stock-out-error></span>) excede el stock disponible (<span
                            data-stock-available-error></span>).</div>
                </div>


                <?= Form::textarea("observaciones", "observaciones", "", "Observaciones", "", false) ?>



                <div class="flex justify-end gap-2 mt-3">


                    <button id="submit-form" type="submit" class="btn btn-default btn-size-default">Crear</button>
                </div>

            </form>

        </div>
    </div>


    <!-- <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th class="px-6 py-3">Lote</th>
                    <th class="px-6 py-3">Origen</th>
                    <th class="px-6 py-3">Destino</th>
                    <th class="px-6 py-3">Cantidad</th>
                    <th class="px-6 py-3">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php // foreach ($movimientos ?? [] as $m): 
                ?>
                    <tr class="border-b">
                        <td class="px-6 py-4"><?= $m['lote'] ?></td>
                        <td class="px-6 py-4"><?= $m['origen'] ?></td>
                        <td class="px-6 py-4"><?= $m['destino'] ?></td>
                        <td class="px-6 py-4"><?= $m['cantidad'] ?></td>
                        <td class="px-6 py-4"><?= $m['fecha'] ?></td>
                    </tr>
                <?php // endforeach; 
                ?>
            </tbody>
        </table>
    </div> -->
</div>
<script src="/assets/scripts/cambiarEtapa.js"></script>
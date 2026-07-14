<?php
include __DIR__ . "/../controllers/SalidasController.php";
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
            <form method="POST" class="space-y-6" id="form-salidas">


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

                <div class="space-y-2 hidden">
                    <label for="tipo_movimiento" class="block text-sm font-medium">Tipo de movimiento</label>
                    <select name="tipo_movimiento" id="tipo_movimiento" class="input-component" required>
                        <option value="" disabled>Seleccionar tipo de movimiento</option>
                        <?php
                        if ($_SESSION['usuario']["id_rol"] != 9) {
                        ?>
                            <!-- <option value="entrada" selected>Entrada</option> -->
                        <?php
                        }
                        ?>
                        <option value="salida" selected>Salida</option>
                    </select>
                </div>

                <!-- Stock disponible -->
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="space-y-2">
                        <?= Form::input("number", "cantidad_salida", "cantidad_salida", "0", "Cantidad") ?>
                        <p class="text-muted-foreground text-sm">Máximo: <span data-max-salida></span></p>
                    </div>

                    <!-- Select con los destinos iterados -->
                    <div class="space-y-2">
                        <label for="destino_id" class="block text-sm font-medium">Destino</label>
                        <select name="destino_id" id="destino_id" class="input-component" required>
                            <option value="" disabled selected>Seleccionar destino</option>
                            <?php foreach ($allDestinos as $destino): ?>
                                <option value="<?= $destino['id'] ?>"><?= $destino['nombre_destino'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?= Form::textarea("observaciones", "observaciones", "", "Observaciones", "", false) ?>

                <div class="flex justify-end gap-2 mt-3">
                    <button type="button" @click="open = false" class="btn btn-outline btn-size-default">
                        limpíar
                    </button>

                    <button id="submit-form" type="submit" class="btn btn-default btn-size-default">Crear</button>
                </div>

            </form>

        </div>
    </div>


    <h3 class="text-lg font-semibold mt-6 mb-3">
        Mis últimas salidas
    </h3>
    <div class="w-fit mb-4 p-4 rounded-base border bg-accent">
        <strong>Total hoy:</strong><br>

        <?php if ($totalUnidades > 0): ?>
            <?= number_format($totalUnidades, 0) ?> unidades<br>
        <?php endif; ?>

        <?php if ($totalGramos > 0): ?>
            <?= number_format($totalGramos, 2) ?> gramos
        <?php endif; ?>
    </div>
    <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Planta
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Cantidad
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Destino
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Motivo
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Fecha
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($misSalidas as $salida): ?>
                    <tr class="bg-neutral-primary border-b border-default">

                        <th scope="row"
                            class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $salida['planta'] ?>
                        </th>

                        <td class="px-6 py-4">
                            <?= $salida['cantidad'] ?>
                        </td>

                        <td class="px-6 py-4">
                            <?= $salida['destino'] ?>
                        </td>

                        <td class="px-6 py-4">
                            <?= $salida['motivo'] ?>
                        </td>

                        <td class="px-6 py-4">
                            <?= date('d/m/Y H:i', strtotime($salida['fecha'])) ?>
                        </td>

                    </tr>
                <?php endforeach; ?>

                <?php if (empty($misSalidas)): ?>
                    <tr class="bg-neutral-primary border-b border-default">
                        <td colspan="5" class="px-6 py-4 text-center">
                            No has registrado salidas todavía.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

    </div>
    <script src="/assets/scripts/salidas.js"></script>
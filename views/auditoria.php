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
                                <span data-title>Buscar por etapa,especie...</span>
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



                <div class="grid gap-4">

                    <div class="space-y-2">
                        <?= Form::input("number", "cantidad_real", "cantidad_real", "0", "Cantidad Física Real (Conteo)") ?>
                        <p class="text-muted-foreground text-sm">Ingresa la cantidad real contada físicamente en la
                            ubicación</p>
                    </div>

                </div>

                <div id="alert-audit" class="p-4 hidden rounded-lg border">
                    <div class="flex items-start gap-3">
                        <div data-icon>

                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-sm" data-title>Stock Real Menor - Diferencia
                                Significativa
                            </h4>
                            <div class="mt-3 space-y-2">
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Stock en
                                        Sistema:</span><span class="font-medium tabular-nums" data-stock-sistema></span>
                                </div>
                                <div class="flex justify-between text-sm"><span class="text-muted-foreground">Stock Real
                                        (Conteo):</span><span class="font-medium tabular-nums" data-stock-real></span>
                                </div>
                                <div class="border-t border-dashed pt-2">
                                    <div class="flex justify-between text-sm"><span
                                            class="font-medium text-foreground">Diferencia:</span><span
                                            class="font-bold tabular-nums" data-diferencia></span>
                                    </div>
                                </div>
                                <p class="text-xs mt-2 p-2 rounded" data-message>Se
                                    registrará una SALIDA de 50 Unidades para ajustar el inventario.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <?= Form::textarea("observaciones", "observaciones", "", "Observaciones de auditoría", "Describe la razón del ajuste, hallazgos durante la auditoría, etc...", false) ?>
                    <p class="text-muted-foreground text-sm">Obligatorio para auditoría. Documenta la razón de la
                        diferencia.</p>
                </div>

                <div class="flex justify-end gap-2 mt-3">
                    <button id="submit-form" type="submit" class="btn btn-default btn-size-default">Registrar
                        ajuste</button>
                </div>

            </form>

        </div>
    </div>



</div>

<script src="/assets/scripts/auditoria.js"></script>
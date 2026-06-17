<?php
include __DIR__ . "/../controllers/LotesController.php";
?>

<div>

    <?php include("./views/layouts/header.php"); ?>




    <div class="max-w-4xl w-full p-6 rounded-xl bg-card border space-y-6">
        <div>
            <h1 class="text-xl font-semibold">Nuevo Lote</h1>
            <p class="text-muted-foreground text-sm"></p>
        </div>

        <form method="POST" class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-2">
            <div>
                <label for="planta_id" class="block mb-2.5 text-sm font-medium">Planta</label>
                <select id="planta_id" name="planta_id" class="input-component" required>
                    <option value="" selected disabled>Seleccionar planta</option>
                    <?php foreach ($allPlantas as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre_comun'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tipo_material"
                    class="block mb-2.5 text-sm font-medium">

                    Tipo de Material
                </label>

                <select id="tipo_material"
                    name="tipo_material"
                    class="input-component"
                    required>

                    <option value="" selected disabled>
                        Seleccionar tipo de material
                    </option>

                    <option value="semilla">
                        Semilla
                    </option>

                    <option value="plantula">
                        Plántula
                    </option>

                    <option value="esqueje">
                        Esqueje
                    </option>
                    <option value="planta">
                        Planta
                    </option>

                </select>
            </div>
            <div>
                <label for="unidad_medida" class="block mb-2.5 text-sm font-medium">Unidad de Medida</label>
                <select id="unidad_medida" name="unidad_medida" class="input-component" required>
                    <option value="" selected disabled>Seleccionar unidad de medida</option>
                    <?php foreach ($unidades_medida as $p): ?>
                        <option class="capitalize" value="<?= $p ?>">
                            <?= $p ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?= Form::input("number", "cantidad", "cantidad", "", "Cantidad Inicial", "0") ?>
            <div>
                <label for="etapa_id" class="block mb-2.5 text-sm font-medium">Etapa Inicial</label>
                <select id="etapa_id" name="etapa_id" class="input-component" required>
                    <option value="" selected disabled>Seleccionar etapa</option>
                    <?php foreach ($allEtapas as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="ubicacion_id" class="block mb-2.5 text-sm font-medium">Ubicación</label>
                <select id="ubicacion_id" name="ubicacion_id" class="input-component" required>
                    <option value="" selected disabled>Seleccionar ubicación</option>
                    <?php foreach ($allUbicaciones as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="origen_id" class="block mb-2.5 text-sm font-medium">Origen</label>
                <select id="origen_id" name="origen_id" class="input-component" required>
                    <option value="" selected disabled>Seleccionar origen</option>

                    <?php foreach ($allOrigen as $o): ?>
                        <option value="<?= $o['id'] ?>">
                            <?= $o['nombre_origen'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-span-2">
                <?= Form::textarea("observaciones", "observaciones", "", "Observaciones", "Notas adicionales sobre el lote...") ?>
            </div>

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

            <div class="flex items-center justify-end gap-2 col-span-2">
                <button type="submit" class="btn btn-default btn-size-default">
                    Registrar Lote
                </button>
            </div>
        </form>
    </div>






</div>
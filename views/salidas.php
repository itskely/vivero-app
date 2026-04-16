<?php
include __DIR__ . "/../controllers/LotesController.php";
?>

<div>
    <div class="p-4" x-data="{ open: false }">
        <?php include("./views/layouts/header.php"); ?>

        

        <div class="max-w-4xl w-full p-6 rounded-xl bg-card border space-y-6 mt-4" >
            
                <div>
                    <div>
                        <h1 class="text-xl font-semibold">Registrar salida</h1>
                        <p class="text-muted-foreground text-sm"></p>
                    </div>
                </div>
                <form method="POST" class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        <label for="planta_id" class="block mb-2.5 text-sm font-medium">Planta</label>
                        <select id="planta_id" name="planta_id" class="input-component" required>
                            <option value="" selected disabled>Seleccionar planta</option>
                            <?php foreach ($allPlantas as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $oneLote && $oneLote['planta_id'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= $p['nombre_comun'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="destino_id" class="block mb-2.5 text-sm font-medium">Destino</label>
                        <select id="destino_id" name="destino_id" class="input-component" required>
                            <option value="" selected disabled>Seleccionar destino</option>
                            <?php foreach ($allDestinos as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $oneLote && $oneLote['destino_id'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= $p['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="unidad_medida" class="block mb-2.5 text-sm font-medium">Unidad de Medida</label>
                        <select id="unidad_medida" name="unidad_medida" class="input-component" required>
                            <option value="" selected disabled>Seleccionar unidad de medida</option>
                            <?php foreach ($unidades_medida as $p): ?>
                                <option class="capitalize" value="<?= $p ?>" <?= $oneLote && $oneLote['unidad_medida'] == $p ? 'selected' : '' ?>>
                                    <?= $p ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?= Form::input("number", "cantidad", "cantidad", "", "Cantidad que sale", "0") ?>
                    <div>
                        <label for="etapa_id" class="block mb-2.5 text-sm font-medium">Etapa final</label>
                        <select id="etapa_id" name="etapa_id" class="input-component" required>
                            <option value="" selected disabled>Seleccionar etapa</option>
                            <?php foreach ($allEtapas as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $oneLote && $oneLote['etapa_id'] == $p['id'] ? 'selected' : '' ?>>
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
                                <option value="<?= $p['id'] ?>" <?= $oneLote && $oneLote['ubicacion_id'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= $p['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <?= Form::textarea("observaciones", "observaciones", $oneLote ? $oneLote['observaciones'] : "", "Observaciones", "Notas adicionales sobre la salida...") ?>
                    </div>

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

                    <div class="flex items-center justify-end gap-2 col-span-2">
                        <button type="submit" class="btn btn-default btn-size-default">
                            Registrar salida
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
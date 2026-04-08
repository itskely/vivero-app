<div>
    <div class="p-4" x-data="{ open: false }">

        <?php include("./views/layouts/header.php"); ?>


        <div class="flex justify-end">
            <button @click="open = !open" class="btn btn-default btn-size-default">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                Registrar movimiento
            </button>
        </div>


        <div x-show="open" x-transition @click="open = false" class="fixed inset-0 z-50 bg-black/10 backdrop-blur-xs flex justify-center items-center">
            <div @click.stop class="max-w-4xl w-full p-6 rounded-xl bg-card border space-y-6 max-h-screen overflow-auto custom-scrollbar">
                <div>
                    <h1 class="text-xl font-semibold">Cambiar Etapa de Lote</h1>
                    <p class="text-muted-foreground text-sm">
                        Mueve plantas de una etapa a otra con control de mermas.
                    </p>
                </div>


                <form method="POST" class="space-y-6">


                    <?= Form::input("text", "
                    lote", "lote", "", "Buscar lote", "Código del lote") ?>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


                        <div class="p-4 border rounded-xl bg-destructive/10 space-y-3 mt-2">
                            <p class="text-red-600 font-medium">Origen (Sale)</p>
                            <div>
                                <label class="block mb-2 text-sm">Etapa Origen</label>
                                <select name="etapa_origen" class="input-component">
                                    <option disabled selected>Seleccionar...</option>
                                    <?php foreach ($allEtapas as $e): ?>
                                        <option value="<?= $e['id'] ?>">
                                            <?= $e['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm">Ubicacion Origen</label>
                                <select name="etapa_origen" class="input-component">
                                    <option disabled selected>Seleccionar...</option>
                                    <?php foreach ($allEtapas as $e): ?>
                                        <option value="<?= $e['id'] ?>">
                                            <?= $e['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>


                        <div class="p-4 border rounded-xl bg-green-50 dark:bg-green-600/20 space-y-3 mt-2">
                            <p class="text-green-600 font-medium"> Destino (Entra)</p>
                            <div>
                                <label class="block mb-2 text-sm">Etapa destino</label>
                                <select name="etapa_origen" class="input-component">
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
                                <select name="etapa_origen" class="input-component">
                                    <option disabled selected>Seleccionar...</option>
                                    <?php foreach ($allEtapas as $e): ?>
                                        <option value="<?= $e['id'] ?>">
                                            <?= $e['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>

                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <?= Form::input("number", "cantidad_salida", "cantidad_salida", "0", "Cantidad a mover(sale)") ?>

                        <?= Form::input("number", "cantidad_entrada", "cantidad_entrada", "0", "Cantidad que sobrevive(entra)") ?>

                    </div>


                    <?= Form::textarea("observaciones", "observaciones", "", "Observaciones") ?>


                    <div class="flex justify-end gap-2 mt-3">
                        <button type="button" @click="open = false"
                            class="btn btn-outline btn-size-default">
                            limpíar
                        </button>

                        <button type="submit" class="btn btn-default btn-size-default">Crear</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="relative overflow-x-auto shadow-xs rounded-base border">
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
                <?php foreach ($movimientos ?? [] as $m): ?>
                    <tr class="border-b">
                        <td class="px-6 py-4"><?= $m['lote'] ?></td>
                        <td class="px-6 py-4"><?= $m['origen'] ?></td>
                        <td class="px-6 py-4"><?= $m['destino'] ?></td>
                        <td class="px-6 py-4"><?= $m['cantidad'] ?></td>
                        <td class="px-6 py-4"><?= $m['fecha'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
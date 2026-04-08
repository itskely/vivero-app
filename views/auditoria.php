<div>
    <div class="p-4" x-data="{ open: false }">

        <?php include("./views/layouts/header.php"); ?>


        <div class="flex justify-end">
            <button @click="open = !open" class="btn btn-default btn-size-default">

                Registrar nueva auditoria
            </button>
        </div>


        <div x-show="open" x-transition @click="open = false"
            class="fixed inset-0 z-50 bg-black/10 backdrop-blur-xs flex justify-center items-center">

            <div @click.stop class="max-w-4xl w-full p-6 rounded-xl bg-card border space-y-6">


                <div>
                    <h1 class="text-xl font-semibold">Ajuste de Auditoría</h1>
                    <p class="text-muted-foreground text-sm">
                        Concilia el inventario físico con el sistema
                    </p>
                </div>


                <form method="POST" class="space-y-6">


                    <?= Form::input("text", "lote", "lote", "", "lote", "Lote a auditar") ?>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



                        <div>
                            <label class="block mb-2 text-sm">Etapa</label>
                            <select name="etapa_origen" class="input-component">
                                <option disabled selected>Seleccionar etapa</option>
                                <?php foreach ($allEtapas as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= $e['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm">Ubicacion </label>
                            <select name="etapa_origen" class="input-component">
                                <option disabled selected>Seleccionar ubicación</option>
                                <?php foreach ($allEtapas as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= $e['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>







                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <?= Form::input("number", "cantidad_salida", "cantidad_salida", "0", "Cantidad fisica real (conteo)",) ?>


                    </div>



                    <?= Form::textarea("observaciones", "observaciones", "", "Observaciones de auditoria") ?>


                    <div class="flex justify-end gap-2 mt-3">
                        <button type="button" @click="open = false"
                            class="btn btn-outline btn-size-default">
                            limpíar
                        </button>

                        <button type="submit" class="btn btn-default btn-size-default">guardar auditoria</button>
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
                    <th class="px-6 py-3">Etapa</th>
                    <th class="px-6 py-3">Ubicación</th>
                    <th class="px-6 py-3">Fecha</th>
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
    </div>
</div>
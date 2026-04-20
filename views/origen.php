<?php
include __DIR__ . "/../controllers/OrigenController.php";
?>

<div>
    <div class="p-4">
        <?php include("./views/layouts/header.php"); ?>

        <form method="POST" class="max-w-sm mx-auto space-y-4">
            <?=
                Form::input(
                    "text",
                    "nombre",
                    "nombre",
                    $oneOrigen ? $oneOrigen['nombre_origen'] : "",
                    "Nombre"
                );
            ?>
            <?=
                Form::textarea(
                    "descripcion",
                    "descripcion",
                    $oneOrigen ? $oneOrigen['descripcion'] : "",
                    "Descripción"
                );
            ?>
            <div>
                <label for="tipo" class="block mb-2.5 text-sm font-medium">Tipo</label>
                <select id="tipo" name="tipo" class="input-component" required>
                    <option value="" selected disabled>Seleccionar tipo</option>
                    <?php foreach ($tipo as $t): ?>
                        <option value="<?= $t ?>" <?= $oneOrigen && $oneOrigen['tipo'] == $t ? 'selected' : '' ?>><?= $t ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>



            <button type="submit" class="btn btn-default btn-size-default">Crear</button>
            <?php
            if ($oneOrigen):
                ?>
                <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>"
                    class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Cancelar</a>
                <?php
            endif;
            ?>
            <?php
            if (isset($_SESSION["success"]))
            {
                ?>
                <div class="p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                    <?= $_SESSION["success"] ?>
                </div>
                <?php
                unset($_SESSION["success"]);
            }
            ?>

            <?php
            if (isset($_SESSION["error"]))
            {
                ?>
                <div class="p-4 mb-4 text-sm text-fg-danger-strong rounded-base bg-danger-soft" role="alert">
                    <?= $_SESSION["error"] ?>
                </div>
                <?php
                unset($_SESSION["error"]);
            }
            ?>
        </form>


    </div>


    <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Descripcion
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Tipo
                    </th>

                    <th scope="col" class="px-6 py-3 font-medium">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($allOrigen as $or)
                {
                    ?>
                    <tr class="bg-neutral-primary border-b border-default">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $or['id'] ?>
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $or['nombre_origen'] ?>
                        </th>
                        <td class="px-6 py-4">
                            <?= $or['descripcion'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $or['tipo'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&id=<?= $or['id'] ?>"
                                class="font-medium text-blue-600 hover:underline"><i
                                    class="fa-regular fa-pen-to-square"></i>Editar</a>
                            <button onclick="function eliminar() {
                                    if (confirm('¿Estás seguro de que deseas eliminar este origen?')) {
                                        window.location.href = '/home.php?page_id=<?= $pageAccessed['id'] ?>&delete_id=<?= $or['id'] ?>';
                                    }
                                }
                                eliminar()" class="font-medium text-red-600 hover:underline ms-4 cursor-pointer">
                                <i class="fa-regular fa-trash-can"></i>
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

</div>
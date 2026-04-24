<?php
include __DIR__ . "/../controllers/PagesController.php";
?>

<div>
    <div class="p-4" x-data="{ open: <?= $onePage ? "true" : "false" ?> }">
        <?php include("./views/layouts/header.php"); ?>

        <div class="flex justify-end">

            <button @click="open = !open" class="btn btn-default btn-size-default">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="size-6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Página
            </button>
        </div>

        <div x-show="open" x-transition @click="open = !open" class="fixed inset-0 z-50 bg-black/10 backdrop-blur-xs flex justify-center items-center">
            <div @click.stop class="max-w-xl w-full p-4 rounded-xl bg-card border space-y-4">
                <div>

                    <div>
                        <h1 class="text-xl font-semibold">Nueva página</h1>
                        <p class="text-muted-foreground text-sm">Llena todos los campos requeridos y envia el formulario para generar una nueva vista.</p>
                    </div>
                </div>
                <form method="POST" class="space-y-4">
                    <?=
                    Form::input(
                        "text",
                        "name",
                        "name",
                        $onePage ? $onePage['name'] : "",
                        "Nombre de la página"
                    );
                    ?>
                    <?=
                    Form::input(
                        "text",
                        "route",
                        "route",
                        $onePage ? $onePage['route'] : "",
                        "Ruta de la página"
                    );
                    ?>
                    <?=
                    Form::input(
                        "number",
                        "ord",
                        "ord",
                        $onePage ? $onePage['ord'] : "",
                        "Orden"
                    );
                    ?>
                    <?=
                    Form::input(
                        "text",
                        "icon",
                        "icon",
                        $onePage ? $onePage['icon'] : "",
                        "Ícono"
                    );
                    ?>

                    <?=
                    Form::textarea(
                        "description",
                        "description",
                        $onePage ? $onePage['description'] : "",
                        "Descripción"
                    );
                    ?>
                    <div class="flex items-center gap-2">
                        <label for="is_home" class="text-sm font-medium text-heading">Página de inicio</label>
                        <input
                            type="checkbox"
                            id="is_home"
                            name="is_home"
                            class="checkbox-component"
                            <?= $onePage && $onePage['is_home'] ? 'checked' : '' ?> />
                    </div>
                    <button type="submit" class="btn btn-default btn-size-default">Crear</button>
                    <?php
                    if ($onePage):
                    ?>
                        <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>" class="btn btn-outline btn-size-default">Cancelar</a>
                    <?php
                    endif;
                    ?>
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
                </form>
            </div>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Descripción
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Ruta
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Orden
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Icono
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Es Página principal
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Acciones
                    </th>
                </tr>
            </thead>    
            <tbody>
                <?php
                foreach ($allPages as $pg) {
                ?>
                    <tr class="rounded-xl bg-card border ">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $pg['name'] ?>
                        </th>
                        <td class="px-6 py-4">
                            <?= $pg['description'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $pg['route'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $pg['ord'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <i class="<?= $pg['icon'] ?>"></i>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            if (!$pg['is_home']) {
                            ?>
                                <span class="bg-danger-soft text-fg-danger-strong text-xs font-medium px-1.5 py-0.5 rounded">No</span>

                            <?php
                            } else {
                            ?>
                                <span class="bg-success-soft text-fg-success-strong text-xs font-medium px-1.5 py-0.5 rounded">Si</span>
                            <?php
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&id=<?= $pg['id'] ?>" class="font-medium text-blue-600 hover:underline"><i class="fa-regular fa-pen-to-square"></i>Editar</a>
                            <button
                                onclick="function eliminar() {
                                    if (confirm('¿Estás seguro de que deseas eliminar esta página?')) {
                                        window.location.href = '/home.php?page_id=<?= $pageAccessed['id'] ?>&delete_id=<?= $pg['id'] ?>';
                                    }
                                }
                                eliminar()"
                                class="font-medium text-red-600 hover:underline ms-4 cursor-pointer">
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
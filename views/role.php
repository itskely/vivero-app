<?php
include __DIR__ . "/../controllers/RolesController.php";
?>

<div>
    <div class="p-4">
        <?php include("./views/layouts/header.php"); ?>

        <form method="POST" class="max-w-sm mx-auto space-y-4">
            <?=
            Form::input(
                "text",
                "name",
                "name",
                $oneRole ? $oneRole['name'] : "",
                "Nombre del rol"
            );
            ?>
            <button type="submit" class="btn btn-default btn-size-default">Enviar</button>
            <?php
            if ($oneRole):
            ?>
                <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>" class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Cancelar</a>
            <?php
            endif;
            ?>
            <?php
            if (isset($_SESSION["success"])) {
            ?>
                <div class="p-4 mb-4 text-sm text-fg-success-strong rounded-base bg-success-soft" role="alert">
                    <?= $_SESSION["success"] ?>
                </div>
            <?php
                unset($_SESSION["success"]);
            }
            ?>

            <?php
            if (isset($_SESSION["error"])) {
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
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($allRoles as $rl) {
                    $rolePages = $role->getRolePages($rl['id']);
                ?>
                    <tr class="bg-card border-b">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $rl['id'] ?>
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $rl['nombre'] ?>
                        </th>
                        <td class="px-6 py-4 flex items-center gap-4">
                            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&id=<?= $rl['id'] ?>" class="font-medium text-blue-600 hover:underline">
                                <i class="fa-regular fa-pen-to-square"></i>
                                Editar
                            </a>
                            <button
                                onclick="function eliminar() {
                                    if (confirm('¿Estás seguro de que deseas eliminar este rol?')) {
                                        window.location.href = '/home.php?page_id=<?= $pageAccessed['id'] ?>&delete_id=<?= $rl['id'] ?>';
                                    }
                                }
                                eliminar()"
                                class="font-medium text-destructive hover:underline cursor-pointer">
                                <i class="fa-regular fa-trash-can"></i>
                                Eliminar
                            </button>

                            <!-- Modal toggle -->
                            <button data-modal-target="default-modal-<?= $rl['id'] ?>" data-modal-toggle="default-modal-<?= $rl['id'] ?>" class="font-medium text-green-800 hover:underline" type="button">
                                <i class="fa-regular fa-eye"></i>
                                Permisos
                            </button>

                            <form id="permissions-form-<?= $rl['id'] ?>" method="POST">
                                <input type="hidden" name="role_id" value="<?= $rl['id'] ?>">

                                <!-- Main modal -->
                                <div id="default-modal-<?= $rl['id'] ?>" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                    <div class="relative p-4 w-full max-w-2xl max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6">
                                            <!-- Modal header -->
                                            <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                                                <h3 class="text-lg font-medium text-heading">
                                                    Permisos por Páginas para el rol - <strong><?= $rl['nombre'] ?></strong>
                                                </h3>
                                                <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal-<?= $rl['id'] ?>">
                                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <div class="space-y-4 md:space-y-6 py-4 md:py-6 grid  grid-cols-2">
                                                <?php
                                                foreach ($allPages as $pg):
                                                ?>
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            name="pages[]"
                                                            value="<?= $pg['id'] ?>"
                                                            class="sr-only peer"
                                                            <?= in_array($pg['id'], array_column($rolePages, 'page_id')) ? "checked" : "" ?>>
                                                        <div class="relative w-9 h-5 bg-neutral-quaternary peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-soft dark:peer-focus:ring-brand-soft rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-buffer after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                                        <span class="select-none ms-3 text-sm font-medium text-heading"><?= $pg['name'] ?></span>
                                                    </label>
                                                <?php
                                                endforeach;
                                                ?>
                                            </div>
                                            <!-- Modal footer -->
                                            <div class="flex items-center border-t border-default space-x-4 pt-4 md:pt-5">
                                                <button type="submit" class="btn btn-default btn-size-default">Guardar</button>
                                                <button data-modal-hide="default-modal-<?= $rl['id'] ?>" type="button" class="btn btn-outline btn-size-default">Cancelar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

</div>
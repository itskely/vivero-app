<?php
include __DIR__ . "/../controllers/UsersController.php";
?>

<div>
    <div class="p-4" x-data="{ open: <?= $oneUser ? "true" : "false" ?> }">
        <?php include("./views/layouts/header.php"); ?>

        <div class="flex justify-end">

            <button @click="open = !open" class="btn btn-default btn-size-default">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5}
                    stroke="currentColor" className="size-6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Usuario
            </button>
        </div>

        <div x-show="open" x-transition @click="open = !open"
            class="fixed inset-0 z-50 bg-black/10 backdrop-blur-xs flex justify-center items-center">
            <div @click.stop class="max-w-xl w-full p-4 rounded-xl bg-card border space-y-4">
                <div>

                    <div>
                        <h1 class="text-xl font-semibold">Nuevo Usuario</h1>
                        <p class="text-muted-foreground text-sm">Llena todos los campos requeridos y envia el formulario
                            para generar un nuevo usuario.</p>
                    </div>
                </div>

                <form method="POST" class="max-w-sm mx-auto space-y-4">
                    <div>
                        <label for="nombre_completo" class="block mb-2.5 text-sm font-medium text-heading">Nombre
                            completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" class="input-component" required
                            value="<?= $oneUser ? $oneUser['nombre_completo'] : "" ?>" />
                    </div>
                    <div>
                        <label for="cedula" class="block mb-2.5 text-sm font-medium text-heading">Cédula</label>
                        <input type="text" id="cedula" name="cedula" class="input-component" required
                            value="<?= $oneUser ? $oneUser['cedula'] : "" ?>" />
                    </div>
                    <div>
                        <label for="password" class="block mb-2.5 text-sm font-medium text-heading">Contraseña</label>
                        <input type="password" id="password" name="password" class="input-component" value="" />
                    </div>
                    <div>
                        <label for="id_rol" class="block mb-2.5 text-sm font-medium text-heading">Rol</label>
                        <select id="id_rol" name="id_rol" class="input-component">
                            <?php foreach ($allRoles as $rol): ?>
                                <option value="<?= $rol['id'] ?>" <?= $oneUser && $oneUser['id_rol'] == $rol['id'] ? 'selected' : '' ?>>
                                    <?= $rol['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-default btn-size-default">Crear</button>
                    <?php
                    if ($oneUser):
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
        </div>
    </div>


    <div class="rounded-xl bg-card border">
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
                        Cédula
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Rol
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Fecha de creación
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($allUsers as $user)
                {
                    ?>
                    <tr class="rounded-xl bg-card border">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $user['id'] ?>
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $user['nombre_completo'] ?>
                        </th>
                        <td class="px-6 py-4">
                            <?= $user['cedula'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $user['rol_nombre'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $user['fecha_registro'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&id=<?= $user['id'] ?>"
                                class="font-medium text-blue-600 hover:underline"><i
                                    class="fa-regular fa-pen-to-square"></i>Editar</a>
                            <button onclick="function eliminar() {
                                    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                                        window.location.href = '/home.php?page_id=<?= $pageAccessed['id'] ?>&delete_id=<?= $user['id'] ?>';
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
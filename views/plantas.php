<?php
include __DIR__ . "/../controllers/PlantaController.php";
?>



<div>
    <div class="p-4" x-data="{ open: <?= $onePlanta ? "true" : "false" ?> }">
        <?php include("./views/layouts/header.php"); ?>

        <div class="flex justify-end">

            <button @click="open = !open" class="btn btn-default btn-size-default">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5}
                    stroke="currentColor" className="size-6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva especie
            </button>
        </div>

        <div x-show="open" x-transition @click="open = !open"
            class="fixed inset-0 z-50 bg-black/10 backdrop-blur-xs flex justify-center items-center">
            <div @click.stop class="max-w-xl w-full p-4 rounded-xl bg-card border space-y-4">
                <div>

                    <div>
                        <h1 class="text-xl font-semibold">Nueva especie</h1>
                        <p class="text-muted-foreground text-sm">Llena todos los campos requeridos y envia el formulario
                            para registrar una nueva especie.</p>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto space-y-4">
                    <div>
                        <label for="nombre_cientifico" class="block mb-2.5 text-sm font-medium text-heading">Nombre
                            cientifico</label>
                        <input type="text" id="nombre_cientifico" name="nombre_cientifico" class="input-component"
                            required value="<?= $onePlanta ? $onePlanta['nombre_cientifico'] : "" ?>" />
                    </div>
                    <div>
                        <label for="nombre_comun" class="block mb-2.5 text-sm font-medium text-heading">Nombre
                            común</label>
                        <input type="text" id="nombre_comun" name="nombre_comun" class="input-component" required
                            value="<?= $onePlanta ? $onePlanta['nombre_comun'] : "" ?>" />
                    </div>

                    <div>
                        <label class="block mb-2.5 text-sm font-medium text-heading" for="file_input">imagen</label>
                        <input
                            class="cursor-pointer bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full shadow-xs placeholder:text-body"
                            id="file_input" type="file" name="imagen">
                        <div id="image-div"
                            class="relative mx-auto my-2 overflow-hidden items-center justify-center rounded-full size-20 border-green-400 border-2 hidden">
                            <img id="image-preview" src="" alt="" class="bg-center bg-cover">
                        </div>
                    </div>
                    <?=
                    Form::textarea(
                        "descripcion",
                        "descripcion",
                        $onePlanta ? $onePlanta['descripcion'] : "",
                        "Descripción"
                    );
                    ?>

                    <button type="submit" class="btn btn-default btn-size-default">Registrar</button>
                    <?php
                    if ($onePlanta):
                    ?>
                        <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>"
                            class="text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Cancelar</a>
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
        </div>
    </div>


    <div class="relative overflow-x-auto shadow-xs rounded-base border">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm bg-accent border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nombre científico
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Nombre común
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Imagen
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Descripción
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Acciones
                    </th>

                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($allPlants as $pg) {

                ?>
                    <tr class="rounded-xl bg-card border">
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $pg['id'] ?>
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= $pg['nombre_cientifico'] ?>
                        </th>
                        <td class="px-6 py-4">
                            <?= $pg['nombre_comun'] ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            if ($pg['imagen']):
                            ?>
                                <div
                                    class="relative mx-auto my-2 overflow-hidden items-center justify-center rounded-full size-20 border-green-500 border-2">
                                    <img src="/assets/uploads/<?= $pg['imagen'] ?>" alt="<?= $pg['nombre_comun'] ?>"
                                        class="object-center object-cover w-full h-full">
                                </div>
                            <?php
                            else:
                            ?>
                                <span class="italic text-xs">Sin imagen</span>
                            <?php
                            endif;
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= $pg['descripcion'] ?>
                        </td>


                        <td class="px-6 py-4">
                            <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&id=<?= $pg['id'] ?>"
                                class="font-medium text-blue-600 hover:underline"><i
                                    class="fa-regular fa-pen-to-square"></i>Editar</a>
                            <button onclick="function eliminar() {
                                    if (confirm('¿Estás seguro de que deseas eliminar esta planta?')) {
                                        window.location.href = '/home.php?page_id=<?= $pageAccessed['id'] ?>&delete_id=<?= $pg['id'] ?>';
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

    <?php if ($totalPaginas > 1): ?>
        <div class="flex justify-between items-center mt-4 px-4">
            <span class="text-sm text-muted-foreground">
                Mostrando <?= min($offset + 1, $totalRegistros) ?> a <?= min($offset + $limit, $totalRegistros) ?> de <?= $totalRegistros ?> resultados
            </span>

            <div class="flex gap-1">

                <?php if ($page > 1): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page - 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-outline btn-size-default">
                        Anterior
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $i ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-size-default <?= $i == $page ? 'btn-default' : 'btn-outline' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPaginas): ?>
                    <a href="/home.php?page_id=<?= $pageAccessed['id'] ?>&page=<?= $page + 1 ?><?= $busqueda ? '&busqueda=' . urlencode($busqueda) : '' ?>"
                        class="btn btn-outline btn-size-default">
                        Siguiente
                    </a>
                <?php endif; ?>

            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    const inputFile = document.getElementById('file_input')
    const imageDiv = document.getElementById('image-div')
    const imagePreview = document.getElementById('image-preview')
    inputFile.addEventListener('change', function(e) {
        const files = e.target.files
        const imagen = files[0]
        const buffer = URL.createObjectURL(imagen)
        console.log(buffer);

        imageDiv.classList.toggle('hidden')
        imagePreview.src = buffer
    })
</script>
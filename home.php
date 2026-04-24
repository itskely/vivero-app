<?php
ob_start();
session_start();
include "./config/database.php";

if (!isset($_SESSION['usuario']))
{
    header("Location: ./index.php");
    exit;
}
include "./config/auth.php";
include "./helpers/Forms.php";
?>
<!DOCTYPE html>
<html lang="es" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/output.css" />

    <script src="./assets/lib/flowbite.min.js"></script>

    <title>Menu Principal</title>
    <script>
        if (window.history.replaceState) {
            // Esto evita que al recargar la página se reenvíe el formulario
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/not-a-toast@1.1.5/dist/style.css">
    <script src="./assets/lib/jquery-4.0.0.min.js"></script>
</head>

<body class="">
    <script src="https://cdn.jsdelivr.net/npm/not-a-toast@1.1.5/dist/not-a-toast.umd.js"></script>

    <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
        type="button"
        class="text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base ms-3 mt-3 text-sm p-2 focus:outline-none inline-flex sm:hidden">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
            viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10" />
        </svg>
    </button>

    <aside id="default-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar">
        <div class="h-full px-3 py-4 overflow-y-auto bg-sidebar border-e border">
            <ul class="space-y-2 font-medium">
                <div class="flex items-center gap-2">
                    <img src="https://portaljaimeduque.net/img/LOGO_FPJD.png" alt="" class="size-8 shrink-0">
                    <h1 class="text-xl font-bold">ViveroApp</h1>
                </div>
                <div class="h-px w-full bg-border"></div>
                <?php
                foreach ($pagesRoleUser as $pg)
                {
                    ?>
                    <li>
                        <a href="/home.php?page_id=<?= $pg['id'] ?>"
                            class="<?= $pg['id'] == $page_accessed_id ? "bg-sidebar-accent text-sidebar-accent-foreground" : "" ?> flex text-muted-foreground items-center px-2 py-1.5 hover:bg-sidebar-accent rounded-base hover:text-sidebar-accent-foreground group">
                            <i class="<?= $pg['icon'] ?>"></i>
                            <span class="ms-3"><?= $pg['name'] ?></span>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <li>
                    <a href="./config/logout.php"
                        class="flex text-muted-foreground items-center px-2 py-1.5 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground rounded-base group">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <div class="sm:ml-64">
        <!-- Imagen superior -->
        <div class="w-full h-16 bg-cover bg-center bg-opacity-10 sticky top-0 z-50"
            style="background-image:url('/assets/img/planta.jpg')">
            <div class="flex-1 w-full h-full bg-black/50 flex items-center justify-end px-4">
                <button id="mode-button" class="btn btn-outline btn-size-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-4">
            <?php
            if ($pageAccessed)
            {
                require_once("./views/" . $pageAccessed['route']);
            } else
            {
                ?>
                <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
                    <div class="text-center">
                        <p class="text-base font-semibold bg-primary">404</p>
                        <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Pagina
                            no encontrada</h1>
                        <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Lo sentimos, no pudimos
                            encontrar la página que estás buscando.</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a href="./home.php?page_id=<?= $principalPage['id'] ?>"
                                class="rounded-md bg-primary px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Volver
                                a la página principal</a>
                            <a href="#" class="text-sm font-semibold text-gray-900">Contactar soporte <span
                                    aria-hidden="true">&rarr;</span></a>
                        </div>
                    </div>
                </main>
                <?php
            }
            ?>

        </div>
    </div>

    <script>
        const modeButton = document.getElementById("mode-button")
        document.addEventListener("DOMContentLoaded", () => {
            const darkModeStored = localStorage.getItem("mode") || "light"
            document.documentElement.classList.add(darkModeStored)
        })
        modeButton.addEventListener("click", (e) => {
            const darkModeStored = localStorage.getItem("mode") || "light"

            if (darkModeStored === "dark") {
                document.documentElement.classList.remove("dark")
                localStorage.setItem("mode", "light")
            } else {
                document.documentElement.classList.add("dark")
                localStorage.setItem("mode", "dark")
            }
        })
    </script>

</body>

</html>
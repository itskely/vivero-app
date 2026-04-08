<?php
session_start();
include "./config/database.php";

if (isset($_SESSION['usuario'])) {
    header("Location: ./home.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="./assets/output.css" />
</head>


<body>
    <header class="p-8 bg-green-700 text-white bg-[url(/assets/img/planta.jpg)] bg-cover bg-center">
        <div class="max-w-4xl mx-auto flex items-center justify-between">

        </div>
    </header>

    <main class="flex flex-col items-center justify-center min-h-screen">
        <img src="https://portaljaimeduque.net/img/LOGO_FPJD.png" alt="" class="size-10 shrink-0">
        <h1 class="text-2xl font-bold text-black text-center mb-24">VIVERO APP</h1>
        <h2 class="text-2xl  text-green-800 text-center mb-6">iniciar sesión</h2>
        <form id="login-form" method="POST" class="mx-auto max-w-md w-full space-y-4 rounded-lg border  bg-yellow-50/50 p-6">
            <div>
                <label class="block text-sm font-medium text-gray-900" for="cedula">Cedula</label>

                <input name="cedula" class="mt-4 w-full p-2 rounded-lg border-gray-300 focus:border-green-600 focus:outline-none" id="cedula" type="text" placeholder="Cedula">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900" for="password">Contraseña</label>

                <input name="password" class="mt-4 w-full p-2 rounded-lg border-gray-300 focus:border-green-600 focus:outline-none" id="password" type="password" placeholder="Contraseña">
            </div>

            <div id="error-message" class="hidden bg-red-100 border border-red-200 mb-4 text-sm text-red-800 rounded-lg p-4 dark:bg-red-500/20 dark:border-red-900 dark:text-red-400" role="alert" tabindex="-1" aria-labelledby="hs-soft-color-danger-label">
                <?= $_SESSION['error'] ?? '' ?>
                <?php unset($_SESSION['error']); ?>
            </div>


            <button type="submit" class="block w-full rounded-lg border border-green-700 bg-green-800 px-12 py-3 text-sm font-medium text-white transition-colors hover:bg-green-700">
                Iniciar sesión
            </button>
        </form>


    </main>
    <script>
        const form = document.getElementById("login-form");
        const span = document.getElementById("error-message");

        form.addEventListener("submit", function(event) {

            event.preventDefault();

            const formData = new FormData(form);

            fetch("./controllers/AuthController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {

                    console.log(data);

                    if (data.error) {
                        span.classList.remove("hidden");
                        span.innerHTML = data.error;
                    }

                    if (data.success) {
                        window.location.href = `/home.php?page_id=${data.pageToAccess.id}`;
                    }

                });

        });
    </script>

</body>

</html>
<?php
include __DIR__ . "/../controllers/EstadisticasController.php";
?>
<!-- Header -->
<header class="sticky top-0 z-40 border-b border-border bg-card/95 backdrop-blur supports-backdrop-filter:bg-card/60">
    <div>
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-primary flex items-center justify-center">
                        <i data-lucide="leaf" class="h-5 w-5 text-primary-foreground"></i>
                    </div>
                    <span class="text-lg font-semibold">Vivero</span>
                </div>
                <span class="hidden md:inline-block text-muted-foreground">|</span>
                <span class="hidden md:inline-block text-sm text-muted-foreground">Dashboard de Estadísticas</span>
            </div>

            <div class="flex items-center gap-3">
                <!-- Refresh Button -->
                <button onclick="refreshAllCharts()" class="btn-outline p-2 rounded-lg" title="Actualizar datos">
                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
    </div>
</header>

<main class="py-6">
    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 5: Semillas recolectadas por mes  -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

                <div>
                    <h3 class="text-lg font-semibold">
                        Semillas recolectadas
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        Colecta de semillas por especie
                    </p>
                </div>

                <div class="flex gap-2">
                    <select id="filterUnidad"
                        class="input-component text-sm"
                        onchange="updateSemillasRecolectadas()">

                        <option value="gramos">Gramos</option>
                        <option value="unidades">Unidades</option>

                    </select>
                    <select id="filterMode"
                        class="input-component text-sm"
                        onchange="updateFiltroSemillas()">

                        <option value="mes">Mes</option>
                        <option value="cuatrimestre">Cuatrimestre</option>
                        <option value="anio">Año</option>

                    </select>
                    <select id="filterMes"
                        class="input-component text-sm"
                        onchange="updateSemillasRecolectadas()">

                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>

                    </select>

                    <select id="filterCuatrimestre"
                        class="input-component text-sm"
                        onchange="updateSemillasRecolectadas()">
                        <option value="1">Enero - Abril</option>
                        <option value="2">Mayo - Agosto</option>
                        <option value="3">Septiembre - Diciembre</option>
                    </select>

                    <select id="filterYear"
                        class="input-component text-sm"
                        onchange="updateSemillasRecolectadas()">

                    </select>

                </div>


            </div>

            <div id="chartSemillasRecolectadas"
                class="chart-container">
            </div>
            <div class="mt-4 text-right">
                <span class="font-semibold">Total: </span>
                <span id="totalSemillas">0</span>
            </div>
        </div>
        <div class="card p-5">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

                <div>
                    <h3 class="text-lg font-semibold">
                        Plántulas recolectadas
                    </h3>

                    <p class="text-sm text-muted-foreground">
                        Colecta de plantulas por especie
                    </p>
                </div>

                <div class="flex gap-2">

                    <select id="filterModePlantulas"
                        class="input-component text-sm"
                        onchange="updateFiltroPlantulas()">

                        <option value="mes">Mes</option>
                        <option value="cuatrimestre">Cuatrimestre</option>
                        <option value="anio">Año</option>

                    </select>

                    <select id="filterMesPlantulas"
                        class="input-component text-sm"
                        onchange="updatePlantulasRecolectadas()">

                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>

                    </select>

                    <select id="filterCuatrimestrePlantulas"
                        class="input-component text-sm"
                        onchange="updatePlantulasRecolectadas()">

                        <option value="1">Enero - Abril</option>
                        <option value="2">Mayo - Agosto</option>
                        <option value="3">Septiembre - Diciembre</option>

                    </select>

                    <select id="filterYearPlantulas"
                        class="input-component text-sm"
                        onchange="updatePlantulasRecolectadas()">
                    </select>

                </div>

            </div>

            <div id="chartPlantulasRecolectadas"></div>
            <div class="mt-4 text-right">
                <span class="font-semibold">Total: </span>
                <span id="totalPlantulas">0</span>
            </div>
        </div>
        <!-- Chart 5: Salidas por Destino -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

                <div>
                    <h3 class="text-lg font-semibold">
                        Salidas por destino
                    </h3>

                    <p class="text-sm text-muted-foreground">
                        Trazabilidad de salidas
                    </p>
                </div>

                <div class="flex gap-2">

                    <select id="filterModeDestino"
                        class="input-component text-sm"
                        onchange="updateFiltroDestinos()">

                        <option value="mes">Mes</option>
                        <option value="cuatrimestre">Cuatrimestre</option>
                        <option value="anio">Año</option>

                    </select>

                    <select id="filterMesDestino"
                        class="input-component text-sm"
                        onchange="updateOrigenLotes()">

                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>

                    </select>

                    <select id="filterCuatrimestreDestino"
                        class="input-component text-sm"
                        onchange="updateOrigenLotes()">

                        <option value="1">Enero - Abril</option>
                        <option value="2">Mayo - Agosto</option>
                        <option value="3">Septiembre - Diciembre</option>

                    </select>

                    <select id="filterYearDestino"
                        class="input-component text-sm"
                        onchange="updateOrigenLotes()">

                    </select>

                </div>
            </div>
            <div id="chartOrigenLotes" class="chart-container"></div>
        </div>

        <div class="card p-5">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

                <div>
                    <h3 class="text-lg font-semibold">
                        Inventario actual en Rustificación
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        Cantidad actual por especie
                    </p>
                </div>

                <div class="flex gap-2">


                    <select id="filterUbicacionRustificacion"
                        class="input-component text-sm"
                        onchange="updateInventarioRustificacion()">

                        <option value="2">Tingua</option>
                        <option value="3">Arrieros</option>

                    </select>
                </div>

            </div>

            <div id="chartInventarioRustificacion"
                class="chart-container">
            </div>
            <div class="mt-4 text-right">
                <span class="font-semibold">Total: </span>
                <span id="totalInventarioRustificacion">0</span>
            </div>

        </div>



</main>

<script>
    // Generar meses para gráficas
    function generateMonths(count = 12) {
        const months = [];

        const now = new Date();
        for (let i = count - 1; i >= 0; i--) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            months.push(date.toLocaleDateString('es-ES', {
                month: 'short',
                year: 'numeric'
            }));
        }
        return months;
    }

    // No usada
    function generateWeeks(count = 12) {
        const weeks = [];
        const now = new Date();
        for (let i = count - 1; i >= 0; i--) {
            const date = new Date(now);
            date.setDate(date.getDate() - (i * 7));
            weeks.push(`Sem ${Math.ceil(date.getDate() / 7)} ${date.toLocaleDateString('es-ES', { month: 'short' })}`);
        }
        return weeks;
    }

    // No usada
    function generateDays(count = 30) {
        const days = [];
        const now = new Date();
        for (let i = count - 1; i >= 0; i--) {
            const date = new Date(now);
            date.setDate(date.getDate() - i);
            days.push(date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short'
            }));
        }
        return days;
    }

    function randomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    async function salidasDestinoData(chartMode) {
        try {
            const respuesta = await fetch(`/api/movimientos.php?${chartMode}`);

            const text = await respuesta.text(); // 👈 siempre texto primero

            try {
                return JSON.parse(text); // 👈 intento seguro
            } catch (e) {
                console.error(" Respuesta NO JSON:", text);
                return {
                    data: [],
                    years: []
                };
            }

        } catch (error) {
            console.error(" Error de red:", error);
            return {
                data: [],
                years: []
            };
        }
    }

    // =====================================================
    // CHART CONFIGURATIONS
    // =====================================================

    const chartInstances = {};

    // Base chart options
    function getBaseOptions() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            chart: {
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: true,
                    tools: {
                        download: false,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false
                    },
                },
                background: 'transparent',
            },
            theme: {
                mode: isDark ? 'dark' : 'light',
            },
            colors: ['var(--chart-1)', 'var(--chart-2)', 'var(--chart-3)', 'var(--chart-4)', 'var(--chart-5)', 'var(--chart-6)'],
            grid: {
                borderColor: 'var(--muted-foreground)',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: {
                    colors: 'var(--foreground)',
                },
            },
            xaxis: {
                labels: {
                    style: {
                        colors: 'var(--foreground)',
                        fontSize: '12px',
                    },
                },
                axisBorder: {
                    color: 'var(--muted-foreground)',
                },
                axisTicks: {
                    color: 'var(--muted-foreground)',
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: 'var(--foreground)',
                        fontSize: '12px',
                    },
                },
            },
        };
    }

    // =====================================================
    // CHART 5: salidas por destinos 
    // =====================================================
    async function updateOrigenLotes() {
        const mes = document.getElementById(
            'filterMesDestino'
        ).value;

        const cuatrimestre = document.getElementById(
            'filterCuatrimestreDestino'
        ).value;

        const year = document.getElementById(
            'filterYearDestino'
        ).value;
        const mode = document.getElementById(
            'filterModeDestino'
        ).value;

        const filterYearDestino = document.getElementById('filterYearDestino');
        const currentYear = new Date().getFullYear()
        const currentMonth = new Date().getMonth() + 1


        let response = await salidasDestinoData(
            `chart=salidas_destino&mes=${mes.trim() ? mes : currentMonth}&mode=${mode}&cuatrimestre=${cuatrimestre}&year=${year.trim() ? year : currentYear}`
        );

        let origenes = response.data;
        let years = response.years
        filterYearDestino.innerHTML = ''
        years.sort((a, b) => b - a).map((dato) => {
            filterYearDestino.innerHTML += `<option value="${dato}">${dato}</option>`;
        })
        filterYearDestino.value = year.trim() ? year : currentYear

        const options = {
            ...getBaseOptions(),
            series: origenes.map((origen) => parseInt(origen.total)),
            chart: {
                ...getBaseOptions().chart,
                type: 'donut',
                height: 320,
            },
            labels: origenes.map(o => o.nombre),
            colors: ['var(--chart-1)', 'var(--chart-2)', 'var(--chart-3)', 'var(--chart-4)', 'var(--chart-5)'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total unidades',
                                color: getBaseOptions().theme.mode === 'dark' ? '#a3a3a3' : '#737373',
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val) + '%';
                },
            },
            legend: {
                ...getBaseOptions().legend,
                position: 'bottom',
            },
        };

        renderChart('chartOrigenLotes', options);
    }


    function exportChart(id, name = "chart") {
        // Retrieve the chart instance via its assigned chart.id
        const chartInstance = chartInstances[id];

        if (!chartInstance) {
            console.error("Chart instance not found");
            return;
        }
        chartInstance.dataURI().then(({
            imgURI
        }) => {
            console.log(imgURI);
            const enlace = document.createElement('a');
            enlace.href = imgURI;
            enlace.download = name + '.png'; // Nombre del archivo final
            document.body.appendChild(enlace);
            enlace.click();
            document.body.removeChild(enlace);
        });
    }
    // =====================================================
    // CHART 6: Semillas recolectadas
    // =====================================================
    async function updateSemillasRecolectadas() {
        const unidad_medida = document.getElementById(
            'filterUnidad'
        ).value;
        const mes = document.getElementById(
            'filterMes'
        ).value;

        const cuatrimestre = document.getElementById(
            'filterCuatrimestre'
        ).value;

        const year = document.getElementById(
            'filterYear'
        ).value;
        const mode = document.getElementById(
            'filterMode'
        ).value;

        const currentMonth = new Date().getMonth() + 1
        const currentYear = new Date().getFullYear()

        let response = await salidasDestinoData(
            `chart=semillas_recolectadas&unidad=${unidad_medida}&mode=${mode}&mes=${mes.trim() ? mes : currentMonth}&cuatrimestre=${cuatrimestre}&year=${year.trim() ? year : currentYear}`
        );


        let years = response.years
        filterYear.innerHTML = ''
        years.sort((a, b) => b - a).map((dato) => {
            filterYear.innerHTML += `<option value="${dato}">${dato}</option>`;
        })
        filterYear.value = year.trim() ? year : currentYear

        let semillas = response.data;
        let total = semillas.reduce((suma, s) => {
            return suma + parseFloat(s.total_gramos);
        }, 0);

        document.getElementById("totalSemillas").textContent =
            `${total.toLocaleString()} ${unidad_medida}`;

        const options = {
            ...getBaseOptions(),

            series: [{
                name: unidad_medida === 'gramos' ?
                    'Gramos' : 'Unidades',

                data: semillas.map(
                    s => parseFloat(s.total_gramos)
                )
            }],

            chart: {
                ...getBaseOptions().chart,
                type: 'bar',
                height: 350,
            },

            xaxis: {
                categories: semillas.map(
                    s => s.nombre_comun
                ),
                labels: {
                    rotate: -45
                }
            },

            yaxis: {
                title: {
                    text: unidad_medida === 'gramos' ? 'Gramos' : 'Unidades'
                }
            },

            dataLabels: {
                enabled: true
            }
        };

        renderChart(
            'chartSemillasRecolectadas',
            options
        );
    }

    function updateFiltroSemillas() {

        const mode =
            document.getElementById('filterMode').value;

        document.getElementById('filterMes')
            .style.display =
            mode === 'mes' ? 'block' : 'none';

        document.getElementById('filterCuatrimestre')
            .style.display =
            mode === 'cuatrimestre' ? 'block' : 'none';
        updateSemillasRecolectadas();
    }
    async function updatePlantulasRecolectadas() {

        const mes = document.getElementById('filterMesPlantulas').value;
        const cuatrimestre = document.getElementById('filterCuatrimestrePlantulas').value;
        const year = document.getElementById('filterYearPlantulas').value;
        const mode = document.getElementById('filterModePlantulas').value;

        const currentMonth = new Date().getMonth() + 1;
        const currentYear = new Date().getFullYear();

        let response = await salidasDestinoData(
            `chart=plantulas_recolectadas&mode=${mode}&mes=${mes || currentMonth}&cuatrimestre=${cuatrimestre}&year=${year || currentYear}`
        );
        console.log(response)

        let years = response.years || [];

        const filterYearPlantulas = document.getElementById('filterYearPlantulas');
        filterYearPlantulas.innerHTML = '';

        years.sort((a, b) => b - a).forEach((dato) => {
            filterYearPlantulas.innerHTML += `
            <option value="${dato}">${dato}</option>
        `;
        });

        filterYearPlantulas.value = year || currentYear;

        let plantulas = response.data || [];
        let total = plantulas.reduce((suma, s) => {
            return suma + parseFloat(s.total);
        }, 0);

        document.getElementById("totalPlantulas").textContent =
            `${total.toLocaleString()} unidades`;

        const options = {
            ...getBaseOptions(),

            chart: {
                ...getBaseOptions().chart,
                type: 'bar',
                height: 350,
            },

            series: [{
                name: 'Plántulas',
                data: plantulas.map(p => Number(p.total))
            }],

            xaxis: {
                categories: plantulas.map(p => p.nombre_comun),
                labels: {
                    rotate: -45
                }
            },

            dataLabels: {
                enabled: true
            }
        };

        renderChart('chartPlantulasRecolectadas', options);
    }

    function updateFiltroPlantulas() {

        const mode =
            document.getElementById('filterModePlantulas').value;

        document.getElementById('filterMesPlantulas')
            .style.display =
            mode === 'mes' ? 'block' : 'none';

        document.getElementById('filterCuatrimestrePlantulas')
            .style.display =
            mode === 'cuatrimestre' ? 'block' : 'none';
        updatePlantulasRecolectadas();
    }

    function updateFiltroDestinos() {

        const mode =
            document.getElementById('filterModeDestino').value;

        document.getElementById('filterMesDestino')
            .style.display =
            mode === 'mes' ? 'block' : 'none';

        document.getElementById('filterCuatrimestreDestino')
            .style.display =
            mode === 'cuatrimestre' ? 'block' : 'none';
        updateOrigenLotes();
    }

    async function updateInventarioRustificacion() {

        const ubicacion = document.getElementById("filterUbicacionRustificacion").value;

        const response = await salidasDestinoData(
            `chart=inventario_rustificacion&ubicacion=${ubicacion}`
        );

        console.log("RESPUESTA:", response);

        if (!response || !Array.isArray(response.data) || response.data.length === 0) {
            console.warn("Sin datos para inventario");
            document.getElementById("chartInventarioRustificacion").innerHTML =
                "<p class='text-sm text-muted-foreground'>Sin datos disponibles</p>";
            return;
        }

        const especies = response.data;
        let total = especies.reduce((suma, e) => {
            return suma + parseFloat(e.cantidad);
        }, 0);

        document.getElementById("totalInventarioRustificacion").textContent =
            `${total.toLocaleString()} unidades`;

        const options = {
            ...getBaseOptions(),
            colors: ['#064e3b'],

            chart: {
                ...getBaseOptions().chart,
                type: "bar",
                height: 350
            },

            series: [{
                name: "Cantidad",
                data: especies.map(e => Number(e.cantidad))
            }],

            xaxis: {
                categories: especies.map(e => e.nombre_comun)
            },

            dataLabels: {
                enabled: true
            }
        };

        renderChart("chartInventarioRustificacion", options);
    }

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================

    function renderChart(elementId, options) {
        if (chartInstances[elementId]) {
            chartInstances[elementId].destroy();
        }
        chartInstances[elementId] = new ApexCharts(document.getElementById(elementId), options);
        chartInstances[elementId].render();
    }

    function refreshAllCharts() {
        updateOrigenLotes();
        updateSemillasRecolectadas();
        updatePlantulasRecolectadas();
        updateInventarioRustificacion();
    }

    // =====================================================
    // INITIALIZATION
    // =====================================================

    document.addEventListener('DOMContentLoaded', function() {

        lucide.createIcons();

        const mesActual = new Date().getMonth() + 1;

        document.getElementById('filterMes').value =
            mesActual;

        document.getElementById('filterMesDestino').value =
            mesActual;

        document.getElementById('filterMesPlantulas').value =
            mesActual;



        updateFiltroSemillas();
        updateFiltroDestinos();
        updateFiltroPlantulas();

        refreshAllCharts();
    });
</script>
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
    <!-- Stats Cards Row -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- Stock Total -->
        <div class="card p-4 stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-muted-foreground font-medium">Stock Total</p>
                    <p class="text-2xl font-bold mt-1" id="statStock">12,458</p>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="h-3 w-3 trend-up"></i>
                        <span class="text-xs trend-up">+12.5%</span>
                    </div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <i data-lucide="package" class="h-5 w-5 text-primary"></i>
                </div>
            </div>
        </div>

        <!-- Lotes Activos -->
        <div class="card p-4 stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-muted-foreground font-medium">Lotes Activos</p>
                    <p class="text-2xl font-bold mt-1" id="statLotes">156</p>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="h-3 w-3 trend-up"></i>
                        <span class="text-xs trend-up">+8 nuevos</span>
                    </div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                    <i data-lucide="layers" class="h-5 w-5 text-blue-500"></i>
                </div>
            </div>
        </div>

        <!-- Entradas del Mes -->
        <div class="card p-4 stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-muted-foreground font-medium">Entradas (Mes)</p>
                    <p class="text-2xl font-bold mt-1" id="statEntradas">2,340</p>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="arrow-up-right" class="h-3 w-3 trend-up"></i>
                        <span class="text-xs text-muted-foreground">vs 2,180 mes ant.</span>
                    </div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <i data-lucide="arrow-down-to-line" class="h-5 w-5 text-emerald-500"></i>
                </div>
            </div>
        </div>

        <!-- Salidas del Mes -->
        <div class="card p-4 stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-muted-foreground font-medium">Salidas (Mes)</p>
                    <p class="text-2xl font-bold mt-1" id="statSalidas">1,890</p>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-down" class="h-3 w-3 trend-down"></i>
                        <span class="text-xs text-muted-foreground">vs 2,050 mes ant.</span>
                    </div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <i data-lucide="arrow-up-from-line" class="h-5 w-5 text-amber-500"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Movimientos en el Tiempo -->
        <div class="card p-5 lg:col-span-2">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Movimientos en el Tiempo</h3>
                    <p class="text-sm text-muted-foreground">Entradas vs Salidas por período</p>
                </div>
                <div class="grid grid-cols-3 items-center gap-2">
                    <button class="btn btn-outline btn-size-default w-fit ml-auto"
                        onclick="exportChart('chartMovimientosTiempo', 'movimientos_tiempo')">Exportar</button>

                    <select id="filterMovTiempo" class="input-component text-sm" onchange="updateMovimientosTiempo()">
                        <option value="all" selected>Todas las etapas</option>
                        <?php
                        foreach ($allEtapas as $key => $value) {
                        ?>
                            <option value="<?= $value["id"] ?>">
                                <?= $value["nombre"] ?>
                            </option>
                        <?php
                        }
                        ?>
                    </select>
                    <select id="filterMovTiempoGranularity" class="input-component text-sm"
                        onchange="updateMovimientosTiempo()">
                    </select>
                </div>
            </div>
            <div id="chartMovimientosTiempo" class="chart-container"></div>
        </div>

        <!-- Chart 5: Salidas por Destino -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Salidas por destino</h3>
                    <p class="text-sm text-muted-foreground">Trazabilidad de salidas </p>
                </div>
            </div>
            <div id="chartOrigenLotes" class="chart-container"></div>
        </div>

        <!-- Chart 6: Inventario por Ubicación -->
        <!-- <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Inventario por Ubicación</h3>
                    <p class="text-sm text-muted-foreground">Distribución física del stock</p>
                </div>
                <select id="filterUbicacionEtapa" class="input-component w-fit text-sm" onchange="updateInventarioUbicacion()">
                    <option value="all">Todas las etapas</option>
                    <option value="1">Semilla</option>
                    <option value="2">Germinación</option>
                    <option value="3">Plántula</option>
                    <option value="4">Vegetativo</option>
                    <option value="5">Producción</option>
                </select>
            </div>
            <div id="chartInventarioUbicacion" class="chart-container"></div>
        </div> -->


    </div>
</main>

<script>
    // =====================================================
    // MOCK DATA - Simula respuestas JSON del backend
    // =====================================================

    const MOCK_DATA = {
        // Datos de catálogos
        plantas: [{
                id: 1,
                nombre_comun: 'Albahaca',
                nombre_cientifico: 'Ocimum basilicum'
            },
            {
                id: 2,
                nombre_comun: 'Romero',
                nombre_cientifico: 'Salvia rosmarinus'
            },
            {
                id: 3,
                nombre_comun: 'Menta',
                nombre_cientifico: 'Mentha spicata'
            },
            {
                id: 4,
                nombre_comun: 'Lavanda',
                nombre_cientifico: 'Lavandula angustifolia'
            },
            {
                id: 5,
                nombre_comun: 'Tomillo',
                nombre_cientifico: 'Thymus vulgaris'
            },
            {
                id: 6,
                nombre_comun: 'Orégano',
                nombre_cientifico: 'Origanum vulgare'
            },
            {
                id: 7,
                nombre_comun: 'Cilantro',
                nombre_cientifico: 'Coriandrum sativum'
            },
            {
                id: 8,
                nombre_comun: 'Perejil',
                nombre_cientifico: 'Petroselinum crispum'
            },
            {
                id: 9,
                nombre_comun: 'Salvia',
                nombre_cientifico: 'Salvia officinalis'
            },
            {
                id: 10,
                nombre_comun: 'Hierbabuena',
                nombre_cientifico: 'Mentha × piperita'
            },
            {
                id: 11,
                nombre_comun: 'Eneldo',
                nombre_cientifico: 'Anethum graveolens'
            },
            {
                id: 12,
                nombre_comun: 'Estragón',
                nombre_cientifico: 'Artemisia dracunculus'
            },
        ],

        etapas: [{
                id: 1,
                nombre: 'Semilla'
            },
            {
                id: 2,
                nombre: 'Germinación'
            },
            {
                id: 3,
                nombre: 'Plántula'
            },
            {
                id: 4,
                nombre: 'Vegetativo'
            },
            {
                id: 5,
                nombre: 'Producción'
            },
        ],

        ubicaciones: [{
                id: 1,
                nombre: 'Invernadero A'
            },
            {
                id: 2,
                nombre: 'Invernadero B'
            },
            {
                id: 3,
                nombre: 'Almacén Central'
            },
            {
                id: 4,
                nombre: 'Zona de Secado'
            },
            {
                id: 5,
                nombre: 'Cuarto Frío'
            },
            {
                id: 6,
                nombre: 'Área de Empaque'
            },
        ],

        origenes: [{
                id: 1,
                nombre_origen: 'Producción Propia',
                tipo: 'Interno'
            },
            {
                id: 2,
                nombre_origen: 'Proveedor Semillas SA',
                tipo: 'compra'
            },
            {
                id: 3,
                nombre_origen: 'Jardín Botánico',
                tipo: 'Donaciones'
            },
            {
                id: 4,
                nombre_origen: 'Cooperativa Regional',
                tipo: 'externo'
            },
            {
                id: 5,
                nombre_origen: 'Importación',
                tipo: 'compra'
            },
        ],
    };

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

    async function generateTrendData(etapaFilter, year, chartMode) {
        let data = [];
        try {
            const respuesta = await fetch(`/api/movimientos.php?etapa_id=${etapaFilter}&year=${year}&chart=${chartMode}`);
            if (respuesta.ok) {
                data = await respuesta.json();
            }
        } catch (error) {
            console.error(error);
        }

        return data;
    }

    async function salidasDestinoData(chartMode) {
        let data = [];
        try {
            const respuesta = await fetch(`/api/movimientos.php?chart=${chartMode}`);
            if (respuesta.ok) {
                data = await respuesta.json();
            }
        } catch (error) {
            console.error(error);
        }

        return data;
    }

    // function generateTrendData(baseValue, count, variance = 0.2) {
    //     const data = [];
    //     let current = baseValue;
    //     for (let i = 0; i < count; i++) {
    //         const change = current * (Math.random() * variance * 2 - variance);
    //         current = Math.max(10, current + change);
    //         data.push(Math.round(current));
    //     }
    //     return data;
    // }

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
    // CHART 1: Movimientos en el Tiempo
    // =====================================================
    async function updateMovimientosTiempo() {
        const elEtapaFilter = document.getElementById('filterMovTiempo');
        const elGranularity = document.getElementById('filterMovTiempoGranularity');
        const etapaFilter = elEtapaFilter.value;
        const year = elGranularity.value;

        let data = await generateTrendData(etapaFilter, year, "movimientos_mes");

        let years = data.years;
        let entradas = data.series.entrada;
        let salidas = data.series.salida;
        let categories = data.labels;

        // Agregar opcion al elGranularity por cada ano en el array
        elGranularity.innerHTML = ''
        years.sort((a, b) => b.ano - a.ano).map((dato) => {
            elGranularity.innerHTML += `<option value="${dato.ano}">${dato.ano}</option>`;
        })
        elGranularity.value = data.filter_year

        const options = {
            ...getBaseOptions(),
            series: [{
                    name: 'Entradas',
                    data: entradas,
                },
                {
                    name: 'Salidas',
                    data: salidas,
                },
            ],
            chart: {
                ...getBaseOptions().chart,
                type: 'area',
                height: 320,
                stacked: false,
            },
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            colors: ['var(--chart-2)', 'var(--chart-1)'],
            xaxis: {
                ...getBaseOptions().xaxis,
                categories: categories,
            },
            dataLabels: {
                enabled: false
            },
        };

        renderChart('chartMovimientosTiempo', options);
    }

    // =====================================================
    // CHART 5: salidas por destinos 
    // =====================================================
    async function updateOrigenLotes() {

        let data = await salidasDestinoData("salidas_destino");
        let origenes = data.data;

        console.log(data);

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
                                label: 'Total Lotes',
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

    // =====================================================
    // CHART 6: Inventario por Ubicación
    // =====================================================
    function updateInventarioUbicacion() {
        const etapaFilter = document.getElementById('filterUbicacionEtapa').value;

        const ubicaciones = MOCK_DATA.ubicaciones.map(u => u.nombre);
        const baseValue = etapaFilter === 'all' ? 800 : randomInt(200, 600);

        const options = {
            ...getBaseOptions(),
            series: [{
                name: 'Stock',
                data: ubicaciones.map(() => randomInt(baseValue * 0.5, baseValue * 1.5)),
            }],
            chart: {
                ...getBaseOptions().chart,
                type: 'bar',
                height: 320,
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: true,
                    distributed: true,
                    barHeight: '65%',
                }
            },
            colors: ['var(--chart-1)', 'var(--chart-2)', 'var(--chart-3)', 'var(--chart-4)', 'var(--chart-5)'],
            xaxis: {
                ...getBaseOptions().xaxis,
                categories: ubicaciones,
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toLocaleString();
                },
                style: {
                    fontSize: '11px',
                    colors: ['#fff']
                }
            },
        };

        renderChart('chartInventarioUbicacion', options);
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
        updateMovimientosTiempo();
        updateOrigenLotes();
        updateInventarioUbicacion();
    }

    // =====================================================
    // INITIALIZATION
    // =====================================================

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Initialize all charts
        refreshAllCharts();

        // Update stats with mock data
        document.getElementById('statStock').textContent = randomInt(10000, 15000).toLocaleString();
        document.getElementById('statLotes').textContent = randomInt(120, 200).toLocaleString();
        document.getElementById('statEntradas').textContent = randomInt(2000, 3000).toLocaleString();
        document.getElementById('statSalidas').textContent = randomInt(1500, 2500).toLocaleString();
    });
</script>
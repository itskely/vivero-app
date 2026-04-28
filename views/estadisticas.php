<!-- Header -->
<header class="sticky top-0 z-40 border-b border-border bg-card/95 backdrop-blur supports-backdrop-filter:bg-card/60">
    <div class="container mx-auto px-4 lg:px-6">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-primary flex items-center justify-center">
                        <i data-lucide="leaf" class="h-5 w-5 text-primary-foreground"></i>
                    </div>
                    <span class="text-lg font-semibold">ViveroTrack</span>
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

<main class="container mx-auto px-4 lg:px-6 py-6">
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
                <div class="flex flex-wrap items-center gap-2">
                    <select id="filterMovTiempo" class="input-field text-sm" onchange="updateMovimientosTiempo()">
                        <option value="all">Todas las etapas</option>
                        <option value="1">Semilla</option>
                        <option value="2">Germinación</option>
                        <option value="3">Plántula</option>
                        <option value="4">Vegetativo</option>
                        <option value="5">Producción</option>
                    </select>
                    <select id="filterMovTiempoGranularity" class="input-field text-sm"
                        onchange="updateMovimientosTiempo()">
                        <option value="monthly" selected>Mensual</option>
                    </select>
                </div>
            </div>
            <div id="chartMovimientosTiempo" class="chart-container"></div>
        </div>

        <!-- Chart 2: Stock por Especie -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Stock por Especie</h3>
                    <p class="text-sm text-muted-foreground">Inventario actual por planta</p>
                </div>
                <select id="filterStockUnidad" class="input-field text-sm" onchange="updateStockEspecie()">
                    <option value="all">Todas las unidades</option>
                    <option value="unidades">Unidades</option>
                    <option value="gramos">Gramos</option>
                    <option value="kilogramos">Kilogramos</option>
                </select>
            </div>
            <div id="chartStockEspecie" class="chart-container"></div>
        </div>

        <!-- Chart 3: Movimientos por Etapa -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Movimientos por Etapa</h3>
                    <p class="text-sm text-muted-foreground">Distribución de actividad</p>
                </div>
                <select id="filterMovEtapaTipo" class="input-field text-sm" onchange="updateMovimientosEtapa()">
                    <option value="all">Todos los tipos</option>
                    <option value="entrada">Solo Entradas</option>
                    <option value="salida">Solo Salidas</option>
                    <option value="traslado">Solo Traslados</option>
                </select>
            </div>
            <div id="chartMovimientosEtapa" class="chart-container"></div>
        </div>

        <!-- Chart 5: Origen de Lotes -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Origen de Lotes</h3>
                    <p class="text-sm text-muted-foreground">Trazabilidad de procedencia</p>
                </div>
                <select id="filterOrigenTipo" class="input-field text-sm" onchange="updateOrigenLotes()">
                    <option value="all">Todos los tipos</option>
                    <option value="Interno">Interno</option>
                    <option value="compra">Compra</option>
                    <option value="Donaciones">Donaciones</option>
                    <option value="externo">Externo</option>
                </select>
            </div>
            <div id="chartOrigenLotes" class="chart-container"></div>
        </div>

        <!-- Chart 6: Inventario por Ubicación -->
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-semibold">Inventario por Ubicación</h3>
                    <p class="text-sm text-muted-foreground">Distribución física del stock</p>
                </div>
                <select id="filterUbicacionEtapa" class="input-field text-sm" onchange="updateInventarioUbicacion()">
                    <option value="all">Todas las etapas</option>
                    <option value="1">Semilla</option>
                    <option value="2">Germinación</option>
                    <option value="3">Plántula</option>
                    <option value="4">Vegetativo</option>
                    <option value="5">Producción</option>
                </select>
            </div>
            <div id="chartInventarioUbicacion" class="chart-container"></div>
        </div>


    </div>
</main>

<script>
    // =====================================================
    // MOCK DATA - Simula respuestas JSON del backend
    // =====================================================

    const MOCK_DATA = {
        // Datos de catálogos
        plantas: [
            { id: 1, nombre_comun: 'Albahaca', nombre_cientifico: 'Ocimum basilicum' },
            { id: 2, nombre_comun: 'Romero', nombre_cientifico: 'Salvia rosmarinus' },
            { id: 3, nombre_comun: 'Menta', nombre_cientifico: 'Mentha spicata' },
            { id: 4, nombre_comun: 'Lavanda', nombre_cientifico: 'Lavandula angustifolia' },
            { id: 5, nombre_comun: 'Tomillo', nombre_cientifico: 'Thymus vulgaris' },
            { id: 6, nombre_comun: 'Orégano', nombre_cientifico: 'Origanum vulgare' },
            { id: 7, nombre_comun: 'Cilantro', nombre_cientifico: 'Coriandrum sativum' },
            { id: 8, nombre_comun: 'Perejil', nombre_cientifico: 'Petroselinum crispum' },
            { id: 9, nombre_comun: 'Salvia', nombre_cientifico: 'Salvia officinalis' },
            { id: 10, nombre_comun: 'Hierbabuena', nombre_cientifico: 'Mentha × piperita' },
            { id: 11, nombre_comun: 'Eneldo', nombre_cientifico: 'Anethum graveolens' },
            { id: 12, nombre_comun: 'Estragón', nombre_cientifico: 'Artemisia dracunculus' },
        ],

        etapas: [
            { id: 1, nombre: 'Semilla' },
            { id: 2, nombre: 'Germinación' },
            { id: 3, nombre: 'Plántula' },
            { id: 4, nombre: 'Vegetativo' },
            { id: 5, nombre: 'Producción' },
        ],

        ubicaciones: [
            { id: 1, nombre: 'Invernadero A' },
            { id: 2, nombre: 'Invernadero B' },
            { id: 3, nombre: 'Almacén Central' },
            { id: 4, nombre: 'Zona de Secado' },
            { id: 5, nombre: 'Cuarto Frío' },
            { id: 6, nombre: 'Área de Empaque' },
        ],

        origenes: [
            { id: 1, nombre_origen: 'Producción Propia', tipo: 'Interno' },
            { id: 2, nombre_origen: 'Proveedor Semillas SA', tipo: 'compra' },
            { id: 3, nombre_origen: 'Jardín Botánico', tipo: 'Donaciones' },
            { id: 4, nombre_origen: 'Cooperativa Regional', tipo: 'externo' },
            { id: 5, nombre_origen: 'Importación', tipo: 'compra' },
        ],
    };

    // Generar meses para gráficas
    function generateMonths(count = 12) {
        const months = [];

        const now = new Date();
        for (let i = count - 1; i >= 0; i--) {
            const date = new Date(now.getFullYear(), now.getMonth() - i, 1);
            months.push(date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' }));
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
            days.push(date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }));
        }
        return days;
    }

    function randomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    async function generateTrendData(baseValue, count, variance = 0.2) {
        let data = []
        await fetch("/api/movimientos.php")
            .then(res => res.json())
            .then(data => {
                data = data;
            })
            .catch(err => {
                console.error(err)
            });

        return data;
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
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: false,
                        reset: true
                    },
                },
                background: 'transparent',
            },
            theme: {
                mode: isDark ? 'dark' : 'light',
            },
            colors: ['#22c55e', '#16a34a', '#15803d', '#166534', '#14532d', '#86efac'],
            grid: {
                borderColor: isDark ? 'rgba(255,255,255,0.1)' : '#e5e5e5',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: {
                    colors: isDark ? '#a3a3a3' : '#737373',
                },
            },
            xaxis: {
                labels: {
                    style: {
                        colors: isDark ? '#a3a3a3' : '#737373',
                        fontSize: '12px',
                    },
                },
                axisBorder: {
                    color: isDark ? 'rgba(255,255,255,0.1)' : '#e5e5e5',
                },
                axisTicks: {
                    color: isDark ? 'rgba(255,255,255,0.1)' : '#e5e5e5',
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: isDark ? '#a3a3a3' : '#737373',
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
        const etapaFilter = document.getElementById('filterMovTiempo').value;
        const granularity = document.getElementById('filterMovTiempoGranularity').value;

        let categories, count;
        switch (granularity) {
            default:
                categories = generateMonths(12);
                count = 12;
        }

        // Simulate filtered data
        const baseEntradas = etapaFilter === 'all' ? 200 : randomInt(50, 150);
        const baseSalidas = etapaFilter === 'all' ? 180 : randomInt(40, 130);

        let entradas = await generateTrendData(baseEntradas, count, 0.25);
        let salidas = await generateTrendData(baseSalidas, count, 0.25);
        console.log(entradas, salidas);

        const options = {
            ...getBaseOptions(),
            series: [
                {
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
            colors: ['#22c55e', '#f59e0b'],
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
    // CHART 2: Stock por Especie
    // =====================================================
    function updateStockEspecie() {
        const unidadFilter = document.getElementById('filterStockUnidad').value;

        let plantas = [...MOCK_DATA.plantas].slice(0, 8);
        let stockData = plantas.map(() => randomInt(200, 2500));

        // Adjust values based on unit filter
        if (unidadFilter === 'gramos') {
            stockData = stockData.map(v => v * 100);
        } else if (unidadFilter === 'kilogramos') {
            stockData = stockData.map(v => Math.round(v / 10));
        }

        const options = {
            ...getBaseOptions(),
            series: [{
                name: 'Stock',
                data: stockData,
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
                    barHeight: '70%',
                }
            },
            colors: ['#22c55e', '#16a34a', '#15803d', '#166534', '#14532d', '#86efac', '#4ade80', '#a7f3d0'],
            xaxis: {
                ...getBaseOptions().xaxis,
                categories: plantas.map(p => p.nombre_comun),
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toLocaleString();
                },
                style: {
                    fontSize: '11px',
                    colors: ['#fff']
                }
            },
        };

        renderChart('chartStockEspecie', options);
    }

    // =====================================================
    // CHART 3: Movimientos por Etapa
    // =====================================================
    function updateMovimientosEtapa() {
        const tipoFilter = document.getElementById('filterMovEtapaTipo').value;

        const etapas = MOCK_DATA.etapas.map(e => e.nombre);

        let series = [];
        if (tipoFilter === 'all' || tipoFilter === 'entrada') {
            series.push({
                name: 'Entradas',
                data: etapas.map(() => randomInt(100, 500)),
            });
        }
        if (tipoFilter === 'all' || tipoFilter === 'salida') {
            series.push({
                name: 'Salidas',
                data: etapas.map(() => randomInt(80, 450)),
            });
        }
        if (tipoFilter === 'all' || tipoFilter === 'traslado') {
            series.push({
                name: 'Traslados',
                data: etapas.map(() => randomInt(30, 200)),
            });
        }

        const options = {
            ...getBaseOptions(),
            series: series,
            chart: {
                ...getBaseOptions().chart,
                type: 'bar',
                height: 320,
                stacked: true,
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '60%',
                }
            },
            colors: ['#22c55e', '#f59e0b', '#3b82f6'],
            xaxis: {
                ...getBaseOptions().xaxis,
                categories: etapas,
            },
            dataLabels: {
                enabled: false
            },
        };

        renderChart('chartMovimientosEtapa', options);
    }

    // =====================================================
    // CHART 5: Origen de Lotes
    // =====================================================
    function updateOrigenLotes() {
        const tipoFilter = document.getElementById('filterOrigenTipo').value;

        let origenes = MOCK_DATA.origenes;
        if (tipoFilter !== 'all') {
            origenes = origenes.filter(o => o.tipo === tipoFilter);
        }

        const options = {
            ...getBaseOptions(),
            series: origenes.map(() => randomInt(10, 60)),
            chart: {
                ...getBaseOptions().chart,
                type: 'donut',
                height: 320,
            },
            labels: origenes.map(o => o.nombre_origen),
            colors: ['#22c55e', '#16a34a', '#15803d', '#166534', '#86efac'],
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
                formatter: function (val) {
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
            colors: ['#22c55e', '#16a34a', '#15803d', '#166534', '#14532d', '#86efac'],
            xaxis: {
                ...getBaseOptions().xaxis,
                categories: ubicaciones,
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
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
        updateStockEspecie();
        updateMovimientosEtapa();
        updateOrigenLotes();
        updateInventarioUbicacion();
    }

    // =====================================================
    // INITIALIZATION
    // =====================================================

    document.addEventListener('DOMContentLoaded', function () {
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
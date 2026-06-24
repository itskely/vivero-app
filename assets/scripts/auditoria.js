/* Peticion a /api/lotes.php y usar 
    el template #template-option con sus data- para llenar la informacion, 
    usarlo como selector cada opcion, marcar con su check la opcion seleccionada
    y mostrar la informacion del lote seleccionado en el boton lote_button a su vez cambiar el valor
    del input hidden llamado "lote" con el id de lote seleccionado
*/

const variants = {
    icons: {
        success: {
            svg: "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-trending-up h-5 w-5 shrink-0 mt-0.5 text-blue-600' aria-hidden='true'><path d='M16 7h6v6'></path><path d='m22 7-8.5 8.5-5-5L2 17'></path></svg>",
            className: '',
        },
        warning: {
            svg: "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-triangle-alert h-5 w-5 shrink-0 mt-0.5 text-amber-600' aria-hidden='true'><path d='m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3'></path><path d='M12 9v4'></path><path d='M12 17h.01'></path></svg>",
            className: '',
        },
        error: {
            svg: "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-triangle-alert h-5 w-5 shrink-0 mt-0.5 text-red-600' aria-hidden='true'><path d='m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3'></path><path d='M12 9v4'></path><path d='M12 17h.01'></path></svg>",
            className: '',
        },
        trendingDown: {
            svg: "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-trending-down h-5 w-5 shrink-0 mt-0.5 text-amber-600' aria-hidden='true'><path d='M16 17h6v-6'></path><path d='m22 17-8.5-8.5-5 5L2 7'></path></svg>",
            className: '',
        },
    },
    states: {
        success: {
            card: {
                className: 'bg-blue-50 border-blue-200 dark:bg-blue-600/20 dark:border-blue-600',
            },
            text: {
                className: 'text-blue-600',
            },
            message: {
                className: 'bg-blue-100 dark:bg-blue-600/30',
            },
        },
        warning: {
            card: {
                className: 'bg-amber-50 border-amber-200 dark:bg-amber-600/20 dark:border-amber-600',
            },
            text: {
                className: 'text-amber-600',
            },
            message: {
                className: 'bg-amber-100 dark:bg-amber-600/30',
            },
        },
        error: {
            card: {
                className: 'bg-red-50 border-red-200 dark:bg-red-600/20 dark:border-red-600',
            },
            text: {
                className: 'text-red-600',
            },
            message: {
                className: 'bg-red-100 dark:bg-red-600/30',
            },
        },
    },
};

const lotesSearchInput = $('#lotes-search-input');
const lotesSpinner = $('#lotes-spinner');
const lotesContainer = $('#lotes-container');
const template = $('#template-option').html();
const buttonLotes = $('#lote_button');
const inputHidden = $('#lote_id'); // DETECT
const availableStock = $('#available-stock');

const templateStock = $('#template-stock').html();

const inventario_id = $('#inventario_id'); // DETECT

const cantidad_real = $('#cantidad_real');

// Alerts
const alertAudit = $('#alert-audit');

// Form
const form = $('#form-cambiar-etapa');

let selectedInventario = null;
let selectedStock = null;

buttonLotes.click(function () {
    $(this).next().toggle('fast');
});
function formatoNumero(numero) {
    if (Math.floor(numero) === numero) {
        return Math.floor(numero);
    }
    return numero;
}
function getLotes(busqueda = '') {
    $.ajax({
        url: '/api/lotes.php?busqueda=' + busqueda,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            lotesSpinner.removeClass('hidden');
        },
        complete: function () {
            lotesSpinner.addClass('hidden');
        },
        success: function (response) {
            const { data, pagination } = response;
            lotesContainer.html('');
            data.forEach(function (inventario) {
                var $nuevoItem = $(template).clone();
                $nuevoItem.attr('data-value', inventario.lote_id);
                $nuevoItem.find('[data-title]').text(`${inventario.etapa} - ${inventario.ubicacion}`);
                $nuevoItem
                    .find('[data-subtitle]')
                    .text(
                        `lote # ${inventario.lote_id}-${inventario.nombre_comun} (${formatoNumero(parseFloat(inventario.cantidad_actual))} ${inventario.unidad_medida})`
                    );

                // Agregar evento de click a cada item, en caso de cliquear, guardamos el valor que tiene en el atributo value
                // luego seteamos el valor al input hidden llamado "lote" con el id de lote seleccionado
                // y mostramos la informacion del lote seleccionado en el boton lote_button
                $nuevoItem.click(function () {
                    selectedInventario = inventario;
                    selectedStock = inventario;
                    inputHidden.val($(this).attr('data-value'));
                    inventario_id.val(inventario.id);
                    cantidad_real.val(formatoNumero(parseFloat(inventario.cantidad_actual)));
                    // Añadir a todos los demas la clase opacity-o y al que esta en 100
                    lotesContainer.find('[data-selected]').removeClass('opacity-100').addClass('opacity-0');
                    $(this).find('[data-selected]').removeClass('opacity-0').addClass('opacity-100');

                    buttonLotes
                        .find('[data-title]')
                        .text($(this).find('[data-title]').text())
                        .addClass('font-semibold text-foreground');
                    buttonLotes.find('[data-subtitle]').text($(this).find('[data-subtitle]').text());
                    buttonLotes.next().toggle('fast');
                });

                lotesContainer.append($nuevoItem);
            });
        },
        error: function (error) {
            console.log('Error al obtener los lotes: ', error);
        },
    });
}

getLotes();
lotesSearchInput.on('input', function () {
    getLotes($(this).val());
});

// Cambios en ambos inputs

$(cantidad_real).on('input', function () {
    const cantidadReal = parseInt(cantidad_real.val()) || 0;
    const stockDisponible = selectedStock.cantidad_actual;

    const $icon = alertAudit.find('[data-icon]');
    const $title = alertAudit.find('[data-title]');
    const $stockSistema = alertAudit.find('[data-stock-sistema]');
    const $stockReal = alertAudit.find('[data-stock-real]');
    const $diferencia = alertAudit.find('[data-diferencia]');
    const $message = alertAudit.find('[data-message]');

    const diff = cantidadReal - stockDisponible;
    const porcentaje = Math.abs(diff / stockDisponible) * 100;

    if (diff === 0) {
        alertAudit.hide('fast');
        return;
    }

    const isMayor = diff > 0;
    const isLeve = porcentaje < 15;

    // Configuración centralizada
    const config = {
        mayor: {
            leve: {
                icon: variants.icons.success.svg,
                state: 'success',
                title: 'Stock Real Mayor',
            },
            grave: {
                icon: variants.icons.warning.svg,
                state: 'warning',
                title: 'Stock Real Mayor - Diferencia Significativa',
            },
        },
        menor: {
            leve: {
                icon: variants.icons.trendingDown.svg,
                state: 'warning',
                title: 'Stock Real Menor',
            },
            grave: {
                icon: variants.icons.error.svg,
                state: 'error',
                title: 'Stock Real Menor - Diferencia Significativa',
            },
        },
    };

    const type = isMayor ? 'mayor' : 'menor';
    const level = isLeve ? 'leve' : 'grave';
    const current = config[type][level];
    const state = variants.states[current.state];

    // Limpiar TODAS las clases antes de aplicar
    const cleanClasses = () => {
        const allStates = ['success', 'warning', 'error'];

        allStates.forEach((s) => {
            alertAudit.removeClass(variants.states[s].card.className);
            $title.removeClass(variants.states[s].text.className);
            $diferencia.removeClass(variants.states[s].text.className);

            $message.removeClass(variants.states[s].message.className + ' ' + variants.states[s].text.className);
        });
    };

    cleanClasses();

    // Aplicar nuevas clases
    alertAudit.addClass(state.card.className);
    $title.addClass(state.text.className);
    $diferencia.addClass(state.text.className);
    $message.addClass(state.message.className + ' ' + state.text.className);

    // Contenido
    $icon.html(current.icon);
    $title.text(current.title);

    $stockSistema.text(`${stockDisponible} ${selectedInventario.unidad_medida}`);
    $stockReal.text(`${cantidadReal} ${selectedInventario.unidad_medida}`);

    const absDiff = Math.abs(diff);

    $diferencia.text(
        `${isMayor ? '+' : '-'}${absDiff} ${selectedInventario.unidad_medida} (${porcentaje.toFixed(2)}%)`
    );

    $message.text(
        `Se registrará una ${isMayor ? 'ENTRADA' : 'SALIDA'} de ${absDiff} ${selectedInventario.unidad_medida} para ajustar el inventario.`
    );

    alertAudit.show('fast');
});

// Detectar cualquier cambio en el form, validar que todo esté lleno y los alerts de error no esten
// En dicho caso habilitar el boton caso contrario lo inhabilita

form.on('submit', function (e) {
    const cantidadReal = parseInt(cantidad_real.val()) || 0;
    const stockDisponible = selectedStock.cantidad_actual;

    const diff = cantidadReal - stockDisponible;

    if (diff === 0) {
        e.preventDefault();

        toast({
            message: 'El stock real es igual al stock del sistema',
            duration: 4000,
            autoClose: true,
            pauseOnHover: true,
        });
        return;
    }
});

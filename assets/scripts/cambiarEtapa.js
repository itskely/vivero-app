/* Peticion a /api/lotes.php y usar 
    el template #template-option con sus data- para llenar la informacion, 
    usarlo como selector cada opcion, marcar con su check la opcion seleccionada
    y mostrar la informacion del lote seleccionado en el boton lote_button a su vez cambiar el valor
    del input hidden llamado "lote" con el id de lote seleccionado
*/

const lotesSearchInput = $('#lotes-search-input');
const lotesSpinner = $('#lotes-spinner');
const lotesContainer = $('#lotes-container');
const template = $('#template-option').html();
const buttonLotes = $('#lote_button');
const inputHidden = $('#lote_id');
const availableStock = $('#available-stock');

const templateStock = $('#template-stock').html();
const stockContainer = $('#stock-container');

const inventario_id = $('#inventario_id');
const etapa_destino_id = $('#etapa_destino_id');
const ubi_destino_id = $('#ubi_destino_id');

const cantidad_salida = $('#cantidad_salida');
const cantidad_entrada = $('#cantidad_entrada');

// Alerts
const alertSuccess = $('#alert-success');
const alertMerma = $('#alert-merma');
const alertError = $('#alert-error');
const alertStockError = $('#alert-stock-error');

// Form
const form = $('#form-cambiar-etapa');

let selectedLote = null;
let selectedStock = null;

buttonLotes.click(function () {
    $(this).next().toggle('fast');
});

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
            data.forEach(function (lote) {
                var $nuevoItem = $(template).clone();
                $nuevoItem.attr('data-value', lote.id);
                $nuevoItem.find('[data-title]').text(`Lote #${lote.id}`);
                $nuevoItem.find('[data-subtitle]').text(lote.planta.nombre_comun);
                // $nuevoItem.find('[data-cantidad]').text(lote.cantidad);
                // $nuevoItem.find('[data-etapa]').text(lote.etapa);

                // Agregar evento de click a cada item, en caso de cliquear, guardamos el valor que tiene en el atributo value
                // luego seteamos el valor al input hidden llamado "lote" con el id de lote seleccionado
                // y mostramos la informacion del lote seleccionado en el boton lote_button
                $nuevoItem.click(function () {
                    selectedLote = lote;
                    selectedStock = null;
                    inputHidden.val($(this).attr('data-value'));
                    // Añadir a todos los demas la clase opacity-o y al que esta en 100
                    lotesContainer.find('[data-selected]').removeClass('opacity-100').addClass('opacity-0');
                    $(this).find('[data-selected]').removeClass('opacity-0').addClass('opacity-100');

                    buttonLotes
                        .find('[data-title]')
                        .text($(this).find('[data-title]').text())
                        .addClass('font-semibold text-foreground');
                    buttonLotes.find('[data-subtitle]').text($(this).find('[data-subtitle]').text());
                    buttonLotes.next().toggle('fast');

                    $.ajax({
                        url: '/api/inventario.php?lote_id=' + $(this).attr('data-value'),
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function () {
                            availableStock.hide('fast');
                            stockContainer.html('');
                        },
                        complete: function () {
                            availableStock.show('fast');
                        },
                        success: function (data) {
                            data.forEach(function (stock) {
                                var $nuevoItem = $(templateStock).clone();
                                $nuevoItem.find('[data-etapa]').text(stock.etapa.nombre);
                                $nuevoItem.find('[data-ubicacion]').text(stock.ubicacion.nombre);
                                $nuevoItem
                                    .find('[data-cantidad]')
                                    .text(stock.cantidad_actual + ' ' + selectedLote.unidad_medida);

                                $nuevoItem.click(function () {
                                    selectedStock = stock;
                                    ubi_destino_id.val(stock.ubicacion_id);
                                    inventario_id.val(stock.id);
                                    $('[data-stock-origen]').text(stock.cantidad_actual);
                                    $('[data-max-salida]').text(stock.cantidad_actual);

                                    stockContainer
                                        .find('[data-stock-item]')
                                        .removeClass('border-primary shadow-lg shadow-primary/30')
                                        .addClass('border-border');
                                    $(this)
                                        .removeClass('border-border')
                                        .addClass('border-primary shadow-lg shadow-primary/30');
                                });
                                stockContainer.append($nuevoItem);
                            });
                        },
                        error: function (error) {
                            console.log('Error al obtener los lotes: ', error);
                        },
                    });
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

$(cantidad_salida).on('input', function () {
    const cantidadSalida = parseInt(cantidad_salida.val());
    const cantidadEntrada = parseInt(cantidad_entrada.val());
    const stockOrigen = parseInt($('[data-stock-origen]').text());
    const stockDisponible = parseInt($('[data-stock-available]').text());

    console.log('Salida', cantidadSalida);
    console.log('Entrada', cantidadEntrada);
    if (cantidadSalida > stockOrigen) {
        alertStockError.show('fast');
        alertStockError.find('[data-stock-out-error]').text(cantidadSalida);
        alertStockError.find('[data-stock-available-error]').text(stockOrigen);
    } else {
        alertStockError.hide('fast');
    }

    if (cantidadEntrada > cantidadSalida) {
        alertError.show('fast');
    } else {
        alertError.hide('fast');
    }

    if (cantidadEntrada === cantidadSalida) {
        alertSuccess.show('fast');
        alertSuccess.find('[data-stock-out]').text(cantidadSalida + ' ' + selectedLote.unidad_medida);
        alertSuccess.find('[data-stock-in]').text(cantidadEntrada + ' ' + selectedLote.unidad_medida);
    } else {
        alertSuccess.hide('fast');
    }

    if (cantidadEntrada < cantidadSalida) {
        let porcentaje = ((cantidadSalida - cantidadEntrada) / cantidadSalida) * 100;
        alertMerma.show('fast');
        alertMerma.find('[data-stock-out]').text(cantidadSalida + ' ' + selectedLote.unidad_medida);
        alertMerma.find('[data-stock-in]').text(cantidadEntrada + ' ' + selectedLote.unidad_medida);
        alertMerma
            .find('[data-stock-merma]')
            .text(cantidadSalida - cantidadEntrada + ' ' + selectedLote.unidad_medida + ' (' + porcentaje + '%)');
        alertMerma.find('[data-stock-merma-porcentaje]').css('width', porcentaje + '%');
    } else {
        alertMerma.hide('fast');
    }
});

$(cantidad_entrada).on('input', function () {
    const cantidadSalida = parseInt(cantidad_salida.val());
    const cantidadEntrada = parseInt(cantidad_entrada.val());
    const stockOrigen = parseInt($('[data-stock-origen]').text());
    const stockDisponible = parseInt($('[data-stock-available]').text());

    console.log('Salida', cantidadSalida);
    console.log('Entrada', cantidadEntrada);
    if (cantidadSalida > stockOrigen) {
        alertStockError.show('fast');
        alertStockError.find('[data-stock-out-error]').text(cantidadSalida);
        alertStockError.find('[data-stock-available-error]').text(stockOrigen);
    } else {
        alertStockError.hide('fast');
    }

    if (cantidadEntrada > cantidadSalida) {
        alertError.show('fast');
    } else {
        alertError.hide('fast');
    }

    if (cantidadEntrada === cantidadSalida) {
        alertSuccess.show('fast');
        $('[data-stock-out]').text(cantidadSalida + ' ' + selectedLote.unidad_medida);
        $('[data-stock-in]').text(cantidadEntrada + ' ' + selectedLote.unidad_medida);
    } else {
        alertSuccess.hide('fast');
    }

    if (cantidadEntrada < cantidadSalida) {
        let porcentaje = ((cantidadSalida - cantidadEntrada) / cantidadSalida) * 100;
        alertMerma.show('fast');
        alertMerma.find('[data-stock-out]').text(cantidadSalida + ' ' + selectedLote.unidad_medida);
        alertMerma.find('[data-stock-in]').text(cantidadEntrada + ' ' + selectedLote.unidad_medida);
        alertMerma
            .find('[data-stock-merma]')
            .text(cantidadSalida - cantidadEntrada + ' ' + selectedLote.unidad_medida + ' (' + porcentaje + '%)');
        alertMerma.find('[data-stock-merma-porcentaje]').css('width', porcentaje + '%');
    } else {
        alertMerma.hide('fast');
    }
});

// Detectar cualquier cambio en el form, validar que todo esté lleno y los alerts de error no esten
// En dicho caso habilitar el boton caso contrario lo inhabilita

form.on('submit', function (e) {
    const cantidadSalida = parseInt(cantidad_salida.val());
    const cantidadEntrada = parseInt(cantidad_entrada.val());
    const stockOrigen = parseInt($('[data-stock-origen]').text());
    const stockDisponible = parseInt($('[data-stock-available]').text());

    if (!selectedLote || !selectedStock) {
        e.preventDefault();

        toast({
            message: 'Debe seleccionar un lote y un stock',
            duration: 4000,
            autoClose: true,
            pauseOnHover: true,
        });
        return;
    }

    if (!inventario_id.val()) {
        e.preventDefault();
        toast({
            message: 'Debe seleccionar un inventario',
            duration: 4000,
            autoClose: true,
            pauseOnHover: true,
        });
        return;
    }

    if (!etapa_destino_id.val()) {
        e.preventDefault();
        toast({
            message: 'Debe seleccionar una etapa destino',
            duration: 4000,
            autoClose: true,
            pauseOnHover: true,
        });
        return;
    }

    if (cantidadEntrada > cantidadSalida) {
        e.preventDefault();
        return;
    }

    if (cantidadSalida > stockOrigen) {
        e.preventDefault();
        return;
    }

    if (!cantidadSalida) {
        e.preventDefault();
        toast({
            message: 'Debe ingresar una cantidad de salida',
            duration: 4000,
            autoClose: true,
            pauseOnHover: true,
        });
        return;
    }
});

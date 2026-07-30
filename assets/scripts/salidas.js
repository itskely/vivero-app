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

const inventario_id = $('#inventario_id');
const etapa_destino_id = $('#etapa_destino_id');
const ubi_destino_id = $('#ubi_destino_id');

const cantidad_salida = $('#cantidad_salida');
const cantidad_entrada = $('#cantidad_entrada');

// Form
const form = $('#form-salidas');

let selectedInventario = null;

buttonLotes.click(function () {
    $(this).next().toggle('fast');
});

function formatoNumero(numero) {
    if (Math.floor(numero) === numero) {
        return Math.floor(numero);
    }
    return numero;
}

async function unificarLote(inventario) {
    let $form = new FormData();
    $form.append('planta_id', inventario.planta_id);
    $form.append('etapa_id', inventario.etapa_id);
    $form.append('ubicacion_origen_id', inventario.ubicacion_id);
    $form.append('ubicacion_destino_id', inventario.ubicacion_id);
    $form.append('motivo', `Unificación de ${inventario.lote_id}`);

    let $response = await fetch('/api/unificar-lote.php', {
        method: 'POST',
        body: $form,
    });

    let data = await $response.json();
    console.log(data);
    return data;
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
                if (inventario.etapa_id === 6) {
                    $nuevoItem.find('button').removeClass('hidden').addClass('flex');
                    $nuevoItem.find('button').click(async function (e) {
                        e.stopPropagation();
                        const spinnerEl = lotesSpinner.clone();
                        let icon = $(this).find('svg');
                        icon.addClass('hidden');

                        $nuevoItem.find('button').append(spinnerEl);
                        spinnerEl.removeClass('hidden absolute');
                        $nuevoItem.find('button').prop('disabled', true);

                        let data = await unificarLote(inventario);

                        toast({
                            message: data.message,
                            position: 'top-right',
                        });

                        icon.removeClass('hidden');
                        $nuevoItem.find('button').prop('disabled', false);
                        spinnerEl.remove();
                    });
                }
                $nuevoItem.attr('data-value', inventario.lote_id);
                $nuevoItem.find('[data-title]').text(`${inventario.etapa} - ${inventario.ubicacion}`);
                $nuevoItem
                    .find('[data-subtitle]')
                    .text(
                        `lote # ${inventario.lote_id}-${inventario.nombre_comun} (${formatoNumero(parseFloat(inventario.cantidad_actual))} ${inventario.unidad_medida})`
                    );
                // $nuevoItem.find('[data-cantidad]').text(lote.cantidad);
                // $nuevoItem.find('[data-etapa]').text(lote.etapa);

                // Agregar evento de click a cada item, en caso de cliquear, guardamos el valor que tiene en el atributo value
                // luego seteamos el valor al input hidden llamado "lote" con el id de lote seleccionado
                // y mostramos la informacion del lote seleccionado en el boton lote_button
                $nuevoItem.click(function () {
                    selectedInventario = inventario;
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

                    inventario_id.val(inventario.id);
                    $('[data-max-salida]').text(formatoNumero(parseFloat(inventario.cantidad_actual)));
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

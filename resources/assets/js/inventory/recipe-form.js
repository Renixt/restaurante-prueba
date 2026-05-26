/**
 * Recetas - Formulario de gestión de receta por platillo
 * Clonado y adaptado de: order-form.js
 */

'use strict';

$(function () {
  // -----------------------------------------------------------------------
  // Estado local
  // -----------------------------------------------------------------------
  var items = []; // { inventory_item_id, nombre, unit, quantity_required }

  // Cargar receta existente
  if (window.existingRecipe && window.existingRecipe.length) {
    window.existingRecipe.forEach(function (r) {
      addItem(r.inventory_item_id, r.name, r.unit, r.quantity_required);
    });
  }

  // -----------------------------------------------------------------------
  // Helpers
  // -----------------------------------------------------------------------
  function renderItems() {
    var $body    = $('#recipe-body');
    var $hidden  = $('#recipe-hidden');
    var $emptyRow = $('#recipe-empty-row');

    $body.find('tr:not(#recipe-empty-row)').remove();
    $hidden.empty();

    if (items.length === 0) {
      if ($emptyRow.length === 0) {
        $body.append('<tr id="recipe-empty-row"><td colspan="3" class="text-center text-muted py-4">' +
          '<i class="icon-base ti tabler-clipboard-list icon-24px d-block mb-1"></i>Agrega insumos a la receta</td></tr>');
      } else {
        $emptyRow.show();
      }
    } else {
      if ($emptyRow.length) $emptyRow.hide();

      items.forEach(function (item, idx) {
        var row =
          '<tr data-idx="' + idx + '">' +
          '<td class="align-middle fw-medium">' + item.nombre + '</td>' +
          '<td class="text-center align-middle">' +
          '<input type="number" class="form-control form-control-sm text-center qty-recipe-input" ' +
          'value="' + item.quantity_required + '" min="0.001" step="0.001" data-idx="' + idx + '" style="width:90px;margin:auto;">' +
          ' <small class="text-muted">' + item.unit + '</small>' +
          '</td>' +
          '<td class="text-center align-middle">' +
          '<button type="button" class="btn btn-icon btn-text-danger btn-sm remove-recipe-item" data-idx="' + idx + '">' +
          '<i class="icon-base ti tabler-x icon-18px"></i></button>' +
          '</td></tr>';
        $body.append(row);

        $hidden.append(
          '<input type="hidden" name="recipe[' + idx + '][inventory_item_id]" value="' + item.inventory_item_id + '">' +
          '<input type="hidden" name="recipe[' + idx + '][quantity_required]" class="hidden-qty-recipe" data-idx="' + idx + '" value="' + item.quantity_required + '">'
        );
      });
    }
  }

  function addItem(inventoryItemId, nombre, unit, quantityRequired) {
    quantityRequired = parseFloat(quantityRequired) || 1;
    var existing = items.find(function (i) { return i.inventory_item_id === inventoryItemId; });
    if (existing) {
      existing.quantity_required = quantityRequired;
    } else {
      items.push({ inventory_item_id: inventoryItemId, nombre: nombre, unit: unit, quantity_required: quantityRequired });
    }
    renderItems();
  }

  // -----------------------------------------------------------------------
  // Botón agregar insumo
  // -----------------------------------------------------------------------
  $('#btn-agregar-insumo').on('click', function () {
    var $select  = $('#select-insumo');
    var selected = $select.val();
    var $option  = $select.find('option:selected');
    var qty      = parseFloat($('#input-cantidad-receta').val()) || 1;

    if (!selected) {
      $select.addClass('is-invalid');
      return;
    }
    $select.removeClass('is-invalid');

    var inventoryItemId = parseInt(selected);
    var nombre = $option.data('nombre');
    var unit   = $option.data('unit');

    addItem(inventoryItemId, nombre, unit, qty);

    $select.val('').trigger('change');
    $('#input-cantidad-receta').val(1);
  });

  // Quitar insumo
  $(document).on('click', '.remove-recipe-item', function () {
    items.splice(parseInt($(this).data('idx')), 1);
    renderItems();
  });

  // Cambiar cantidad en tabla
  $(document).on('change', '.qty-recipe-input', function () {
    var idx = parseInt($(this).data('idx'));
    var qty = parseFloat($(this).val()) || 0.001;
    if (qty < 0.001) qty = 0.001;
    $(this).val(qty);
    items[idx].quantity_required = qty;
    $('input[name="recipe[' + idx + '][quantity_required]"]').val(qty);
  });
});

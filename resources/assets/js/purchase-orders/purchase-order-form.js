/**
 * Pedidos a Proveedores - Formulario (Crear / Editar)
 * Clonado y adaptado de: order-form.js
 */

'use strict';

$(function () {
  // -----------------------------------------------------------------------
  // Select2
  // -----------------------------------------------------------------------
  const select2Elements = $('.select2');
  if (select2Elements.length) {
    select2Elements.each(function () {
      var $el = $(this);
      $el.wrap('<div class="position-relative"></div>').select2({
        placeholder: $el.data('placeholder') || 'Selecciona...',
        dropdownParent: $el.parent(),
      });
    });
  }

  // -----------------------------------------------------------------------
  // Estado local
  // -----------------------------------------------------------------------
  var items = []; // { inventory_item_id, nombre, unit, quantity, unit_cost, total }

  // Cargar items existentes (edición)
  if (window.existingPoItems && window.existingPoItems.length) {
    window.existingPoItems.forEach(function (item) {
      addItem(item.inventory_item_id, item.name, item.unit, item.quantity, item.unit_cost);
    });
  }

  // -----------------------------------------------------------------------
  // Helpers
  // -----------------------------------------------------------------------
  function formatCurrency(val) {
    return '$' + parseFloat(val).toFixed(2);
  }

  function renderItems() {
    var $body     = $('#po-items-body');
    var $hidden   = $('#po-items-hidden');
    var $emptyRow = $('#po-empty-row');

    $body.find('tr:not(#po-empty-row)').remove();
    $hidden.empty();

    if (items.length === 0) {
      if ($emptyRow.length === 0) {
        $body.append('<tr id="po-empty-row"><td colspan="6" class="text-center text-muted py-4">' +
          '<i class="icon-base ti tabler-package icon-24px d-block mb-1"></i>Agrega insumos al pedido</td></tr>');
      } else {
        $emptyRow.show();
      }
    } else {
      if ($emptyRow.length) $emptyRow.hide();

      items.forEach(function (item, idx) {
        var row =
          '<tr data-idx="' + idx + '">' +
          '<td class="align-middle fw-medium">' + item.nombre + '</td>' +
          '<td class="text-center align-middle text-muted">' + item.unit + '</td>' +
          '<td class="text-center align-middle">' +
          '<input type="number" class="form-control form-control-sm text-center po-qty-input" ' +
          'value="' + item.quantity + '" min="0.001" step="0.001" data-idx="' + idx + '" style="width:80px;margin:auto;">' +
          '</td>' +
          '<td class="text-center align-middle">' +
          '<div class="input-group input-group-sm" style="width:110px;margin:auto;">' +
          '<span class="input-group-text">$</span>' +
          '<input type="number" class="form-control text-center po-cost-input" ' +
          'value="' + item.unit_cost + '" min="0" step="0.01" data-idx="' + idx + '">' +
          '</div>' +
          '</td>' +
          '<td class="text-center align-middle fw-medium po-line-total">' + formatCurrency(item.total) + '</td>' +
          '<td class="text-center align-middle">' +
          '<button type="button" class="btn btn-icon btn-text-danger btn-sm remove-po-item" data-idx="' + idx + '">' +
          '<i class="icon-base ti tabler-x icon-18px"></i></button></td></tr>';
        $body.append(row);

        $hidden.append(
          '<input type="hidden" name="items[' + idx + '][inventory_item_id]" value="' + item.inventory_item_id + '">' +
          '<input type="hidden" name="items[' + idx + '][quantity]" class="hidden-qty-po" data-idx="' + idx + '" value="' + item.quantity + '">' +
          '<input type="hidden" name="items[' + idx + '][unit_cost]" class="hidden-cost-po" data-idx="' + idx + '" value="' + item.unit_cost + '">'
        );
      });
    }

    updateSummary();
  }

  function addItem(inventoryItemId, nombre, unit, quantity, unitCost) {
    quantity = parseFloat(quantity) || 1;
    unitCost = parseFloat(unitCost) || 0;

    var existing = items.find(function (i) { return i.inventory_item_id === inventoryItemId; });
    if (existing) {
      existing.quantity += quantity;
      existing.total = existing.unit_cost * existing.quantity;
    } else {
      items.push({
        inventory_item_id: inventoryItemId,
        nombre: nombre,
        unit: unit,
        quantity: quantity,
        unit_cost: unitCost,
        total: quantity * unitCost,
      });
    }
    renderItems();
  }

  function updateSummary() {
    var totalPrice = 0;
    items.forEach(function (item) { totalPrice += item.total; });
    var $total = $('#po-summary-total');
    if ($total.length) $total.text(formatCurrency(totalPrice));
  }

  // -----------------------------------------------------------------------
  // Botón agregar
  // -----------------------------------------------------------------------
  $('#btn-agregar-insumo-po').on('click', function () {
    var $select   = $('#select-insumo-po');
    var selected  = $select.val();
    var $option   = $select.find('option:selected');
    var qty       = parseFloat($('#input-qty-po').val()) || 1;
    var cost      = parseFloat($('#input-cost-po').val()) || 0;

    if (!selected) {
      $select.next('.select2-container').find('.select2-selection').addClass('is-invalid');
      return;
    }
    $select.next('.select2-container').find('.select2-selection').removeClass('is-invalid');

    var inventoryItemId = parseInt(selected);
    var nombre = $option.data('nombre');
    var unit   = $option.data('unit');

    addItem(inventoryItemId, nombre, unit, qty, cost);

    $select.val('').trigger('change');
    $('#input-qty-po').val(1);
    $('#input-cost-po').val(0);
  });

  // Auto-rellenar costo desde window.inventoryItemsData al seleccionar insumo
  $('#select-insumo-po').on('change', function () {
    var id = parseInt($(this).val());
    if (id && window.inventoryItemsData && window.inventoryItemsData[id]) {
      $('#input-cost-po').val(window.inventoryItemsData[id].cost || 0);
    }
  });

  // Quitar item
  $(document).on('click', '.remove-po-item', function () {
    items.splice(parseInt($(this).data('idx')), 1);
    renderItems();
  });

  // Cambiar cantidad
  $(document).on('change', '.po-qty-input', function () {
    var idx = parseInt($(this).data('idx'));
    var qty = parseFloat($(this).val()) || 0.001;
    if (qty < 0.001) qty = 0.001;
    $(this).val(qty);
    items[idx].quantity = qty;
    items[idx].total    = items[idx].unit_cost * qty;
    $(this).closest('tr').find('.po-line-total').text(formatCurrency(items[idx].total));
    $('input[name="items[' + idx + '][quantity]"]').val(qty);
    updateSummary();
  });

  // Cambiar costo unitario
  $(document).on('change', '.po-cost-input', function () {
    var idx  = parseInt($(this).data('idx'));
    var cost = parseFloat($(this).val()) || 0;
    items[idx].unit_cost = cost;
    items[idx].total     = cost * items[idx].quantity;
    $(this).closest('tr').find('.po-line-total').text(formatCurrency(items[idx].total));
    $('input[name="items[' + idx + '][unit_cost]"]').val(cost);
    updateSummary();
  });

  // Validar al enviar
  $('#po-form').on('submit', function (e) {
    if (items.length === 0) {
      e.preventDefault();
      var $alert = $('<div class="alert alert-danger alert-dismissible mt-3">' +
        'Debes agregar al menos un insumo al pedido.' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
      $('#po-items-table').before($alert);
      $alert[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
});

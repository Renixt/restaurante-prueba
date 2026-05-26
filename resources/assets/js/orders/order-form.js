/**
 * Órdenes - Formulario (Crear / Editar)
 * Clonado y adaptado de: forms-selects.js
 */

'use strict';

$(function () {
  // -----------------------------------------------------------------------
  // Select2 — patrón de forms-selects.js
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
  // Toggle sección mesa según tipo
  // -----------------------------------------------------------------------
  function toggleMesaSection() {
    const tipo       = $('input[name="type"]:checked').val();
    const $mesaSection = $('#mesa-section');
    if ($mesaSection.length) {
      if (tipo === 'mesa') {
        $mesaSection.show();
      } else {
        $mesaSection.hide();
        $('#mesa_id').val('').trigger('change');
      }
    }
  }

  $('input[name="type"]').on('change', toggleMesaSection);
  toggleMesaSection(); // estado inicial

  // -----------------------------------------------------------------------
  // Gestión dinámica de items de la orden
  // -----------------------------------------------------------------------
  var itemCounter = 0;
  var items = []; // { menu_item_id, nombre, unit_price, quantity, total }

  // Cargar items existentes (edición)
  if (window.existingItems && window.existingItems.length) {
    window.existingItems.forEach(function (item) {
      addItem(item.menu_item_id, item.nombre, item.unit_price, item.quantity);
    });
  }

  function formatCurrency(val) {
    return '$' + parseFloat(val).toFixed(2);
  }

  function renderItems() {
    var $body    = $('#items-body');
    var $hidden  = $('#items-hidden');
    var $emptyRow = $('#empty-row');

    $body.find('tr:not(#empty-row)').remove();
    $hidden.empty();

    if (items.length === 0) {
      if ($emptyRow.length === 0) {
        $body.append(
          '<tr id="empty-row"><td colspan="5" class="text-center text-muted py-4">' +
          '<i class="icon-base ti tabler-shopping-cart icon-24px d-block mb-1"></i>' +
          'Agrega platillos a la orden</td></tr>'
        );
      } else {
        $emptyRow.show();
      }
    } else {
      if ($emptyRow.length) $emptyRow.hide();

      items.forEach(function (item, idx) {
        var row =
          '<tr data-idx="' + idx + '">' +
          '<td class="align-middle fw-medium">' + item.nombre + '</td>' +
          '<td class="text-center align-middle">' + formatCurrency(item.unit_price) + '</td>' +
          '<td class="text-center align-middle">' +
          '<input type="number" class="form-control form-control-sm text-center qty-input" ' +
          'value="' + item.quantity + '" min="1" max="99" data-idx="' + idx + '" style="width:70px;margin:auto;">' +
          '</td>' +
          '<td class="text-center align-middle fw-medium line-total">' + formatCurrency(item.total) + '</td>' +
          '<td class="text-center align-middle">' +
          '<button type="button" class="btn btn-icon btn-text-danger btn-sm remove-item" data-idx="' + idx + '">' +
          '<i class="icon-base ti tabler-x icon-18px"></i></button>' +
          '</td>' +
          '</tr>';
        $body.append(row);

        // Hidden inputs para envío del formulario
        $hidden.append(
          '<input type="hidden" name="items[' + idx + '][menu_item_id]" value="' + item.menu_item_id + '">' +
          '<input type="hidden" name="items[' + idx + '][quantity]" class="hidden-qty" data-idx="' + idx + '" value="' + item.quantity + '">'
        );
      });
    }

    updateSummary();
  }

  function addItem(menuItemId, nombre, unitPrice, quantity) {
    quantity = parseInt(quantity) || 1;

    // Si ya existe, incrementar cantidad
    var existing = items.find(function (i) { return i.menu_item_id === menuItemId; });
    if (existing) {
      existing.quantity += quantity;
      existing.total = existing.unit_price * existing.quantity;
    } else {
      items.push({
        menu_item_id: menuItemId,
        nombre: nombre,
        unit_price: parseFloat(unitPrice),
        quantity: quantity,
        total: parseFloat(unitPrice) * quantity,
      });
    }

    renderItems();
  }

  function updateSummary() {
    var totalQty   = 0;
    var totalPrice = 0;

    items.forEach(function (item) {
      totalQty   += item.quantity;
      totalPrice += item.total;
    });

    var $total      = $('#summary-total');
    var $count      = $('#summary-count');
    var $perPerson  = $('#per-person');
    var splitCount  = parseInt($('#split_count').val()) || 1;

    if ($total.length)     $total.text(formatCurrency(totalPrice));
    if ($count.length)     $count.text(totalQty);
    if ($perPerson.length) $perPerson.text(formatCurrency(totalPrice / splitCount));
  }

  // Botón agregar item
  $('#btn-agregar-item').on('click', function () {
    var $select   = $('#select-platillo');
    var selected  = $select.val();
    var $option   = $select.find('option:selected');
    var quantity  = parseInt($('#input-cantidad').val()) || 1;

    if (!selected) {
      $select.next('.select2-container').find('.select2-selection').addClass('is-invalid');
      return;
    }

    $select.next('.select2-container').find('.select2-selection').removeClass('is-invalid');

    var menuItemId = parseInt(selected);
    var nombre     = $option.data('nombre');
    var precio     = parseFloat($option.data('precio'));

    addItem(menuItemId, nombre, precio, quantity);

    // Resetear selector
    $select.val('').trigger('change');
    $('#input-cantidad').val(1);
  });

  // Quitar item
  $(document).on('click', '.remove-item', function () {
    var idx = parseInt($(this).data('idx'));
    items.splice(idx, 1);
    renderItems();
  });

  // Cambiar cantidad en tabla
  $(document).on('change', '.qty-input', function () {
    var idx = parseInt($(this).data('idx'));
    var qty = parseInt($(this).val()) || 1;
    if (qty < 1) qty = 1;
    if (qty > 99) qty = 99;
    $(this).val(qty);

    items[idx].quantity = qty;
    items[idx].total    = items[idx].unit_price * qty;

    // Actualizar visual de línea
    $(this).closest('tr').find('.line-total').text(formatCurrency(items[idx].total));
    $('input[name="items[' + idx + '][quantity]"]').val(qty);
    updateSummary();
  });

  // Recalcular por persona al cambiar split_count
  $('#split_count').on('input change', updateSummary);

  // Validación al enviar: al menos 1 item
  $('#order-form').on('submit', function (e) {
    if (items.length === 0) {
      e.preventDefault();
      var $alert = $('<div class="alert alert-danger alert-dismissible mt-3">' +
        'Debes agregar al menos un platillo a la orden.' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
      $('#items-table').before($alert);
      $alert[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
});

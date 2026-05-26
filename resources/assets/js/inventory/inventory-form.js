/**
 * Inventario - Formulario Insumo (Crear / Editar)
 * Clonado de: menu-item-form.js
 */

'use strict';

$(function () {
  const select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: $this.data('placeholder') || 'Selecciona...',
        dropdownParent: $this.parent(),
      });
    });
  }
});

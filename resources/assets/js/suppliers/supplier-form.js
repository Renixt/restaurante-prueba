/**
 * Proveedores - Formulario
 * Clonado de: menu-item-form.js
 */

'use strict';

$(function () {
  // RFC: forzar mayúsculas al escribir
  const rfcInput = document.getElementById('sup-rfc');
  if (rfcInput) {
    rfcInput.addEventListener('input', function () {
      this.value = this.value.toUpperCase();
    });
  }
});

/**
 * Menú - Formulario (Crear / Editar)
 * Clonado y adaptado de: forms-selects.js
 */

'use strict';

$(function () {
  // Select2 para categoría — patrón de forms-selects.js
  const select2 = $('.select2');

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Selecciona una categoría',
        dropdownParent: $this.parent(),
      });
    });
  }

  // Vista previa de imagen seleccionada
  const inputImagen   = document.getElementById('menu-imagen');
  const previewWrap   = document.getElementById('menu-imagen-preview');
  const previewImg    = previewWrap ? previewWrap.querySelector('img') : null;

  if (inputImagen && previewWrap && previewImg) {
    inputImagen.addEventListener('change', function () {
      const file = inputImagen.files[0];
      if (!file) {
        previewWrap.classList.add('d-none');
        previewImg.src = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewWrap.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    });
  }
});

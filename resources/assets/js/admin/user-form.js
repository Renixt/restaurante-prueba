'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Password strength hint — no external lib needed
  const pwd = document.getElementById('password');
  if (pwd) {
    pwd.addEventListener('input', function () {
      this.setCustomValidity(this.value.length > 0 && this.value.length < 8 ? 'Mínimo 8 caracteres' : '');
    });
  }
});

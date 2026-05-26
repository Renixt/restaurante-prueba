/**
 * Proveedores - Listado
 * Clonado y adaptado de: menu-item-list.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_table = document.querySelector('.datatables-suppliers');
  if (!dt_table) return;

  const csrfToken    = dt_table.dataset.csrf;
  const suppliersBase = dt_table.dataset.suppliersBase;

  const statusObj = {
    'activo':   { title: 'Activo',   class: 'bg-label-success' },
    'inactivo': { title: 'Inactivo', class: 'bg-label-secondary' },
  };

  const dt = new DataTable(dt_table, {
    ajax: { url: suppliersBase + '-data', type: 'GET' },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'business_name' },
      { data: 'rfc' },
      { data: 'phone' },
      { data: 'email' },
      { data: 'items_count' },
      { data: 'orders_count' },
      { data: 'status_label' },
      { data: 'id', orderable: false, searchable: false },
    ],
    columnDefs: [
      { className: 'control', searchable: false, orderable: false, responsivePriority: 2, targets: 0, render: () => '' },
      { targets: 2, responsivePriority: 1, render: (d, t, f) => `<span class="fw-medium text-heading">${f['business_name']}</span>` },
      { targets: 3, render: (d, t, f) => `<code>${f['rfc']}</code>` },
      { targets: 6, render: (d, t, f) => `<span class="badge bg-label-primary">${f['items_count']}</span>` },
      { targets: 7, render: (d, t, f) => `<span class="badge bg-label-info">${f['orders_count']}</span>` },
      {
        targets: 8,
        render: (d, t, f) => {
          const s = statusObj[f['status']] || statusObj['inactivo'];
          return `<span class="badge ${s.class}">${s.title}</span>`;
        },
      },
      {
        targets: -1, title: 'Acciones', searchable: false, orderable: false,
        render: (d, t, f) => {
          const id = f['id'];
          return `<div class="d-flex align-items-center">
            <a href="${suppliersBase}/${id}/edit" class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Editar">
              <i class="icon-base ti tabler-edit icon-22px"></i></a>
            <a href="javascript:;" data-id="${id}" class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-supplier" title="Eliminar">
              <i class="icon-base ti tabler-trash icon-22px"></i></a>
          </div>`;
        },
      },
    ],
    order: [[2, 'asc']],
    layout: {
      topStart: { rowClass: 'row m-3 my-0 justify-content-between', features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }] },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar proveedor', text: '_INPUT_' } },
          {
            buttons: [
              {
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-plus icon-xs"></i><span class="d-none d-sm-inline-block">Nuevo Proveedor</span></span>',
                className: 'btn btn-primary',
                action: () => { window.location.href = suppliersBase + '/create'; },
              },
            ],
          },
        ],
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging',
    },
    language: {
      sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Buscar proveedor',
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
      },
    },
    responsive: { details: { display: DataTable.Responsive.display.modal({ header: row => row.data()['business_name'] }), type: 'column' } },
    initComplete: function () {
      const api = this.api();
      const selStatus = document.createElement('select');
      selStatus.className = 'form-select';
      selStatus.innerHTML = '<option value="">Todos</option><option value="Activo">Activo</option><option value="Inactivo">Inactivo</option>';
      document.querySelector('.sup_status').appendChild(selStatus);
      selStatus.addEventListener('change', () => {
        api.column(8).search(selStatus.value ? '^' + selStatus.value + '$' : '', true, false).draw();
      });
    },
  });

  function bindDeleteEvents() {
    const tableBody = dt_table.querySelector('tbody');
    function handleDelete(id, row) {
      if (!confirm('¿Eliminar este proveedor?')) return;
      fetch(suppliersBase + '/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      }).then(res => {
        if (res.ok) { dt.row(row).remove().draw(); }
        else { res.json().then(d => alert(d.message || 'No se pudo eliminar.')); }
      }).catch(() => alert('Error de conexión.'));
    }
    if (tableBody) {
      tableBody.addEventListener('click', e => {
        const btn = e.target.closest('.delete-supplier');
        if (btn) handleDelete(btn.dataset.id, btn.closest('tr'));
      });
    }
  }

  bindDeleteEvents();

  setTimeout(() => {
    [
      { selector: '.dt-buttons .btn',        classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length .form-select',  classToRemove: 'form-select-sm', classToAdd: 'ms-0' },
      { selector: '.dt-length',               classToAdd: 'mb-md-6 mb-0' },
      { selector: '.dt-layout-end',           classToRemove: 'justify-content-between', classToAdd: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap' },
      { selector: '.dt-buttons',              classToAdd: 'd-flex gap-4 mb-md-0 mb-4' },
      { selector: '.dt-layout-table',         classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full',          classToRemove: 'col-md col-12', classToAdd: 'table-responsive' },
    ].forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(el => {
        if (classToRemove) classToRemove.split(' ').forEach(c => el.classList.remove(c));
        if (classToAdd)    classToAdd.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);
});

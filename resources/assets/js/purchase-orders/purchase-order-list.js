/**
 * Pedidos a Proveedores - Listado
 * Clonado y adaptado de: menu-item-list.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_table = document.querySelector('.datatables-purchase-orders');
  if (!dt_table) return;

  const csrfToken = dt_table.dataset.csrf;
  const poBase    = dt_table.dataset.poBase;

  const dt = new DataTable(dt_table, {
    ajax: { url: poBase + '-data', type: 'GET' },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'folio' },
      { data: 'supplier' },
      { data: 'status_label' },
      { data: 'total' },
      { data: 'delivery_date' },
      { data: 'created_at' },
      { data: 'id', orderable: false, searchable: false },
    ],
    columnDefs: [
      { className: 'control', searchable: false, orderable: false, responsivePriority: 2, targets: 0, render: () => '' },
      { targets: 2, responsivePriority: 1, render: (d, t, f) => `<span class="fw-medium text-heading">${f['folio']}</span>` },
      { targets: 4, render: (d, t, f) => `<span class="badge ${f['status_class']}">${f['status_label']}</span>` },
      { targets: 5, render: (d, t, f) => `<span class="fw-medium">$${f['total']}</span>` },
      {
        targets: -1, title: 'Acciones', searchable: false, orderable: false,
        render: (d, t, f) => {
          const id = f['id'];
          let html = `<div class="d-flex align-items-center">
            <a href="${poBase}/${id}" class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Ver">
              <i class="icon-base ti tabler-eye icon-22px"></i></a>`;
          if (f['can_edit']) {
            html += `<a href="${poBase}/${id}/edit" class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Editar">
              <i class="icon-base ti tabler-edit icon-22px"></i></a>`;
          }
          html += `<a href="javascript:;" data-id="${id}" class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-po" title="Eliminar">
              <i class="icon-base ti tabler-trash icon-22px"></i></a>
          </div>`;
          return html;
        },
      },
    ],
    order: [[0, 'desc']],
    layout: {
      topStart: { rowClass: 'row m-3 my-0 justify-content-between', features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }] },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar pedido', text: '_INPUT_' } },
          {
            buttons: [
              {
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-plus icon-xs"></i><span class="d-none d-sm-inline-block">Nuevo Pedido</span></span>',
                className: 'btn btn-primary',
                action: () => { window.location.href = poBase + '/create'; },
              },
            ],
          },
        ],
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging',
    },
    language: {
      sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Buscar pedido',
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
      },
    },
    responsive: { details: { display: DataTable.Responsive.display.modal({ header: row => 'Pedido ' + row.data()['folio'] }), type: 'column' } },
    initComplete: function () {
      const api = this.api();
      const selStatus = document.createElement('select');
      selStatus.className = 'form-select';
      selStatus.innerHTML = '<option value="">Todos los estados</option><option value="Pendiente">Pendiente</option><option value="Enviado">Enviado</option><option value="Recibido">Recibido</option><option value="Cancelado">Cancelado</option>';
      document.querySelector('.po_status').appendChild(selStatus);
      selStatus.addEventListener('change', () => {
        api.column(4).search(selStatus.value ? '^' + selStatus.value + '$' : '', true, false).draw();
      });
    },
  });

  function bindDeleteEvents() {
    const tableBody = dt_table.querySelector('tbody');
    function handleDelete(id, row) {
      if (!confirm('¿Eliminar este pedido?')) return;
      fetch(poBase + '/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      }).then(res => {
        if (res.ok) { dt.row(row).remove().draw(); }
        else { res.json().then(d => alert(d.message || 'No se pudo eliminar.')); }
      }).catch(() => alert('Error de conexión.'));
    }
    if (tableBody) {
      tableBody.addEventListener('click', e => {
        const btn = e.target.closest('.delete-po');
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

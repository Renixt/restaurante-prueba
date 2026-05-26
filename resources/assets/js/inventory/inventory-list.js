/**
 * Inventario - Listado de Insumos
 * Clonado y adaptado de: menu-item-list.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_table = document.querySelector('.datatables-inventory');
  if (!dt_table) return;

  const csrfToken    = dt_table.dataset.csrf;
  const inventoryBase = dt_table.dataset.inventoryBase;

  const activeObj = {
    true:  { title: 'Activo',   class: 'bg-label-success' },
    false: { title: 'Inactivo', class: 'bg-label-secondary' },
  };

  const dt = new DataTable(dt_table, {
    ajax: { url: inventoryBase + '-data', type: 'GET' },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'name' },
      { data: 'sku' },
      { data: 'unit_label' },
      { data: 'current_stock' },
      { data: 'minimum_stock' },
      { data: 'cost' },
      { data: 'supplier' },
      { data: 'is_active' },
      { data: 'id', orderable: false, searchable: false },
    ],
    columnDefs: [
      { className: 'control', searchable: false, orderable: false, responsivePriority: 2, targets: 0, render: () => '' },
      {
        targets: 2,
        responsivePriority: 1,
        render: (data, type, full) => {
          const isLow = full['low_stock'];
          return `<span class="fw-medium text-heading">${full['name']}</span>` +
            (isLow ? ' <span class="badge bg-label-danger ms-1">Stock bajo</span>' : '');
        },
      },
      { targets: 3, render: (d, t, f) => `<span class="text-muted">${f['sku']}</span>` },
      { targets: 5, render: (d, t, f) => `<span class="${f['low_stock'] ? 'text-danger fw-bold' : 'fw-medium'}">${f['current_stock']}</span>` },
      { targets: 7, render: (d, t, f) => `<span class="fw-medium">$${f['cost']}</span>` },
      {
        targets: 9,
        render: (d, t, f) => {
          const key = String(f['is_active']);
          const s = activeObj[key] || activeObj['false'];
          return `<span class="badge ${s.class}">${s.title}</span>`;
        },
      },
      {
        targets: -1, title: 'Acciones', searchable: false, orderable: false,
        render: (d, t, f) => {
          const id = f['id'];
          return `<div class="d-flex align-items-center">
            <a href="${inventoryBase}/${id}/edit" class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Editar">
              <i class="icon-base ti tabler-edit icon-22px"></i></a>
            <a href="${inventoryBase}/${id}/movements" class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Movimientos">
              <i class="icon-base ti tabler-history icon-22px"></i></a>
            <a href="javascript:;" data-id="${id}" class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-inventory" title="Eliminar">
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
          { search: { placeholder: 'Buscar insumo', text: '_INPUT_' } },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle',
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-upload icon-xs"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  { extend: 'print', text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-1"></i>Imprimir</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6,7,8,9] } },
                  { extend: 'csv',   text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>CSV</span>',      className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6,7,8,9] } },
                  { extend: 'excel', text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>Excel</span>', className: 'dropdown-item', exportOptions: { columns: [2,3,4,5,6,7,8,9] } },
                ],
              },
              {
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-plus icon-xs"></i><span class="d-none d-sm-inline-block">Nuevo Insumo</span></span>',
                className: 'btn btn-primary',
                action: () => { window.location.href = inventoryBase + '/create'; },
              },
            ],
          },
        ],
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging',
    },
    language: {
      sLengthMenu: '_MENU_', search: '', searchPlaceholder: 'Buscar insumo',
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
      },
    },
    responsive: { details: { display: DataTable.Responsive.display.modal({ header: row => 'Insumo: ' + row.data()['name'] }), type: 'column' } },
    initComplete: function () {
      const api = this.api();

      // Filtro por unidad
      const colUnit = api.column(4);
      const selUnit = document.createElement('select');
      selUnit.className = 'form-select text-capitalize';
      selUnit.innerHTML = '<option value="">Todas las unidades</option>';
      Array.from(new Set(colUnit.data().toArray())).sort().forEach(u => {
        selUnit.innerHTML += `<option value="${u}">${u}</option>`;
      });
      document.querySelector('.inv_unit').appendChild(selUnit);
      selUnit.addEventListener('change', () => {
        colUnit.search(selUnit.value ? '^' + selUnit.value + '$' : '', true, false).draw();
      });

      // Filtro por activo
      const selActive = document.createElement('select');
      selActive.className = 'form-select';
      selActive.innerHTML = '<option value="">Todos</option><option value="Activo">Activo</option><option value="Inactivo">Inactivo</option>';
      document.querySelector('.inv_active').appendChild(selActive);
      selActive.addEventListener('change', () => {
        api.column(9).search(selActive.value ? '^' + selActive.value + '$' : '', true, false).draw();
      });

      // Filtro por stock
      const selStock = document.createElement('select');
      selStock.className = 'form-select';
      selStock.innerHTML = '<option value="all">Todo el stock</option><option value="low">Stock bajo</option>';
      document.querySelector('.inv_stock').appendChild(selStock);
      selStock.addEventListener('change', () => {
        if (selStock.value === 'low') {
          api.rows().every(function() { if (!this.data()['low_stock']) this.node() && $(this.node()).hide(); });
          dt.draw();
        } else {
          dt.draw();
        }
      });
    },
  });

  // Eliminar insumo
  function bindDeleteEvents() {
    const tableBody = dt_table.querySelector('tbody');
    function handleDelete(id, row) {
      if (!confirm('¿Eliminar este insumo?')) return;
      fetch(inventoryBase + '/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      }).then(res => {
        if (res.ok) { dt.row(row).remove().draw(); }
        else { res.json().then(d => alert(d.message || 'No se pudo eliminar.')); }
      }).catch(() => alert('Error de conexión.'));
    }
    if (tableBody) {
      tableBody.addEventListener('click', e => {
        const btn = e.target.closest('.delete-inventory');
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

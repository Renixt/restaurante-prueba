/**
 * Órdenes - Listado
 * Clonado y adaptado de: tables-datatables-basic.js (primera tabla)
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_orders_table = document.querySelector('.datatables-orders');

  if (!dt_orders_table) return;

  const csrfToken  = dt_orders_table.dataset.csrf;
  const ordersBase = dt_orders_table.dataset.ordersBase;

  const statusObj = {
    pendiente:       { label: 'Pendiente',      class: 'bg-label-warning' },
    en_preparacion:  { label: 'En preparación', class: 'bg-label-info' },
    listo:           { label: 'Listo',          class: 'bg-label-success' },
    entregado:       { label: 'Entregado',       class: 'bg-label-primary' },
    pagado:          { label: 'Pagado',          class: 'bg-label-secondary' },
  };

  const dt_orders = new DataTable(dt_orders_table, {
    ajax: {
      url: ordersBase + '-data',
      type: 'GET',
    },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'folio' },
      { data: 'mesa' },
      { data: 'status_label' },
      { data: 'total' },
      { data: 'payment_method' },
      { data: 'created_at' },
      { data: 'id', orderable: false, searchable: false },
    ],
    columnDefs: [
      {
        className: 'control',
        searchable: false,
        orderable: false,
        responsivePriority: 2,
        targets: 0,
        render: function () { return ''; },
      },
      {
        targets: 1,
        orderable: false,
        searchable: false,
        responsivePriority: 4,
        checkboxes: true,
        render: function () {
          return '<input type="checkbox" class="dt-checkboxes form-check-input">';
        },
        checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
      },
      {
        // Folio
        targets: 2,
        responsivePriority: 1,
        render: function (data, type, full) {
          return '<span class="fw-medium text-heading">' + full['folio'] + '</span>';
        },
      },
      {
        // Mesa
        targets: 3,
        render: function (data, type, full) {
          const icon = full['type'] === 'mesa'
            ? '<i class="icon-base ti tabler-armchair icon-16px text-primary me-1"></i>'
            : '<i class="icon-base ti tabler-shopping-bag icon-16px text-warning me-1"></i>';
          return icon + full['mesa'];
        },
      },
      {
        // Estatus
        targets: 4,
        render: function (data, type, full) {
          const st = statusObj[full['status']] || { label: full['status_label'], class: 'bg-label-secondary' };
          return '<span class="badge ' + st.class + '">' + st.label + '</span>';
        },
      },
      {
        // Total
        targets: 5,
        render: function (data, type, full) {
          return '<span class="fw-medium">$' + full['total'] + '</span>';
        },
      },
      {
        // Método de pago
        targets: 6,
        render: function (data, type, full) {
          if (!full['payment_method'] || full['payment_method'] === '—') {
            return '<span class="text-muted">—</span>';
          }
          return '<span class="text-capitalize">' + full['payment_method'] + '</span>';
        },
      },
      {
        // Fecha
        targets: 7,
        render: function (data, type, full) {
          return '<small class="text-muted">' + full['created_at'] + '</small>';
        },
      },
      {
        // Acciones
        targets: -1,
        title: 'Acciones',
        searchable: false,
        orderable: false,
        render: function (data, type, full) {
          const id       = full['id'];
          const canDel   = full['can_delete'];
          const nextSt   = full['next_status'];

          let deleteBtn = '';
          if (canDel) {
            deleteBtn =
              '<a href="javascript:;" data-id="' + id + '" ' +
              'class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-order" title="Eliminar">' +
              '<i class="icon-base ti tabler-trash icon-22px"></i>' +
              '</a>';
          }

          return (
            '<div class="d-flex align-items-center">' +
            '<a href="' + ordersBase + '/' + id + '/edit" ' +
            'class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Ver / Editar">' +
            '<i class="icon-base ti tabler-pencil icon-22px"></i>' +
            '</a>' +
            deleteBtn +
            '</div>'
          );
        },
      },
    ],
    select: {
      style: 'multi',
      selector: 'td:nth-child(2)',
    },
    order: [[2, 'desc']],
    layout: {
      top2Start: {
        rowClass: 'row card-header flex-column flex-md-row border-bottom mx-0 px-3',
        features: [],
      },
      top2End: {
        features: [
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-primary dropdown-toggle me-4',
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-upload icon-xs me-sm-1"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  {
                    extend: 'print',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-1"></i>Imprimir</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6, 7] },
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6, 7] },
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6, 7] },
                  },
                ],
              },
            ],
          },
        ],
      },
      topStart: {
        rowClass: 'row mx-0 px-3 my-0 justify-content-between border-bottom',
        features: [
          {
            pageLength: {
              menu: [10, 25, 50, 100],
              text: 'Show_MENU_entries',
            },
          },
        ],
      },
      topEnd: {
        search: { placeholder: 'Buscar orden...' },
      },
      bottomStart: {
        rowClass: 'row mx-3 justify-content-between',
        features: ['info'],
      },
      bottomEnd: 'paging',
    },
    language: {
      paginate: {
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
        first:    '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
        last:     '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>',
      },
    },
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({
          header: function (row) {
            return 'Orden ' + row.data()['folio'];
          },
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns
            .map(function (col) {
              return col.title !== ''
                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                  '<td>' + col.title + ':</td><td>' + col.data + '</td></tr>'
                : '';
            })
            .join('');
          if (data) {
            const div  = document.createElement('div');
            div.classList.add('table-responsive');
            const tbl  = document.createElement('table');
            const tbody = document.createElement('tbody');
            tbody.innerHTML = data;
            tbl.classList.add('table');
            tbl.appendChild(tbody);
            div.appendChild(tbl);
            return div;
          }
          return false;
        },
      },
    },
    initComplete: function () {
      const api = this.api();

      // Filtro por estatus
      const selectStatus = document.createElement('select');
      selectStatus.className = 'form-select text-capitalize';
      selectStatus.innerHTML =
        '<option value="">Todos los estados</option>' +
        '<option value="Pendiente">Pendiente</option>' +
        '<option value="En preparación">En preparación</option>' +
        '<option value="Listo">Listo</option>' +
        '<option value="Entregado">Entregado</option>' +
        '<option value="Pagado">Pagado</option>';
      const statusContainer = document.querySelector('.orders_status');
      if (statusContainer) statusContainer.appendChild(selectStatus);

      selectStatus.addEventListener('change', function () {
        const val = selectStatus.value ? '^' + selectStatus.value + '$' : '';
        api.column(4).search(val, true, false).draw();
      });

      // Filtro por tipo
      const selectType = document.createElement('select');
      selectType.className = 'form-select text-capitalize';
      selectType.innerHTML =
        '<option value="">Todos los tipos</option>' +
        '<option value="Mesa">Mesa</option>' +
        '<option value="Para llevar">Para llevar</option>';
      const typeContainer = document.querySelector('.orders_type');
      if (typeContainer) typeContainer.appendChild(selectType);

      selectType.addEventListener('change', function () {
        const val = selectType.value ? selectType.value : '';
        api.column(3).search(val, false, false).draw();
      });
    },
  });

  // Eliminar orden
  function bindDeleteEvents() {
    const tbody = dt_orders_table.querySelector('tbody');
    if (tbody) {
      tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-order');
        if (!btn) return;
        if (!confirm('¿Eliminar esta orden?')) return;

        const row = btn.closest('tr');
        fetch(ordersBase + '/' + btn.dataset.id, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
        })
          .then(function (res) {
            if (res.ok) {
              dt_orders.row(row).remove().draw();
            } else {
              return res.json().then(function (data) {
                alert(data.message || 'No se pudo eliminar la orden.');
              });
            }
          })
          .catch(function () { alert('Error de conexión.'); });
      });
    }
  }

  bindDeleteEvents();

  // Ajustar clases DataTable (patrón de tables-datatables-basic.js)
  setTimeout(function () {
    const elementsToModify = [
      { selector: '.dt-buttons .btn',         classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control',  classToRemove: 'form-control-sm', classToAdd: 'ms-4' },
      { selector: '.dt-length .form-select',   classToRemove: 'form-select-sm' },
      { selector: '.dt-layout-table',          classToRemove: 'row mt-2' },
      { selector: '.dt-layout-end',            classToAdd: 'mt-0' },
      { selector: '.dt-layout-end .dt-search', classToAdd: 'mt-0 mt-md-6 mb-6' },
      { selector: '.dt-layout-start',          classToAdd: 'mt-0' },
      { selector: '.dt-layout-end .dt-buttons',classToAdd: 'mb-0' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' },
    ];

    elementsToModify.forEach(function ({ selector, classToRemove, classToAdd }) {
      document.querySelectorAll(selector).forEach(function (el) {
        if (classToRemove) classToRemove.split(' ').forEach(function (c) { el.classList.remove(c); });
        if (classToAdd)    classToAdd.split(' ').forEach(function (c) { el.classList.add(c); });
      });
    });
  }, 100);
});

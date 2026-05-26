/**
 * Mesas - Listado
 * Clonado y adaptado de: menu-item-list.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_mesas_table = document.querySelector('.datatables-mesas');

  if (!dt_mesas_table) return;

  const csrfToken = dt_mesas_table.dataset.csrf;
  const mesasBase = dt_mesas_table.dataset.mesasBase;

  const activaObj = {
    true:  { title: 'Activa',      class: 'bg-label-success' },
    false: { title: 'Inactiva',    class: 'bg-label-secondary' },
  };

  const dt_mesas = new DataTable(dt_mesas_table, {
    ajax: {
      url: mesasBase + '-data',
      type: 'GET',
    },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'numero' },
      { data: 'capacidad' },
      { data: 'ubicacion' },
      { data: 'estado_label' },
      { data: 'activa' },
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
        targets: 2,
        responsivePriority: 1,
        render: function (data, type, full) {
          const numero = full['numero'];
          return (
            '<div class="d-flex align-items-center gap-3">' +
            '<span class="avatar-initial rounded bg-label-primary" ' +
            'style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;">' +
            numero.charAt(0).toUpperCase() +
            '</span>' +
            '<span class="fw-medium text-heading">Mesa ' + numero + '</span>' +
            '</div>'
          );
        },
      },
      {
        targets: 3,
        render: function (data, type, full) {
          return '<span class="text-heading">' + full['capacidad'] + ' pers.</span>';
        },
      },
      {
        targets: 4,
        render: function (data, type, full) {
          return '<span class="text-muted">' + full['ubicacion'] + '</span>';
        },
      },
      {
        targets: 5,
        render: function (data, type, full) {
          const estadoClass = full['estado_class'] || 'bg-label-secondary';
          return '<span class="badge ' + estadoClass + '">' + full['estado_label'] + '</span>';
        },
      },
      {
        targets: 6,
        render: function (data, type, full) {
          const key    = String(full['activa']);
          const estado = activaObj[key] || activaObj['false'];
          return '<span class="badge ' + estado.class + '">' + estado.title + '</span>';
        },
      },
      {
        targets: -1,
        title: 'Acciones',
        searchable: false,
        orderable: false,
        render: function (data, type, full) {
          const id = full['id'];
          return (
            '<div class="d-flex align-items-center">' +
            '<a href="' + mesasBase + '/' + id + '/edit" ' +
            'class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Editar">' +
            '<i class="icon-base ti tabler-edit icon-22px"></i>' +
            '</a>' +
            '<a href="javascript:;" data-id="' + id + '" ' +
            'class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-mesa" title="Eliminar">' +
            '<i class="icon-base ti tabler-trash icon-22px"></i>' +
            '</a>' +
            '</div>'
          );
        },
      },
    ],
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [
          {
            pageLength: {
              menu: [10, 25, 50, 100],
              text: '_MENU_',
            },
          },
        ],
      },
      topEnd: {
        features: [
          {
            search: {
              placeholder: 'Buscar mesa',
              text: '_INPUT_',
            },
          },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle',
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-upload icon-xs"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  {
                    extend: 'print',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-1"></i>Imprimir</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6] },
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6] },
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6] },
                  },
                  {
                    extend: 'pdf',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-description me-1"></i>PDF</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5, 6] },
                  },
                ],
              },
              {
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-plus icon-xs"></i><span class="d-none d-sm-inline-block">Nueva Mesa</span></span>',
                className: 'btn btn-primary',
                action: function () {
                  window.location.href = mesasBase + '/create';
                },
              },
            ],
          },
        ],
      },
      bottomStart: {
        rowClass: 'row mx-3 justify-content-between',
        features: ['info'],
      },
      bottomEnd: 'paging',
    },
    language: {
      sLengthMenu: '_MENU_',
      search: '',
      searchPlaceholder: 'Buscar mesa',
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
            return 'Mesa ' + row.data()['numero'];
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
            const div   = document.createElement('div');
            div.classList.add('table-responsive');
            const table = document.createElement('table');
            const tbody = document.createElement('tbody');
            tbody.innerHTML = data;
            table.classList.add('table');
            table.appendChild(tbody);
            div.appendChild(table);
            return div;
          }
          return false;
        },
      },
    },
    initComplete: function () {
      const api = this.api();

      // Filtro por estado
      const colEstado = api.column(5);
      const selectEstado = document.createElement('select');
      selectEstado.className = 'form-select text-capitalize';
      selectEstado.innerHTML =
        '<option value="">Todos los estados</option>' +
        '<option value="Disponible">Disponible</option>' +
        '<option value="Ocupada">Ocupada</option>' +
        '<option value="Reservada">Reservada</option>' +
        '<option value="En limpieza">En limpieza</option>';
      document.querySelector('.mesas_estado').appendChild(selectEstado);

      selectEstado.addEventListener('change', function () {
        const val = selectEstado.value ? '^' + selectEstado.value + '$' : '';
        colEstado.search(val, true, false).draw();
      });

      // Filtro por activa
      const selectActiva = document.createElement('select');
      selectActiva.className = 'form-select text-capitalize';
      selectActiva.innerHTML =
        '<option value="">Todas</option>' +
        '<option value="Activa">Activa</option>' +
        '<option value="Inactiva">Inactiva</option>';
      document.querySelector('.mesas_activa').appendChild(selectActiva);

      selectActiva.addEventListener('change', function () {
        const val = selectActiva.value ? '^' + selectActiva.value + '$' : '';
        api.column(6).search(val, true, false).draw();
      });
    },
  });

  // Eliminar mesa
  function bindDeleteEvents() {
    const tableBody = dt_mesas_table.querySelector('tbody');
    const modal     = document.querySelector('.dtr-bs-modal');

    function handleDelete(id, row) {
      if (!confirm('¿Eliminar esta mesa?')) return;

      fetch(mesasBase + '/' + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
        .then(function (res) {
          if (res.ok) {
            dt_mesas.row(row).remove().draw();
            if (modal) {
              const closeBtn = modal.querySelector('.btn-close');
              if (closeBtn) closeBtn.click();
            }
          } else {
            res.json().then(function (data) {
              alert(data.message || 'No se pudo eliminar la mesa.');
            });
          }
        })
        .catch(function () {
          alert('Error de conexión al intentar eliminar.');
        });
    }

    if (tableBody) {
      tableBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-mesa');
        if (btn) {
          const row = btn.closest('tr');
          handleDelete(btn.dataset.id, row);
        }
      });
    }

    if (modal) {
      modal.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-mesa');
        if (btn) {
          handleDelete(btn.dataset.id, null);
        }
      });
    }
  }

  bindDeleteEvents();

  document.addEventListener('show.bs.modal', function (e) {
    if (e.target.classList.contains('dtr-bs-modal')) bindDeleteEvents();
  });

  // Ajustar clases de los controles de DataTable
  setTimeout(function () {
    const elementsToModify = [
      { selector: '.dt-buttons .btn',        classToRemove: 'btn-secondary' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length .form-select',  classToRemove: 'form-select-sm', classToAdd: 'ms-0' },
      { selector: '.dt-length',               classToAdd: 'mb-md-6 mb-0' },
      {
        selector: '.dt-layout-end',
        classToRemove: 'justify-content-between',
        classToAdd: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap',
      },
      { selector: '.dt-buttons',      classToAdd: 'd-flex gap-4 mb-md-0 mb-4' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full',  classToRemove: 'col-md col-12', classToAdd: 'table-responsive' },
    ];

    elementsToModify.forEach(function ({ selector, classToRemove, classToAdd }) {
      document.querySelectorAll(selector).forEach(function (el) {
        if (classToRemove) {
          classToRemove.split(' ').forEach(function (c) { el.classList.remove(c); });
        }
        if (classToAdd) {
          classToAdd.split(' ').forEach(function (c) { el.classList.add(c); });
        }
      });
    });
  }, 100);
});

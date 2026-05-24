/**
 * Menú - Listado de Platillos
 * Clonado y adaptado de: app-user-list.js
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dt_menu_table = document.querySelector('.datatables-menu-items');

  if (!dt_menu_table) return;

  const csrfToken  = dt_menu_table.dataset.csrf;
  const menuBase   = dt_menu_table.dataset.menuBase;

  const disponibleObj = {
    true:  { title: 'Disponible',    class: 'bg-label-success' },
    false: { title: 'No disponible', class: 'bg-label-secondary' },
  };

  const dt_menu = new DataTable(dt_menu_table, {
    ajax: {
      url: menuBase + '-data',
      type: 'GET',
    },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, searchable: false },
      { data: 'nombre' },
      { data: 'categoria' },
      { data: 'precio' },
      { data: 'disponible' },
      { data: 'id', orderable: false, searchable: false },
    ],
    columnDefs: [
      {
        // Control responsive
        className: 'control',
        searchable: false,
        orderable: false,
        responsivePriority: 2,
        targets: 0,
        render: function () {
          return '';
        },
      },
      {
        // Imagen + Nombre
        targets: 2,
        responsivePriority: 1,
        render: function (data, type, full) {
          const nombre = full['nombre'];
          const imagen = full['imagen'];
          let imgHtml;

          if (imagen) {
            imgHtml =
              '<img src="' + imagen + '" alt="' + nombre + '" ' +
              'class="rounded" style="width:40px;height:40px;object-fit:cover;">';
          } else {
            imgHtml =
              '<span class="avatar-initial rounded bg-label-primary" ' +
              'style="width:40px;height:40px;display:inline-flex;align-items:center;justify-content:center;">' +
              nombre.charAt(0).toUpperCase() +
              '</span>';
          }

          return (
            '<div class="d-flex align-items-center gap-3">' +
            imgHtml +
            '<span class="fw-medium text-heading">' + nombre + '</span>' +
            '</div>'
          );
        },
      },
      {
        // Categoría
        targets: 3,
        render: function (data, type, full) {
          return '<span class="text-heading">' + full['categoria'] + '</span>';
        },
      },
      {
        // Precio
        targets: 4,
        render: function (data, type, full) {
          return '<span class="fw-medium">$' + full['precio'] + '</span>';
        },
      },
      {
        // Disponible
        targets: 5,
        render: function (data, type, full) {
          const key    = String(full['disponible']);
          const estado = disponibleObj[key] || disponibleObj['false'];
          return (
            '<span class="badge ' + estado.class + '" text-capitalized>' +
            estado.title +
            '</span>'
          );
        },
      },
      {
        // Acciones
        targets: -1,
        title: 'Acciones',
        searchable: false,
        orderable: false,
        render: function (data, type, full) {
          const id = full['id'];
          return (
            '<div class="d-flex align-items-center">' +
            '<a href="' + menuBase + '/' + id + '/edit" ' +
            'class="btn btn-text-secondary rounded-pill waves-effect btn-icon" title="Editar">' +
            '<i class="icon-base ti tabler-edit icon-22px"></i>' +
            '</a>' +
            '<a href="javascript:;" data-id="' + id + '" ' +
            'class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-menu-item" title="Eliminar">' +
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
              placeholder: 'Buscar platillo',
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
                    exportOptions: { columns: [2, 3, 4, 5] },
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-1"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5] },
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5] },
                  },
                  {
                    extend: 'pdf',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-description me-1"></i>PDF</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4, 5] },
                  },
                ],
              },
              {
                text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ti tabler-plus icon-xs"></i><span class="d-none d-sm-inline-block">Nuevo Platillo</span></span>',
                className: 'btn btn-primary',
                action: function () {
                  window.location.href = menuBase + '/create';
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
      searchPlaceholder: 'Buscar platillo',
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
            return 'Detalle: ' + row.data()['nombre'];
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
            const div = document.createElement('div');
            div.classList.add('table-responsive');
            const table  = document.createElement('table');
            const tbody  = document.createElement('tbody');
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

      // Filtro por categoría
      const colCategoria = api.column(3);
      const selectCategoria = document.createElement('select');
      selectCategoria.className = 'form-select text-capitalize';
      selectCategoria.innerHTML = '<option value="">Todas las categorías</option>';
      document.querySelector('.menu_categoria').appendChild(selectCategoria);

      selectCategoria.addEventListener('change', function () {
        const val = selectCategoria.value ? '^' + selectCategoria.value + '$' : '';
        colCategoria.search(val, true, false).draw();
      });

      const cats = Array.from(new Set(colCategoria.data().toArray())).sort();
      cats.forEach(function (c) {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        selectCategoria.appendChild(opt);
      });

      // Filtro por disponibilidad
      const selectDisponible = document.createElement('select');
      selectDisponible.className = 'form-select text-capitalize';
      selectDisponible.innerHTML =
        '<option value="">Todos</option>' +
        '<option value="Disponible">Disponible</option>' +
        '<option value="No disponible">No disponible</option>';
      document.querySelector('.menu_disponible').appendChild(selectDisponible);

      selectDisponible.addEventListener('change', function () {
        const val = selectDisponible.value ? '^' + selectDisponible.value + '$' : '';
        api.column(5).search(val, true, false).draw();
      });
    },
  });

  // Eliminar platillo
  function bindDeleteEvents() {
    const tableBody = dt_menu_table.querySelector('tbody');
    const modal     = document.querySelector('.dtr-bs-modal');

    function handleDelete(id, row) {
      if (!confirm('¿Eliminar este platillo?')) return;

      fetch(menuBase + '/' + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
        .then(function (res) {
          if (res.ok) {
            dt_menu.row(row).remove().draw();
            if (modal) {
              const closeBtn = modal.querySelector('.btn-close');
              if (closeBtn) closeBtn.click();
            }
          } else {
            alert('No se pudo eliminar el platillo.');
          }
        })
        .catch(function () {
          alert('Error de conexión al intentar eliminar.');
        });
    }

    if (tableBody) {
      tableBody.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-menu-item');
        if (btn) {
          const row = btn.closest('tr');
          handleDelete(btn.dataset.id, row);
        }
      });
    }

    if (modal) {
      modal.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-menu-item');
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

  // Ajustar clases de los controles de DataTable (patrón de app-user-list.js)
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

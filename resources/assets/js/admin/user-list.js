'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const table = document.querySelector('.datatables-sgr-users');
  if (!table) return;

  const dt = new DataTable(table, {
    ajax: {
      url: baseUrl + 'users-data',
      dataSrc: 'data'
    },
    columns: [
      { data: null, orderable: false, searchable: false },
      { data: 'name' },
      { data: 'email' },
      { data: 'role' },
      { data: null, orderable: false, searchable: false }
    ],
    columnDefs: [
      {
        targets: 0,
        className: 'control',
        render: () => ''
      },
      {
        targets: 1,
        render: (data, type, full) =>
          `<span class="fw-medium">${full.name}</span>`
      },
      {
        targets: -1,
        render: (data, type, full) => `
          <div class="d-flex align-items-center gap-1">
            <a href="${full.edit_url}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
              <i class="icon-base ti tabler-edit icon-20px"></i>
            </a>
            <button class="btn btn-sm btn-icon btn-text-danger rounded-pill delete-user"
              data-url="${full.delete_url}" data-name="${full.name}">
              <i class="icon-base ti tabler-trash icon-20px"></i>
            </button>
          </div>`
      }
    ],
    order: [[1, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
      },
      topEnd: {
        features: [{ search: { placeholder: 'Buscar usuario', text: '_INPUT_' } }]
      },
      bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
      bottomEnd: 'paging'
    },
    language: {
      sLengthMenu: '_MENU_',
      search: '',
      searchPlaceholder: 'Buscar usuario',
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      }
    },
    responsive: { details: { type: 'column', target: 0 } },
    initComplete: function () {
      setTimeout(() => {
        document.querySelectorAll('.dt-buttons .btn').forEach(el => el.classList.remove('btn-secondary'));
        document.querySelectorAll('.dt-search .form-control').forEach(el => el.classList.remove('form-control-sm'));
        document.querySelectorAll('.dt-length .form-select').forEach(el => { el.classList.remove('form-select-sm'); el.classList.add('ms-0'); });
        document.querySelectorAll('.dt-layout-full').forEach(el => { el.classList.remove('col-md', 'col-12'); el.classList.add('table-responsive'); });
      }, 100);
    }
  });

  document.querySelector('.datatables-sgr-users').addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-user');
    if (!btn) return;
    const name = btn.dataset.name;
    const url  = btn.dataset.url;
    if (!confirm(`¿Eliminar al usuario "${name}"?`)) return;

    fetch(url, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
      if (data.message) dt.ajax.reload();
    });
  });
});

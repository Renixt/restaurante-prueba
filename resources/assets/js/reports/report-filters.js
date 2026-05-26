'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const tables = document.querySelectorAll('.report-table');
  tables.forEach(table => {
    if (table) {
      new DataTable(table, {
        order: [],
        layout: {
          topStart: {
            rowClass: 'row m-3 my-0 justify-content-between',
            features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
          },
          topEnd: {
            features: [{ search: { placeholder: 'Buscar...', text: '_INPUT_' } }]
          },
          bottomStart: { rowClass: 'row mx-3 justify-content-between', features: ['info'] },
          bottomEnd: 'paging'
        },
        language: {
          sLengthMenu: '_MENU_',
          search: '',
          searchPlaceholder: 'Buscar...',
          paginate: {
            next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
            previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
          }
        },
        initComplete: function () {
          setTimeout(() => {
            document.querySelectorAll('.dt-search .form-control').forEach(el => el.classList.remove('form-control-sm'));
            document.querySelectorAll('.dt-length .form-select').forEach(el => { el.classList.remove('form-select-sm'); el.classList.add('ms-0'); });
            document.querySelectorAll('.dt-layout-full').forEach(el => { el.classList.remove('col-md', 'col-12'); el.classList.add('table-responsive'); });
          }, 100);
        }
      });
    }
  });
});

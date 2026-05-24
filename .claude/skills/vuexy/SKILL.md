## SKILL: Vuexy

### REGLAS GENERALES
- No inventes HTML, CSS ni JS.
- Usa exclusivamente las vistas, layouts, parciales y scripts existentes en el proyecto.
- Cuando se necesite un módulo nuevo, clona vistas existentes de Vuexy.
- Cuando una vista use JS, clona también el archivo JS correspondiente y adáptalo.
- Mantén la estructura de carpetas exacta del proyecto.
- Todo módulo debe generarse como un CRUD Laravel completo.
- Las vistas deben extender los layouts existentes.
- Los scripts deben cargarse usando las secciones existentes.
- No modifiques archivos fuera del módulo solicitado.

---

### VISTAS DISPONIBLES (resources/views/content/)
**Tablas base:**
- tables-datatables-basic.blade.php
- app-user-list.blade.php
- app-access-roles.blade.php

**Formularios base:**
- app-user-view-account.blade.php
- app-ecommerce-product-add.blade.php
- forms-basic-inputs.blade.php
- form-layouts-vertical.blade.php

**Vistas detalle:**
- app-user-view-account.blade.php
- app-ecommerce-customer-details-overview.blade.php

**Modales:**
- resources/views/_partials/_modals/*.blade.php

**Layouts:**
- layoutMaster.blade.php
- contentNavbarLayout.blade.php
- horizontalLayout.blade.php
- blankLayout.blade.php

---

### JS DISPONIBLE (resources/assets/js/)
**Datatables:**
- tables-datatables-basic.js
- tables-datatables-advanced.js
- tables-datatables-extensions.js
- app-user-list.js
- app-access-roles.js

**Formularios:**
- forms-basic-inputs.js
- forms-selects.js
- forms-file-upload.js
- forms-pickers.js
- forms-validation.js

**Modales:**
- modal-add-role.js
- modal-edit-user.js

**Detalles:**
- app-user-view.js
- app-user-view-account.js
- app-user-view-billing.js

---

### REGLAS PARA CLONAR VISTAS
Cuando generes un módulo:
1. Indica qué archivo original clonaste.
2. Indica qué JS clonaste.
3. Adapta rutas, IDs, columnas y variables.
4. Mantén toda la funcionalidad original.
5. No elimines scripts ni estilos de Vuexy.
6. No cambies la estructura del layout.

---

### REGLAS PARA GENERAR UN MÓDULO (CRUD COMPLETO)
Cada módulo debe incluir:
1. Migration  
2. Model  
3. Controller (index, create, store, edit, update, destroy)  
4. Form Request  
5. Policy  
6. Seeder (si aplica)  
7. Rutas web.php  
8. Vistas clonadas  
9. JS clonado y adaptado  

---

### REGLAS ESPECÍFICAS PARA CLAUDE CODE
- Mantén las respuestas cortas, directas y estructuradas.
- Respeta la estructura real del proyecto.
- No inventes rutas, layouts ni componentes.
- Usa siempre vistas y JS existentes como base.
- Cuando el usuario pida un módulo, genera solo ese módulo.
- No mezcles módulos.
- No generes front nuevo, solo clones adaptados.

---

### COMANDO DE INVOCACIÓN
Cuando el usuario diga:

> “Usa la skill Vuexy y crea el módulo X”

Debes:
1. Crear el CRUD completo.  
2. Clonar vistas y JS correspondientes.  
3. Adaptar rutas, variables y columnas.  
4. Mantener la estructura Vuexy.  
5. Entregar todo organizado por archivos.  
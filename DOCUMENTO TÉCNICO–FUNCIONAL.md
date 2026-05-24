# 📘 DOCUMENTO TÉCNICO–FUNCIONAL  
# Sistema de Gestión para Restaurante (SGR)  
**Versión 1.0 — Documento oficial para el equipo de desarrollo**

---

# 1. Objetivo del Proyecto
Desarrollar un sistema web modular para gestionar las operaciones principales de un restaurante:

- Menú  
- Órdenes  
- Mesas  
- Inventario  
- Proveedores  
- Usuarios, roles y permisos  
- Reportes  

El sistema debe ser escalable, seguro, documentado y desarrollado con buenas prácticas.

---

# 2. Alcance

## 2.1 Funcionalidades incluidas
- Gestión completa del menú  
- Control de órdenes y mesas  
- Control de inventario  
- Gestión de proveedores  
- Roles y permisos  
- Reportes operativos  
- Seguridad y autenticación  

## 2.2 Funcionalidades excluidas
- Facturación electrónica  
- Integración con terminales bancarias  
- Contabilidad avanzada  
- Nómina  
- Inteligencia artificial  

---

# 3. Requerimientos Funcionales por Módulo

---

# 4. Módulo de Menú

## 4.1 Funcionalidades
- CRUD de platillos  
- Asignación de categorías (entrada, plato fuerte, postre, bebida)  
- Subida de imágenes  
- Activar/desactivar disponibilidad  

## 4.2 Reglas
- Todo platillo debe tener: nombre, precio, categoría  
- El precio debe ser mayor a 0  
- No se puede eliminar un platillo asociado a órdenes activas  
- Imágenes: JPG/PNG, máximo 2MB  

## 4.3 Validaciones
- Nombre único por categoría  
- Descripción mínima de 10 caracteres  
- Precio numérico con máximo 2 decimales  

## 4.4 Criterios de aceptación
- El menú debe cargar en < 1 segundo  
- Los cambios deben reflejarse en tiempo real  

---

# 5. Módulo de Órdenes

## 5.1 Funcionalidades
- Crear órdenes por mesa o para llevar  
- Agregar/eliminar platillos  
- Actualizar estatus: pendiente, en preparación, listo, entregado, pagado  
- Registrar método de pago  
- Dividir cuenta (opcional)  

## 5.2 Reglas
- Una orden no puede cerrarse sin platillos  
- Una orden pagada no puede eliminarse  
- El estatus solo puede avanzar  
- El total debe recalcularse automáticamente  

## 5.3 Validaciones
- La mesa debe existir y estar disponible  
- No se pueden agregar platillos agotados  
- Métodos de pago: efectivo, tarjeta, transferencia  

## 5.4 Criterios de aceptación
- Crear una orden debe requerir máximo 5 clics  
- El cocinero debe ver automáticamente las órdenes nuevas  

---

# 6. Módulo de Mesas

## 6.1 Funcionalidades
- Registrar mesas  
- Cambiar estatus: disponible, ocupada, reservada  
- Asociar órdenes a mesas  

## 6.2 Reglas
- Una mesa ocupada no puede recibir otra orden  
- No se puede eliminar una mesa con órdenes activas  

---

# 7. Módulo de Inventario

## 7.1 Funcionalidades
- Registrar insumos  
- Control de existencias  
- Alertas por bajo inventario  
- Descuentos automáticos al preparar platillos  

## 7.2 Reglas
- Cada platillo debe tener receta (insumos + cantidades)  
- No se puede preparar un platillo sin inventario suficiente  
- Alertas cuando stock < mínimo definido  

## 7.3 Validaciones
- Cantidades numéricas  
- Unidad de medida obligatoria  

## 7.4 Criterios de aceptación
- Las alertas deben mostrarse en el dashboard del administrador  

---

# 8. Módulo de Proveedores

## 8.1 Funcionalidades
- Registrar proveedores  
- Crear pedidos de insumos  
- Registrar entregas  
- Control de estatus del pedido  

## 8.2 Reglas
- Un pedido debe tener al menos un insumo  
- No se puede cerrar un pedido sin registrar entrega  
- RFC obligatorio y válido  

## 8.3 Validaciones
- Teléfono numérico  
- Email válido  
- RFC con formato correcto  

---

# 9. Módulo de Usuarios, Roles y Permisos

## 9.1 Roles sugeridos
- Administrador  
- Gerente  
- Mesero  
- Cocinero  
- Almacén  

## 9.2 Permisos por rol
- **Administrador:** CRUD completo  
- **Gerente:** reportes, inventario, proveedores  
- **Mesero:** órdenes  
- **Cocinero:** ver órdenes en preparación  
- **Almacén:** inventario  

## 9.3 Reglas
- Un usuario debe tener un rol asignado  
- No se puede eliminar un usuario con actividad registrada  

## 9.4 Validaciones
- Email único  
- Contraseña mínima 8 caracteres  

---

# 10. Módulo de Reportes

## 10.1 Reportes requeridos
- Ventas por día  
- Platillos más vendidos  
- Inventario bajo  
- Proveedores con retrasos  

## 10.2 Reglas
- Exportación a PDF o Excel  
- Filtros por fecha  

---

# 11. Requerimientos Técnicos

## 11.1 Backend
- Laravel 10+  
- PHP 8.2+  
- MySQL 8+  

## 11.2 Frontend
- Vue 3 o Blade + Vuexy  
- Tailwind o Bootstrap (opcional)  

## 11.3 Infraestructura
- Docker (opcional)  
- Cloud Code para gestión del proyecto  

## 11.4 Seguridad
- Autenticación con tokens o sesiones  
- Hash de contraseñas  
- Validación de inputs en backend  

---

# 12. Pruebas

## 12.1 Tipos de pruebas
- Unitarias  
- Integración  
- End-to-end  
- Pruebas de carga (opcional)  

## 12.2 Criterios mínimos
- 60% de cobertura en backend  
- Pruebas obligatorias en órdenes y menú  

---

# 13. Entregables del Equipo
- Documentación del proyecto en Cloud Code  
- Diagramas UML  
- Código funcional  
- Pruebas  
- Manual de usuario  
- Manual técnico  
- Presentación final  
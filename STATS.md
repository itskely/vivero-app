## 🔷 1. RESUMEN GENERAL (cards arriba)

Esto no es gráfica, pero es obligatorio:

Stock total actual
Cantidad de lotes activos
Entradas del mes
Salidas del mes

👉 Esto se saca directo de:

inventario
movimientos_inventario

## 🔷 2. STOCK POR ESPECIE (📊 gráfico principal)

Ya tienes esto casi hecho con tu vista:

```sql
SELECT
    nombre_comun,
    unidad_medida,
    stock_total
FROM vista_stock_total_especie;
```

### 📊 Tipo:

Bar chart o stacked bar
Agrupado por unidad_medida

👉 Esto responde:

¿Qué tengo y en qué volumen?

## 🔷 3. MOVIMIENTOS EN EL TIEMPO (📈 tendencia)

Esta es la que tú mencionaste (y sí, es clave).

```sql
SELECT
    DATE_FORMAT(fecha, '%Y-%m') AS periodo,
    tipo_movimiento,
    SUM(cantidad) AS total
FROM movimientos_inventario
WHERE estado = 'activo'
GROUP BY periodo, tipo_movimiento
ORDER BY periodo;
```

### 📊 Tipo:

Line chart
Series:
entrada
salida

👉 Esto responde:

¿El sistema está creciendo o decreciendo?

## 🔷 4. MOVIMIENTOS POR ETAPA (🔥 muy importante)

```sql
SELECT
    e.nombre AS etapa,
    m.tipo_movimiento,
    SUM(m.cantidad) AS total
FROM movimientos_inventario m
JOIN etapas e ON m.etapa_id = e.id
WHERE m.estado = 'activo'
GROUP BY e.nombre, m.tipo_movimiento;
```

### 📊 Tipo:

Bar chart horizontal
O stacked

👉 Esto responde:

¿Dónde se está moviendo más inventario?

## 🔷 5. EFICIENCIA / MERMA (💡 la joya del sistema)

Esto es lo que te diferencia de un CRUD común.

```sql
SELECT
    DATE_FORMAT(fecha_cambio, '%Y-%m') AS periodo,
    SUM(cantidad_procesada) AS procesado,
    SUM(cantidad_resultante) AS resultante,
    (SUM(cantidad_procesada) - SUM(cantidad_resultante)) AS merma
FROM historial_etapas
GROUP BY periodo
ORDER BY periodo;
```

### 📊 Tipo:

Bar + line
procesado
resultante
merma

👉 Esto responde:

¿Cuánto estoy perdiendo en los procesos?

## 🔷 6. ORIGEN DE LOS LOTES (📦 trazabilidad)

```sql
SELECT
    o.nombre_origen,
    COUNT(l.id) AS cantidad_lotes
FROM lotes l
JOIN origen o ON l.origen_id = o.id
GROUP BY o.nombre_origen;
```

### 📊 Tipo:

Pie chart

👉 Esto responde:

¿De dónde viene lo que manejo?

## 🔷 7. INVENTARIO POR UBICACIÓN (📍 operativo)

```sql
SELECT
    u.nombre,
    SUM(i.cantidad_actual) AS total
FROM inventario i
JOIN ubicaciones u ON i.ubicacion_id = u.id
GROUP BY u.nombre;
```

### 📊 Tipo:

Bar chart

👉 Esto responde:

¿Dónde está físicamente el inventario?

## 🔥 BONUS (si quieres algo pro)

### 🔸 TOP plantas más trabajadas

```sql
SELECT
    p.nombre_comun,
    SUM(m.cantidad) AS total_movimientos
FROM movimientos_inventario m
JOIN lotes l ON m.lote_id = l.id
JOIN plantas p ON l.planta_id = p.id
GROUP BY p.nombre_comun
ORDER BY total_movimientos DESC
LIMIT 5;
```

### 🧠 FILTROS (clave para ApexCharts)

Diseña todo para soportar:

rango de fechas
etapa_id
planta_id
ubicacion_id

👉 Solo agregas:

WHERE fecha BETWEEN ? AND ?

### 🧩 ESTRUCTURA JSON IDEAL

Devuelve así (esto te ahorra dolores en frontend):

```json
{
    "labels": ["2026-01", "2026-02"],
    "series": [
        {
            "name": "entrada",
            "data": [100, 200]
        },
        {
            "name": "salida",
            "data": [80, 150]
        }
    ]
}
```

### 🧠 CONCLUSIÓN (directa)

Si solo haces gráficas de movimientos, te quedas corto.

El valor real de tu sistema está en:

stock (qué hay)
flujo (qué pasa)
eficiencia (qué se pierde)

👉 Si incluyes esas 3 capas, tu dashboard deja de ser decorativo y se vuelve herramienta de decisión.

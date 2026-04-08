# Estructura de necesidad de base de datos para vivero

Aquí se cubren diferentes necesidades que se tienen en un vivero, como el control de inventario, el seguimiento de lotes, el registro de transacciones, etc. Cada movimiento rastrea el usuario que lo realizó, la fecha y hora, y cualquier observación relevante.

Se maneja `DECIMAL` para las cantidades, permitiendo tanto pesajes como conteos de unidades.

```sql
-- 1. Catálogo de plantas
CREATE TABLE plantas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_comun VARCHAR(100) NOT NULL,
    nombre_cientifico VARCHAR(100),
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Etapas del Ciclo de Vida (Semilla, Germinación, Rustificación, etc.)
CREATE TABLE etapas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;

-- 3. Ubicaciones Físicas (Cama 1, Invernadero A, Estante B)
CREATE TABLE ubicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;

-- 4. Lotes: La entidad central
-- Agrupa un conjunto de plantas/semillas que nacen o entran juntas.
CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planta_id INT NOT NULL,
    codigo_lote VARCHAR(20) UNIQUE NOT NULL, -- Ej: AGU-2024-001
    fecha_creacion DATE NOT NULL,
    unidad_medida ENUM('unidades', 'gramos', 'kilogramos') NOT NULL,
    usuario_id INT NOT NULL, -- Quién registró el lote originalmente
    observaciones TEXT,
    FOREIGN KEY (planta_id) REFERENCES plantas(id),
    INDEX (codigo_lote)
) ENGINE=InnoDB;

-- 5. Inventario Actual (Estado de hoy)
-- Indica cuánta cantidad de qué lote hay en qué etapa y lugar.
CREATE TABLE inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lote_id INT NOT NULL,
    etapa_id INT NOT NULL,
    ubicacion_id INT NOT NULL,
    cantidad_actual DECIMAL(10, 2) NOT NULL DEFAULT 0, -- Soporta 0.500 kg o 100 unidades
    ultima_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lote_id) REFERENCES lotes(id),
    FOREIGN KEY (etapa_id) REFERENCES etapas(id),
    FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones(id),
    UNIQUE KEY unique_lote_etapa_ubi (lote_id, etapa_id, ubicacion_id)
) ENGINE=InnoDB;

-- 6. Transacciones de Inventario (Entradas y Salidas)
-- Trazabilidad absoluta de movimientos externos o ajustes.
CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lote_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'traslado') NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    motivo VARCHAR(255), -- Ej: "Venta", "Plaga/Muerte", "Donación"
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lote_id) REFERENCES lotes(id),
    INDEX (fecha),
    INDEX (tipo_movimiento)
) ENGINE=InnoDB;

-- 7. Historial de Etapas (Seguimiento del proceso biológico)
-- Registra cuando un lote pasa de una etapa a otra (ej: de Semilla a Germinación).
CREATE TABLE historial_etapas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lote_id INT NOT NULL,
    etapa_anterior_id INT,
    etapa_nueva_id INT NOT NULL,
    cantidad_procesada DECIMAL(10, 2) NOT NULL,
    cantidad_resultante DECIMAL(10, 2) NOT NULL, -- Para medir mermas
    usuario_id INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    FOREIGN KEY (lote_id) REFERENCES lotes(id),
    FOREIGN KEY (etapa_anterior_id) REFERENCES etapas(id),
    FOREIGN KEY (etapa_nueva_id) REFERENCES etapas(id)
) ENGINE=InnoDB;
```

# Procedimientos Almacenados y Triggers

En esta sección se describirán los diferentes procedimientos almacenados y triggers que se utilizan en el sistema. Estos serán de grán utilidad para realizar movimientos complejos como la del inventario sín hacer lógica de más directamente en código.

## 1. Procedimientos Almacenados (Lógica de Negocio)

Los procedimientos encapsulan operaciones complejas que afectan a varias tablas, asegurando que si algo falla, no se queden datos a medias.

### A. Registro Inicial de Lote

Este procedimiento crea el lote, le asigna su ubicación inicial y registra el movimiento de entrada en un solo paso.

```sql
DELIMITER //

CREATE PROCEDURE sp_registrar_nuevo_lote(
    IN p_planta_id INT,
    IN p_codigo_lote VARCHAR(20),
    IN p_unidad_medida VARCHAR(20),
    IN p_cantidad_inicial DECIMAL(10,2),
    IN p_etapa_id INT,
    IN p_ubicacion_id INT,
    IN p_usuario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- 1. Crear el Lote
    INSERT INTO lotes (planta_id, codigo_lote, fecha_creacion, unidad_medida, usuario_id, observaciones)
    VALUES (p_planta_id, p_codigo_lote, CURDATE(), p_unidad_medida, p_usuario_id, p_observaciones);

    SET @last_lote_id = LAST_INSERT_ID();

    -- 2. Insertar en Inventario (El trigger de movimientos se encargará del stock)
    INSERT INTO inventario (lote_id, etapa_id, ubicacion_id, cantidad_actual)
    VALUES (@last_lote_id, p_etapa_id, p_ubicacion_id, 0);

    -- 3. Registrar el Movimiento de Entrada
    INSERT INTO movimientos_inventario (lote_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (@last_lote_id, p_usuario_id, 'entrada', p_cantidad_inicial, 'Registro inicial de lote');

    COMMIT;
END //

DELIMITER ;
```

### B. Cambio de Etapa y Control de Mermas

Vital para cuando las semillas germinan. Aquí es donde registras cuántas entraron al proceso y cuántas sobrevivieron (salida de la etapa vieja, entrada a la nueva).

```sql
DELIMITER //

CREATE PROCEDURE sp_cambiar_etapa_lote(
    IN p_lote_id INT,
    IN p_etapa_anterior_id INT,
    IN p_etapa_nueva_id INT,
    IN p_ubicacion_id INT,
    IN p_cantidad_procesada DECIMAL(10,2),
    IN p_cantidad_resultante DECIMAL(10,2),
    IN p_usuario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- 1. Registrar el historial del cambio
    INSERT INTO historial_etapas (lote_id, etapa_anterior_id, etapa_nueva_id, cantidad_procesada, cantidad_resultante, usuario_id, observaciones)
    VALUES (p_lote_id, p_etapa_anterior_id, p_etapa_nueva_id, p_cantidad_procesada, p_cantidad_resultante, p_usuario_id, p_observaciones);

    -- 2. Restar del inventario en la etapa anterior (vía tabla movimientos)
    INSERT INTO movimientos_inventario (lote_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_usuario_id, 'salida', p_cantidad_procesada, 'Cambio de etapa (Salida de proceso)');

    -- 3. Asegurar que exista el registro en la nueva etapa en inventario
    INSERT INTO inventario (lote_id, etapa_id, ubicacion_id, cantidad_actual)
    VALUES (p_lote_id, p_etapa_nueva_id, p_ubicacion_id, 0)
    ON DUPLICATE KEY UPDATE ubicacion_id = p_ubicacion_id;

    -- 4. Sumar la cantidad resultante a la nueva etapa
    INSERT INTO movimientos_inventario (lote_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_usuario_id, 'entrada', p_cantidad_resultante, 'Cambio de etapa (Entrada a proceso)');

    COMMIT;
END //

DELIMITER ;
```

## 2. Triggers (Automatización de Stock)

El objetivo de estos triggers es que nunca tengas que hacer un UPDATE manual a la tabla inventario. El stock se debe calcular solo según lo que pase en movimientos_inventario.

### A. Actualización Automática de Stock Actual

Este trigger detecta si es entrada (suma) o salida/ajuste (resta) y afecta la tabla inventario en tiempo real.

```sql
DELIMITER //

CREATE TRIGGER tr_actualizar_stock_after_movimiento
AFTER INSERT ON movimientos_inventario
FOR EACH ROW
BEGIN
    -- Determinar si sumamos o restamos
    DECLARE v_cantidad_final DECIMAL(10,2);

    IF NEW.tipo_movimiento = 'entrada' THEN
        SET v_cantidad_final = NEW.cantidad;
    ELSE
        SET v_cantidad_final = NEW.cantidad * -1;
    END IF;

    -- Actualizar la tabla de inventario.
    -- Se asume que el lote ya tiene una fila en la etapa correspondiente.
    UPDATE inventario
    SET cantidad_actual = cantidad_actual + v_cantidad_final
    WHERE lote_id = NEW.lote_id;
    -- Nota: Puedes refinar esto para que filtre por la etapa actual del lote.
END //

DELIMITER ;
```

## 3. Casos de Uso y Explicación

| Acción                               | Herramienta                          | Resultado                                                                                                                                                  |
| ------------------------------------ | ------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Llega un bulto de semillas**       | `sp_registrar_nuevo_lote`            | Se crea el lote "SEM-001", se registra que Brad lo creó, y el inventario sube a 500g automáticamente.                                                      |
| **Las semillas pasan a germinación** | `sp_cambiar_etapa_lote`              | Se registra que usaste 500g de semillas pero solo obtuviste 450 brotes. El sistema resta los 500g del estado "Semilla" y suma 450 al estado "Germinación". |
| **Se muere una planta por plaga**    | `INSERT` en `movimientos_inventario` | Solo insertas el movimiento de tipo `ajuste` con cantidad `1` y motivo "Plaga". El trigger resta automáticamente esa unidad del inventario real.           |

### Consideración Técnica de Escala

He utilizado **Transacciones** (`START TRANSACTION`) en los procedimientos. Esto es vital en un vivero: si el sistema logra crear el lote pero falla al registrar el movimiento inicial, la transacción hace `ROLLBACK` y no te deja datos "huérfanos" o basura en la base de datos.

# Alteraciones por incongruencia

## 1. Ajuste en la tabla de Movimientos

La tabla de movimientos debe saber de qué etapa/ubicación sale el dinero (o las plantas) y a cuál entra.

```sql
-- Agregamos las columnas de contexto a movimientos para que el trigger sepa a qué fila de inventario afectar
ALTER TABLE movimientos_inventario
ADD COLUMN etapa_id INT NOT NULL AFTER lote_id,
ADD COLUMN ubicacion_id INT NOT NULL AFTER etapa_id;

-- Agregamos las llaves foráneas para mantener integridad
ALTER TABLE movimientos_inventario
ADD FOREIGN KEY (etapa_id) REFERENCES etapas(id),
ADD FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones(id);
```

## 2. El Trigger Corregido (Surgical Strike)

Ahora el trigger solo afectará la combinación exacta de Lote + Etapa + Ubicación.

```sql
DROP TRIGGER IF EXISTS tr_actualizar_stock_after_movimiento;

DELIMITER //

CREATE TRIGGER tr_actualizar_stock_after_movimiento
AFTER INSERT ON movimientos_inventario
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_final DECIMAL(10,2);

    -- Definir si sumamos o restamos
    IF NEW.tipo_movimiento IN ('entrada', 'traslado_entrada') THEN
        SET v_cantidad_final = NEW.cantidad;
    ELSE
        SET v_cantidad_final = NEW.cantidad * -1;
    END IF;

    -- PASO CRITICO: Intentar insertar la fila si no existe, o actualizar si existe
    INSERT INTO inventario (lote_id, etapa_id, ubicacion_id, cantidad_actual)
    VALUES (NEW.lote_id, NEW.etapa_id, NEW.ubicacion_id, v_cantidad_final)
    ON DUPLICATE KEY UPDATE
        cantidad_actual = cantidad_actual + v_cantidad_final;
END //

DELIMITER ;
```

## 3. Actualización de los Procedimientos

Ahora los procedimientos deben pasarle a la tabla de movimientos el "dónde" y "en qué estado".

### Procedimiento de Registro Inicial (Corregido)

```sql
DROP PROCEDURE IF EXISTS sp_registrar_nuevo_lote;

DELIMITER //

CREATE PROCEDURE sp_registrar_nuevo_lote(
    IN p_planta_id INT,
    IN p_codigo_lote VARCHAR(20),
    IN p_unidad_medida VARCHAR(20),
    IN p_cantidad_inicial DECIMAL(10,2),
    IN p_etapa_id INT,
    IN p_ubicacion_id INT,
    IN p_usuario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    START TRANSACTION;

    INSERT INTO lotes (planta_id, codigo_lote, fecha_creacion, unidad_medida, usuario_id, observaciones)
    VALUES (p_planta_id, p_codigo_lote, CURDATE(), p_unidad_medida, p_usuario_id, p_observaciones);

    SET @last_lote_id = LAST_INSERT_ID();

    -- El trigger ahora se encarga de todo al insertar aquí
    INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (@last_lote_id, p_etapa_id, p_ubicacion_id, p_usuario_id, 'entrada', p_cantidad_inicial, 'Registro inicial');

    COMMIT;
END //
DELIMITER ;
```

### Procedimiento de Cambio de Etapa (Corregido para mermas y parciales)

Este permite que si tienes 500, puedas pasar solo 20 a otra etapa.

```sql
DROP PROCEDURE IF EXISTS sp_cambiar_etapa_lote;

DELIMITER //

CREATE PROCEDURE sp_cambiar_etapa_lote(
    IN p_lote_id INT,
    IN p_etapa_origen_id INT,
    IN p_ubi_origen_id INT,
    IN p_etapa_destino_id INT,
    IN p_ubi_destino_id INT,
    IN p_cantidad_a_mover DECIMAL(10,2),
    IN p_cantidad_que_sobrevive DECIMAL(10,2),
    IN p_usuario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- 1. Salida del origen
    INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_etapa_origen_id, p_ubi_origen_id, p_usuario_id, 'salida', p_cantidad_a_mover, p_observaciones);

    -- 2. Entrada al destino (solo lo que sobrevivió)
    INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_etapa_destino_id, p_ubi_destino_id, p_usuario_id, 'entrada', p_cantidad_que_sobrevive, p_observaciones);

    -- 3. Historial con el texto de lo que pasó
    INSERT INTO historial_etapas (lote_id, etapa_anterior_id, etapa_nueva_id, cantidad_procesada, cantidad_resultante, usuario_id, observaciones)
    VALUES (p_lote_id, p_etapa_origen_id, p_etapa_destino_id, p_cantidad_a_mover, p_cantidad_que_sobrevive, p_usuario_id, p_observaciones);

    COMMIT;
END //

DELIMITER ;
```

## ¿Por qué esto sí tiene sentido ahora?

1. No hay duplicados falsos: Si el Lote 1 tiene 500 en "Semilla" y mueves 20 a "Germinación", el inventario dirá:

- Fila A: Lote 1 | Semilla | Cantidad: 480
- Fila B: Lote 1 | Germinación | Cantidad: 20
- Total lógico: 500. Es físicamente real.

2. Manejo de parciales: Puedes sacar 50 plantas para un cliente, 20 para siembra y dejar 30 en el vivero, y cada fila se actualizará independientemente.

3. La tabla Inventario es la "Foto Actual": No es una lista de lotes, es una lista de dónde está qué cosa en este momento.

# Pruebas de los procedimientos y de la DB en general

### 1. Limpieza Total (Truncate)

```sql
TRUNCATE TABLE historial_etapas;
TRUNCATE TABLE movimientos_inventario;
TRUNCATE TABLE inventario;
TRUNCATE TABLE lotes;
TRUNCATE TABLE ubicaciones;
TRUNCATE TABLE etapas;
TRUNCATE TABLE plantas;
```

---

### 2. Carga de Configuración (Maestros)

Sin esto no hay paraíso. Necesitamos las categorías base.

```sql
INSERT INTO plantas (id, nombre_comun, nombre_cientifico) VALUES
(1, 'Aguacate Hass', 'Persea americana'),
(2, 'Cacao', 'Theobroma cacao');

INSERT INTO etapas (id, nombre) VALUES
(1, 'Semilla'),
(2, 'Germinación'),
(3, 'Listo para Venta');

INSERT INTO ubicaciones (id, nombre) VALUES
(1, 'Bodega Central'),
(2, 'Invernadero A'),
(3, 'Zona de Despacho');
```

---

### 3. Pruebas de Flujo Real (Testing)

Aquí es donde probamos que la lógica de "fragmentar" el lote funciona.

#### A. Registro de Lote Inicial (500 Aguacates)

Llega el cargamento a la **Bodega Central** en etapa **Semilla**.

```sql
CALL sp_registrar_nuevo_lote(
    1,              -- Aguacate
    'LOTE-AGU-001', -- Código
    'unidades',
    500.00,         -- Cantidad
    1,              -- Etapa: Semilla
    1,              -- Ubi: Bodega Central
    1,              -- Usuario Brad
    'Cargamento inicial de temporada'
);
```

#### B. Movimiento Parcial con Merma (La prueba clave)

De los 500 que hay en bodega, sacamos **200** para llevarlos al **Invernadero A** para que germinen. Pero ojo: en el camino o al sembrarlos, vemos que **10** estaban podridos, así que solo entran **190** a germinación.

```sql
-- Sacamos 200 de Semilla/Bodega, pero solo entran 190 a Germinación/Invernadero
CALL sp_cambiar_etapa_lote(
    1,   -- ID Lote
    1,   -- Etapa Origen: Semilla
    1,   -- Ubi Origen: Bodega
    2,   -- Etapa Destino: Germinación
    2,   -- Ubi Destino: Invernadero
    200.00, -- Cantidad procesada (lo que sale de la bolsa)
    190.00, -- Cantidad resultante (lo que sobrevive)
    1,   -- Usuario
    'Se siembran 200, pero 10 semillas no eran aptas'
);
```

#### C. Ajuste por pérdida externa

Hubo un accidente en la **Bodega Central** y se dañaron **5** semillas de las que se habían quedado allá (las 300 restantes).

```sql
-- Insertamos directo al movimiento, el trigger restará de la fila correcta
INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
VALUES (1, 1, 1, 1, 'salida', 5.00, 'Humedad en bodega');
```

---

### 4. Verificación de "Mente Clara"

Si ejecutas esta consulta, verás la realidad física del vivero:

```sql
SELECT
    l.codigo_lote,
    et.nombre AS etapa,
    u.nombre AS ubicacion,
    i.cantidad_actual AS stock,
    l.unidad_medida
FROM inventario i
JOIN lotes l ON i.lote_id = l.id
JOIN etapas et ON i.etapa_id = et.id
JOIN ubicaciones u ON i.ubicacion_id = u.id
ORDER BY et.id;
```

**El resultado esperado debe ser:**

| Lote         | Etapa           | Ubicación      | Stock      | Unidad de Medida |
| ------------ | --------------- | -------------- | ---------- | ---------------- |
| LOTE-AGU-001 | **Semilla**     | Bodega Central | **295.00** | unidades         |
| LOTE-AGU-001 | **Germinación** | Invernadero A  | **190.00** | unidades         |

**Análisis del éxito:**

1.  Teníamos 500.
2.  Movimos 200 a germinación (quedaban 300 en semilla).
3.  De esos 200, solo 190 "nacieron" (registramos la merma de 10).
4.  De los 300 que quedaban en semilla, perdimos 5 por humedad.
5.  **Total físico:** $295 + 190 = 485$ unidades vivas. Las otras 15 están documentadas como pérdida.

## La Vista de Stock Consolidado

```sql
CREATE VIEW vista_stock_total_especie AS
SELECT
    e.id AS planta_id,
    e.nombre_comun,
    l.unidad_medida,
    SUM(i.cantidad_actual) AS stock_total,
    COUNT(DISTINCT i.lote_id) AS cantidad_lotes_activos
FROM inventario i
JOIN lotes l ON i.lote_id = l.id
JOIN plantas e ON l.planta_id = e.id
GROUP BY e.id, e.nombre_comun, l.unidad_medida;
```

# Reglas del sistema (Filosofía)

## 1. La Regla de Oro: Prohibido el DELETE y el UPDATE de cantidades

Si permitimos un UPDATE movimientos_inventario SET cantidad = 100 WHERE id = 1, el trigger que ya corrió hace semanas no se enterará, y la tabla inventario quedará "mentirosa" para siempre.

> La solución técnica: El concepto de "Anulación"

### Paso A: Agregar estado a los movimientos

```sql
ALTER TABLE movimientos_inventario
ADD COLUMN estado ENUM('activo', 'anulado') DEFAULT 'activo' AFTER motivo,
ADD COLUMN movimiento_referencia_id INT NULL AFTER estado; -- Para saber qué movimiento corrige a cuál
```

### Paso B: El Trigger de "Reversa"

Necesitamos un trigger que, cuando alguien marque un movimiento como anulado, haga exactamente lo contrario en el inventario.

```sql
DROP TRIGGER IF EXISTS tr_anular_movimiento_after_update;

DELIMITER //

CREATE TRIGGER tr_anular_movimiento_after_update
AFTER UPDATE ON movimientos_inventario
FOR EACH ROW
BEGIN
    DECLARE v_cantidad_reversa DECIMAL(10,2);

    -- Solo actuamos si el estado cambió de 'activo' a 'anulado'
    IF OLD.estado = 'activo' AND NEW.estado = 'anulado' THEN

        -- Si era entrada (sumó), la reversa resta. Si era salida (restó), la reversa suma.
        IF OLD.tipo_movimiento IN ('entrada', 'traslado_entrada') THEN
            SET v_cantidad_reversa = OLD.cantidad * -1;
        ELSE
            SET v_cantidad_reversa = OLD.cantidad;
        END IF;

        -- Aplicamos la corrección al inventario
        UPDATE inventario
        SET cantidad_actual = cantidad_actual + v_cantidad_reversa
        WHERE lote_id = OLD.lote_id
          AND etapa_id = OLD.etapa_id
          AND ubicacion_id = OLD.ubicacion_id;
    END IF;
END //

DELIMITER ;
```

## 2. ¿Qué pasa si pasaron semanas y ya no hay stock?

Supongamos que:

1. Hace 3 semanas registraste una entrada de 100 aguacates por error (eran 10).
2. Durante esas 3 semanas vendiste 80.
3. Hoy te quedan 20 en stock real.
4. Quieres "anular" la entrada de 100.

**El problema:** Si el sistema resta esos 100, tu stock quedaría en -80. ¡Imposible físicamente!

**La solución:** El procedimiento de anulación debe validar el stock antes de proceder.

```sql
DROP PROCEDURE IF EXISTS sp_anular_movimiento;

DELIMITER //

CREATE PROCEDURE sp_anular_movimiento(
    IN p_movimiento_id INT,
    IN p_usuario_id INT
)
BEGIN
    DECLARE v_lote_id INT;
    DECLARE v_etapa_id INT;
    DECLARE v_ubi_id INT;
    DECLARE v_cant_mov DECIMAL(10,2);
    DECLARE v_tipo VARCHAR(20);
    DECLARE v_stock_actual DECIMAL(10,2);

    -- 1. Obtener datos del movimiento
    SELECT lote_id, etapa_id, ubicacion_id, cantidad, tipo_movimiento
    INTO v_lote_id, v_etapa_id, v_ubi_id, v_cant_mov, v_tipo
    FROM movimientos_inventario WHERE id = p_movimiento_id;

    -- 2. Obtener stock actual para validar
    SELECT cantidad_actual INTO v_stock_actual
    FROM inventario
    WHERE lote_id = v_lote_id AND etapa_id = v_etapa_id AND ubicacion_id = v_ubi_id;

    -- 3. Validar: Si voy a anular una entrada, debo tener suficiente stock para restar
    IF v_tipo IN ('entrada', 'traslado_entrada') AND v_stock_actual < v_cant_mov THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error: No puedes anular esta entrada porque ya se usó/vendió parte de ese stock.';
    ELSE
        -- 4. Proceder con la anulación
        UPDATE movimientos_inventario
        SET estado = 'anulado', motivo = CONCAT(motivo, ' (ANULADO por usuario ', p_usuario_id, ')')
        WHERE id = p_movimiento_id;
    END IF;
END //

DELIMITER ;
```

## 3. Ajuste de Auditoría

Imaginemos por un momento que todo lo anterior sucedió, personas cometieron errores de digitación en cantidaddes, pasaron semanas y hubieron movimientos de salida y de etapa de un lote, y hoy nos damos cuenta que la cantidad ingresada ej. 500 unidades en realidad eran 50 unidades.

> [!IMPORTANT]  
> El siguiente procedimiento almacenado se recomienda ser ejecutado en entorno de Super Administrador. Y con supervisión, alterar el stock a la fuerza sin precausión puede llevar a fuertes repercuciones en la verdad que se tiene en el sistema.

### El Procedimiento: sp_ajuste_auditoria

Este procedimiento no suma ni resta a ciegas; él calcula la diferencia necesaria para que el stock digital se rinda ante la realidad física que viste en el vivero.

```sql
DROP PROCEDURE IF EXISTS sp_ajuste_auditoria;

DELIMITER //

CREATE PROCEDURE sp_ajuste_auditoria(
    IN p_lote_id INT,
    IN p_etapa_id INT,
    IN p_ubicacion_id INT,
    IN p_cantidad_fisica_real DECIMAL(10,2), -- Lo que contaste con tus ojos
    IN p_usuario_id INT,
    IN p_observaciones TEXT
)
BEGIN
    DECLARE v_stock_actual DECIMAL(10,2) DEFAULT 0;
    DECLARE v_diferencia DECIMAL(10,2);
    DECLARE v_tipo_mov ENUM('entrada', 'salida', 'ajuste');

    -- 1. Obtener cuánto cree el sistema que hay actualmente
    SELECT IFNULL(cantidad_actual, 0) INTO v_stock_actual
    FROM inventario
    WHERE lote_id = p_lote_id AND etapa_id = p_etapa_id AND ubicacion_id = p_ubicacion_id;

    -- 2. Calcular la diferencia (Realidad - Sistema)
    SET v_diferencia = p_cantidad_fisica_real - v_stock_actual;

    -- 3. Si no hay diferencia, no hacemos nada
    IF v_diferencia = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El stock físico coincide con el sistema. No es necesario el ajuste.';
    ELSE
        START TRANSACTION;

        -- Si la diferencia es positiva, es una entrada de ajuste. Si es negativa, es una salida.
        -- Usamos el valor absoluto para el registro del movimiento.
        IF v_diferencia > 0 THEN
            SET v_tipo_mov = 'entrada';
        ELSE
            SET v_tipo_mov = 'salida';
            SET v_diferencia = ABS(v_diferencia);
        END IF;

        -- 4. Registrar el movimiento de ajuste
        INSERT INTO movimientos_inventario (
            lote_id, etapa_id, ubicacion_id, usuario_id,
            tipo_movimiento, cantidad, motivo
        ) VALUES (
            p_lote_id, p_etapa_id, p_ubicacion_id, p_usuario_id,
            v_tipo_mov, v_diferencia, CONCAT('AJUSTE DE AUDITORÍA: ', p_observaciones)
        );

        COMMIT;
    END IF;
END //

DELIMITER ;
```

### Caso de Uso

**Escenario:**

El sistema dice que en el Invernadero A (Ubi: 2) en etapa Germinación (Etapa: 2) hay 190 aguacates. Pero Brad va con su tabla portapapeles, cuenta uno por uno y dice: "¡Maldición, solo hay 185!".

En lugar de buscar quién se robó 5 o dónde estuvo el error de dedo hace un mes, ejecutamos el ajuste:

```sql
CALL sp_ajuste_auditoria(
    1,              -- Lote Aguacate
    2,              -- Etapa: Germinación
    2,              -- Ubicación: Invernadero A
    185.00,         -- CANTIDAD REAL (Lo que viste)
    1,              -- Usuario Brad
    'Conteo físico mensual: se detectan 5 plantas muertas no reportadas'
);
```

### ¿Qué sucede internamente?

1. El SP hace la resta: $185 - 190 = -5$.
2. Como es negativo, genera un movimiento de salida por 5.00.
3. El Trigger detecta esa salida y resta 5 de la tabla inventario.
4. Resultado final: El inventario ahora dice exactamente 185.00. La paz vuelve al sistema.

## 4. Respondemos a la Filosofía

- **¿Se puede editar?** No deberías editar el valor (cantidad). Si te equivocaste en la nota u observación, haz un UPDATE solo a ese campo. Si la cantidad está mal, ANULA y crea uno nuevo.

- **¿Se puede eliminar?** NUNCA. El registro debe quedar ahí con estado = 'anulado'. Es el seguro de vida ante una auditoría.

- **¿Qué pasa después de semanas?** Como vimos en el procedimiento anterior, puedes anularlo **siempre y cuando la operación inversa no genere un stock negativo**. Si ya te gastaste las plantas, el error "se consolidó" y tendrías que hacer un Ajuste de Inventario (una salida por pérdida o auditoría) en lugar de una anulación.

# Pruebas finales del sistema

## Master Test: El Vivero a Prueba de Errores

### 1. Reset Total e Infraestructura

Borramos todo y cargamos lo básico.

```sql
TRUNCATE TABLE historial_etapas;
TRUNCATE TABLE movimientos_inventario;
TRUNCATE TABLE inventario;
TRUNCATE TABLE lotes;
TRUNCATE TABLE ubicaciones;
TRUNCATE TABLE etapas;
TRUNCATE TABLE plantas;

-- Carga Maestra
INSERT INTO plantas (id, nombre_comun) VALUES (1, 'Aguacate Hass');
INSERT INTO etapas (id, nombre) VALUES (1, 'Semilla'), (2, 'Germinación'), (3, 'Listo para Venta');
INSERT INTO ubicaciones (id, nombre) VALUES (1, 'Bodega Central'), (2, 'Invernadero A');
```

---

### 2. Escenario A: El Error de Dedo Inmediato (Anulación)

Brad registra la llegada de **500 unidades**, pero inmediatamente se da cuenta de que el camión solo trajo **400**.

```sql
-- 1. Registro erróneo
CALL sp_registrar_nuevo_lote(1, 'LOTE-TEST-001', 'unidades', 500.00, 1, 1, 1, 'Error de ingreso inicial');

-- 2. Verificamos que hay 500
-- SELECT * FROM inventario;

-- 3. Anulamos el movimiento (ID 1 es el del ingreso)
CALL sp_anular_movimiento(1, 1);

-- 4. Verificamos: El inventario debe estar en 0.00 y el movimiento en 'anulado'
SELECT id, tipo_movimiento, cantidad, estado FROM movimientos_inventario;
SELECT * FROM inventario;
```

---

### 3. Escenario B: El Bloqueo de Seguridad (Evitar Stock Negativo)

Vamos a intentar anular un ingreso de mercancía que **ya se movió** a otra etapa. El sistema debe impedir que el stock quede en negativo.

```sql
-- 1. Ingreso correcto de 100 semillas
CALL sp_registrar_nuevo_lote(1, 'LOTE-TEST-002', 'unidades', 100.00, 1, 1, 1, 'Lote para prueba de bloqueo');

-- 2. Movemos 80 a Germinación
CALL sp_cambiar_etapa_lote(2, 1, 1, 2, 2, 80.00, 80.00, 1, 'Traslado parcial');

-- 3. INTENTO DE ATRACO: Intentamos anular el ingreso de las 100 semillas iniciales
-- Esto debería fallar porque solo quedan 20 en la Bodega, y el sistema necesita restar 100.
CALL sp_anular_movimiento(2, 1);
-- RESULTADO ESPERADO: "Error: No puedes anular esta entrada porque ya se usó/vendió parte de ese stock."
```

---

### 4. Escenario C: El Ajuste de Auditoría (Realidad Física)

Después de varias operaciones, el sistema dice que hay **80** en el Invernadero A. Pero en el mundo real, hubo una ola de calor y solo quedan **72**.

```sql
-- El sistema cree que hay 80 (del paso anterior).
-- Aplicamos ajuste de auditoría por pérdida no reportada.
CALL sp_ajuste_auditoria(
    2,      -- Lote 2
    2,      -- Etapa: Germinación
    2,      -- Ubicación: Invernadero A
    72.00,  -- CANTIDAD REAL ENCONTRADA
    1,      -- Usuario Brad
    'Auditoría: Se encuentran 8 plantas secas por calor'
);

-- Verificamos que el stock se ajustó perfectamente a 72 (80 - 8 = 72,  Hubo una salida de 8 registrada en movimientos)
SELECT * FROM inventario WHERE lote_id = 2 AND etapa_id = 2;
```

---

### 5. El Reporte Final de Trazabilidad

Este query es el que le mostraríamos a un auditor para explicar por qué el stock es el que es.

```sql
SELECT
    m.id,
    l.codigo_lote,
    e.nombre AS etapa,
    u.nombre AS ubicacion,
    m.tipo_movimiento,
    m.cantidad,
    m.estado,
    m.motivo,
    m.fecha
FROM movimientos_inventario m
JOIN lotes l ON m.lote_id = l.id
JOIN etapas e ON m.etapa_id = e.id
JOIN ubicaciones u ON m.ubicacion_id = u.id
ORDER BY m.id ASC;
```

---

### ¿Qué acabamos de demostrar?

1.  **La Anulación funciona:** Si el stock está disponible, el sistema revierte el movimiento y limpia el inventario.
2.  **La Integridad es prioridad:** El sistema prefiere dar un error que permitir que un stock quede en $-80$.
3.  **La Auditoría es el cierre:** Si el error es viejo y ya no se puede anular, el ajuste de auditoría "nivela" el sistema con la realidad, dejando una nota clara de por qué se hizo.

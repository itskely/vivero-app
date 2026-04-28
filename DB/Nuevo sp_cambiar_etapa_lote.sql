-- Nuevo

BEGIN
    DECLARE v_nuevo_lote_id INT;
    DECLARE v_planta_id INT;
    DECLARE v_origen_id INT;
    DECLARE v_codigo_lote VARCHAR(100);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- =========================
    -- 1. Crear nuevo lote si hay cambio de unidad
    -- =========================
    IF p_nueva_unidad IS NOT NULL AND p_nueva_unidad <> '' THEN

        -- Obtener código del lote original
        SELECT planta_id, origen_id 
        INTO v_planta_id, v_origen_id 
        FROM lotes
        WHERE id = p_lote_id;

        -- Generar nuevo código (ej: PIN-2026-001.1)
        SET v_codigo_lote = UUID();

        -- Crear nuevo lote
        INSERT INTO lotes (
            planta_id, codigo_lote, fecha_creacion, unidad_medida, usuario_id, origen_id
        )
        VALUES (
            v_planta_id,
            v_codigo_lote,
            CURDATE(),
            p_nueva_unidad,
            p_usuario_id,
            v_origen_id
        );

        SET v_nuevo_lote_id = LAST_INSERT_ID();

    ELSE
        SET v_nuevo_lote_id = p_lote_id;
    END IF;

    -- =========================
    -- 2. Salida del lote original
    -- =========================
    INSERT INTO movimientos_inventario (
        lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo
    )
    VALUES (
        p_lote_id, p_etapa_origen_id, p_ubi_origen_id, p_usuario_id,
        'salida', p_cantidad_a_mover, p_observaciones
    );

    -- =========================
    -- 3. Entrada (puede ser al nuevo lote)
    -- =========================
    INSERT INTO movimientos_inventario (
        lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo
    )
    VALUES (
        v_nuevo_lote_id, p_etapa_destino_id, p_ubi_destino_id, p_usuario_id,
        'entrada', p_cantidad_que_sobrevive, p_observaciones
    );

    -- =========================
    -- 4. Historial (sin cambios estructurales)
    -- =========================
    INSERT INTO historial_etapas (
        lote_id, etapa_anterior_id, etapa_nueva_id,
        cantidad_procesada, cantidad_resultante,
        usuario_id, observaciones
    )
    VALUES (
        v_nuevo_lote_id, -- aquí usamos el nuevo si existe
        p_etapa_origen_id,
        p_etapa_destino_id,
        p_cantidad_a_mover,
        p_cantidad_que_sobrevive,
        p_usuario_id,
        p_observaciones
    );

    COMMIT;
END;
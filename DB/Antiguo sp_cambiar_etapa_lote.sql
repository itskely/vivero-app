-- Antiguo

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
END
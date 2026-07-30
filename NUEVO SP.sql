DELIMITER $$

CREATE PROCEDURE `sp_unificar_inventario`(
    IN p_planta_id INT,
    IN p_etapa_id INT,
    IN p_ubicacion_origen_id INT,   -- <--- NUEVO PARÁMETRO PARA FILTRAR
    IN p_ubicacion_destino_id INT,
    IN p_usuario_id INT,
    IN p_motivo VARCHAR(255)
)
BEGIN
    -- Variables
    DECLARE v_total_cantidad DECIMAL(10,2) DEFAULT 0;
    DECLARE v_cantidad_lotes INT DEFAULT 0; -- <--- NUEVA VARIABLE PARA CONTAR
    DECLARE v_origen_ganador_id INT;
    DECLARE v_unidad_medida VARCHAR(20);
    DECLARE v_tipo_material VARCHAR(50);
    DECLARE v_nuevo_lote_id INT;
    
    -- Variables cursor
    DECLARE v_done INT DEFAULT 0;
    DECLARE v_lote_actual_id INT;
    DECLARE v_ubicacion_actual_id INT;
    DECLARE v_cantidad_actual DECIMAL(10,2);
    
    -- 1. CURSOR ACTUALIZADO CON EL FILTRO OPCIONAL DE UBICACIÓN
    DECLARE cur_inventario CURSOR FOR 
        SELECT i.lote_id, i.ubicacion_id, i.cantidad_actual 
        FROM inventario i
        INNER JOIN lotes l ON i.lote_id = l.id
        WHERE l.planta_id = p_planta_id 
          AND i.etapa_id = p_etapa_id 
          AND i.cantidad_actual > 0
          AND (p_ubicacion_origen_id IS NULL OR i.ubicacion_id = p_ubicacion_origen_id);
          
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SELECT 'Error: Ha ocurrido un problema en la base de datos al generar las salidas.' AS mensaje_error;
    END;

    -- 2. CÁLCULO DEL TOTAL Y CONTEO DE LOTES A UNIFICAR
    SELECT COALESCE(SUM(i.cantidad_actual), 0), COUNT(i.lote_id) 
    INTO v_total_cantidad, v_cantidad_lotes
    FROM inventario i
    INNER JOIN lotes l ON i.lote_id = l.id
    WHERE l.planta_id = p_planta_id 
      AND i.etapa_id = p_etapa_id 
      AND i.cantidad_actual > 0
      AND (p_ubicacion_origen_id IS NULL OR i.ubicacion_id = p_ubicacion_origen_id);

    -- 3. VALIDACIÓN DE CANTIDAD DE LOTES
    IF v_cantidad_lotes > 1 THEN

        -- 4. ORIGEN GANADOR ACTUALIZADO CON EL FILTRO
        SELECT l.origen_id, l.unidad_medida, l.tipo_material 
        INTO v_origen_ganador_id, v_unidad_medida, v_tipo_material
        FROM lotes l
        INNER JOIN inventario i ON l.id = i.lote_id
        WHERE l.planta_id = p_planta_id 
          AND i.etapa_id = p_etapa_id 
          AND i.cantidad_actual > 0
          AND (p_ubicacion_origen_id IS NULL OR i.ubicacion_id = p_ubicacion_origen_id)
        GROUP BY l.origen_id, l.unidad_medida, l.tipo_material
        ORDER BY SUM(i.cantidad_actual) DESC
        LIMIT 1;

        -- 5. LLAMAR A TU SP (Él creará el Lote y el movimiento de Entrada)
        CALL sp_registrar_nuevo_lote(
            p_planta_id, 
            v_unidad_medida, 
            v_total_cantidad,       -- La suma total va como cantidad inicial
            p_etapa_id, 
            p_ubicacion_destino_id, 
            p_usuario_id, 
            CONCAT('Lote unificado. Motivo: ', p_motivo), 
            v_origen_ganador_id, 
            "planta desarrollada"
        );
        
        -- Capturamos el ID del lote que guardó tu SP en la variable de sesión
        SET v_nuevo_lote_id = @last_lote_id;

        -- 6. VACIAR LOS LOTES ANTIGUOS
        -- Iniciamos transacción para que todas las salidas se hagan en bloque
        START TRANSACTION;
        
        OPEN cur_inventario;
        read_loop: LOOP
            FETCH cur_inventario INTO v_lote_actual_id, v_ubicacion_actual_id, v_cantidad_actual;
            IF v_done THEN
                LEAVE read_loop;
            END IF;

            IF v_lote_actual_id != v_nuevo_lote_id THEN
            
                INSERT INTO movimientos_inventario (
                    lote_id, etapa_id, ubicacion_id, usuario_id, 
                    tipo_movimiento, cantidad, motivo
                ) VALUES (
                    v_lote_actual_id, p_etapa_id, v_ubicacion_actual_id, p_usuario_id, 
                    'salida', v_cantidad_actual, CONCAT('Salida por unificación hacia lote ', v_nuevo_lote_id)
                );
                
            END IF;
            
        END LOOP;
        CLOSE cur_inventario;

        COMMIT;
        
        SELECT CONCAT('Éxito. Lote unificado creado con ID: ', v_nuevo_lote_id, '. Cantidad total unificada: ', v_total_cantidad) AS resultado;

    ELSEIF v_cantidad_lotes = 1 THEN
        -- Si solo hay un lote, devolvemos este mensaje sin procesar nada
        SELECT 'Aviso: Solo existe un registro de inventario bajo estos criterios. No es necesario realizar la unificación.' AS resultado;
    ELSE
        SELECT 'Aviso: No se encontró inventario para unificar con los parámetros indicados.' AS resultado;
    END IF;

END$$

DELIMITER ;
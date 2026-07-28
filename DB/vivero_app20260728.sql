-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-07-2026 a las 16:12:32
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vivero_app`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ajuste_auditoria` (IN `p_lote_id` INT, IN `p_etapa_id` INT, IN `p_ubicacion_id` INT, IN `p_cantidad_fisica_real` DECIMAL(10,2), IN `p_usuario_id` INT, IN `p_observaciones` TEXT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_anular_movimiento` (IN `p_movimiento_id` INT, IN `p_usuario_id` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_etapa_lote` (IN `p_lote_id` INT, IN `p_etapa_origen_id` INT, IN `p_ubi_origen_id` INT, IN `p_etapa_destino_id` INT, IN `p_ubi_destino_id` INT, IN `p_cantidad_a_mover` DECIMAL(10,2), IN `p_cantidad_que_sobrevive` DECIMAL(10,2), IN `p_usuario_id` INT, IN `p_observaciones` TEXT, IN `p_nueva_unidad` VARCHAR(50))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- 1. Salida del origen
    INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_etapa_origen_id, p_ubi_origen_id, p_usuario_id, 'salida', p_cantidad_a_mover, CONCAT('CAMBIO ETAPA: ', p_observaciones));

    -- 2. Entrada al destino (solo lo que sobrevivió)
    INSERT INTO movimientos_inventario (lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo)
    VALUES (p_lote_id, p_etapa_destino_id, p_ubi_destino_id, p_usuario_id, 'entrada', p_cantidad_que_sobrevive, CONCAT('CAMBIO ETAPA: ', p_observaciones));

    -- 3. Historial con el texto de lo que pasó
    INSERT INTO historial_etapas (lote_id, etapa_anterior_id, etapa_nueva_id, cantidad_procesada, cantidad_resultante, usuario_id, observaciones)
    VALUES (p_lote_id, p_etapa_origen_id, p_etapa_destino_id, p_cantidad_a_mover, p_cantidad_que_sobrevive, p_usuario_id, CONCAT('CAMBIO ETAPA: ', p_observaciones));

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_nuevo_lote` (IN `p_planta_id` INT, IN `p_unidad_medida` VARCHAR(20), IN `p_cantidad_inicial` DECIMAL(10,2), IN `p_etapa_id` INT, IN `p_ubicacion_id` INT, IN `p_usuario_id` INT, IN `p_observaciones` TEXT, IN `p_origen_id` INT, IN `p_tipo_material` VARCHAR(20))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO lotes (planta_id, fecha_creacion, unidad_medida, usuario_id, observaciones, origen_id, tipo_material)
    VALUES ( p_planta_id,CURDATE(), p_unidad_medida, p_usuario_id,p_observaciones,p_origen_id,p_tipo_material);

    SET @last_lote_id = LAST_INSERT_ID();
    -- El trigger ahora se encarga de todo al insertar aquí
    INSERT INTO movimientos_inventario (lote_id,etapa_id,ubicacion_id,usuario_id, tipo_movimiento,cantidad,motivo)
    VALUES (@last_lote_id,p_etapa_id,p_ubicacion_id,p_usuario_id,'entrada',p_cantidad_inicial,CONCAT('Registro inicial:',p_observaciones));

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_transformar_lote_etapa` (IN `p_lote_padre_id` INT, IN `p_etapa_origen_id` INT, IN `p_ubi_origen_id` INT, IN `p_etapa_destino_id` INT, IN `p_ubi_destino_id` INT, IN `p_cant_salida_padre` DECIMAL(10,2), IN `p_cant_entrada_hijo` DECIMAL(10,2), IN `p_usuario_id` INT, IN `p_observaciones` TEXT, IN `p_nueva_unidad` VARCHAR(50))   BEGIN
    DECLARE v_nuevo_lote_id INT;
    DECLARE v_planta_id INT;
    DECLARE v_origen_id INT;

    -- Handler para errores: si algo falla, no se quita stock ni se crea el hijo
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- 1. Obtener la herencia del padre (sin códigos de texto)
    SELECT planta_id, origen_id
    INTO v_planta_id, v_origen_id
    FROM lotes
    WHERE id = p_lote_padre_id;

    -- 2. Crear el lote hijo (Nace la nueva unidad de medida)
    INSERT INTO lotes (
        lote_padre_id,
        planta_id,
        fecha_creacion,
        unidad_medida,
        usuario_id,
        origen_id
    )
    VALUES (
        p_lote_padre_id,
        v_planta_id,
        CURDATE(),
        p_nueva_unidad,
        p_usuario_id,
        v_origen_id
    );

    SET v_nuevo_lote_id = LAST_INSERT_ID();

    -- 3. SALIDA DEL PADRE (Consumimos los gramos/semillas)
    -- El trigger restará de la fila del padre. Si no hay stock suficiente, lanzará error.
    INSERT INTO movimientos_inventario (
        lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo
    )
    VALUES (
        p_lote_padre_id, p_etapa_origen_id, p_ubi_origen_id, p_usuario_id,
        'salida', p_cant_salida_padre, CONCAT('Transformación: ', p_observaciones)
    );

    -- 4. ENTRADA AL HIJO (Nacen las unidades/plántulas)
    INSERT INTO movimientos_inventario (
        lote_id, etapa_id, ubicacion_id, usuario_id, tipo_movimiento, cantidad, motivo
    )
    VALUES (
        v_nuevo_lote_id, p_etapa_destino_id, p_ubi_destino_id, p_usuario_id,
        'entrada', p_cant_entrada_hijo, 'Ingreso por transformación biológica'
    );

    -- 5. Historial de Etapas (Para el árbol genealógico en el Front)
    INSERT INTO historial_etapas (
        lote_id,
        etapa_anterior_id,
        etapa_nueva_id,
        cantidad_procesada,
        cantidad_resultante,
        usuario_id,
        observaciones
    )
    VALUES (
        v_nuevo_lote_id,
        p_etapa_origen_id,
        p_etapa_destino_id,
        p_cant_salida_padre,
        p_cant_entrada_hijo,
        p_usuario_id,
        CONCAT('Se transformaron ', p_cant_salida_padre, ' en ', p_cant_entrada_hijo, ' ', p_nueva_unidad)
    );

    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destino`
--

CREATE TABLE `destino` (
  `id` int(11) NOT NULL,
  `nombre_destino` varchar(255) NOT NULL,
  `descripcion` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapas`
--

CREATE TABLE `etapas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `puede_alterar_unidad` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etapas`
--

INSERT INTO `etapas` (`id`, `nombre`, `descripcion`, `puede_alterar_unidad`) VALUES
(2, 'Semillas ', 'Etapa inicial del proceso en el vivero donde se realiza la limpieza, revisión y selección de las semillas. En esta fase se verifica la calidad del material vegetal antes de pasar a la etapa de germinación', 0),
(3, 'Germinación', ' Etapa de germinación es el proceso en el cual la semilla comienza a desarrollarse y da origen a una nueva planta. En esta fase, la semilla absorbe agua, se activa el crecimiento interno y emerge la primera raíz, seguida del tallo y las primeras hojas.', 1),
(4, 'Crecimiento y desarrollo', 'Etapa de crecimiento es el periodo en el cual la planta aumenta su tamaño y desarrolla nuevas hojas, tallos y raíces después de la germinación.', 0),
(5, 'Adaptación', 'Etapa de adaptación es el proceso en el cual la planta se prepara para soportar las condiciones del ambiente externo después de haber pasado por las fases de germinación, crecimiento y desarrollo dentro del vivero.', 0),
(6, 'Rustificación', 'Etapa final del proceso en el vivero en el cual la planta se fortalece para resistir condiciones ambientales más exigentes antes de ser trasladada a su lugar definitivo.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_etapas`
--

CREATE TABLE `historial_etapas` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `etapa_anterior_id` int(11) DEFAULT NULL,
  `etapa_nueva_id` int(11) NOT NULL,
  `cantidad_procesada` decimal(10,2) NOT NULL,
  `cantidad_resultante` decimal(10,2) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `etapa_id` int(11) NOT NULL,
  `ubicacion_id` int(11) NOT NULL,
  `cantidad_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes`
--

CREATE TABLE `lotes` (
  `id` int(11) NOT NULL,
  `lote_padre_id` int(11) DEFAULT NULL,
  `planta_id` int(11) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `unidad_medida` enum('unidades','gramos') NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `origen_id` int(11) NOT NULL,
  `tipo_material` enum('semilla','plantula','esqueje','planta desarrollada') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `etapa_id` int(11) NOT NULL,
  `ubicacion_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','traslado') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `estado` enum('activo','anulado') DEFAULT 'activo',
  `movimiento_referencia_id` int(11) DEFAULT NULL,
  `destino_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `movimientos_inventario`
--
DELIMITER $$
CREATE TRIGGER `tr_actualizar_stock_after_movimiento` AFTER INSERT ON `movimientos_inventario` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_anular_movimiento_after_update` AFTER UPDATE ON `movimientos_inventario` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `origen`
--

CREATE TABLE `origen` (
  `id` int(11) NOT NULL,
  `nombre_origen` varchar(255) NOT NULL,
  `descripcion` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `ord` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_home` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pages`
--

INSERT INTO `pages` (`id`, `name`, `route`, `ord`, `icon`, `description`, `is_home`) VALUES
(1, 'Páginas', 'page.php', 2, 'fa-solid fa-book-open', 'Crea, edita o elimina las páginas de tu aplicación.', 0),
(2, 'Inicio', 'home.php', 1, 'fa-solid fa-house', 'Bienvenido al sistema de gestión del vivero\r\n', 1),
(3, 'Inventario', 'inventario.php', 11, 'fa-solid fa-boxes-stacked', ' Visualizar y controlar las plantas disponibles en el vivero y su etapa de crecimiento.', 0),
(4, 'Nueva especie', 'plantas.php', 7, 'fa-solid fa-seedling', 'Registrar nuevas plantas o especies en el sistema', 0),
(10, 'Roles', 'role.php', 3, 'fa-solid fa-shield-halved', 'Gestiona los permisos que tiene cada pagina.', 0),
(11, 'Estadisticas', 'estadisticas.php', 14, 'fa-solid fa-chart-line', 'Muestra datos del vivero para facilitar el análisis del inventario', 0),
(12, 'Usuarios', 'users.php', 4, 'fa-regular fa-user', 'listado de todos los usuarios.', 0),
(16, 'Etapas ', 'etapas.php', 6, 'fa-solid fa-layer-group', 'Etapas por las que pasan cada especie dentro del vivero', 0),
(17, 'Lotes/entradas ', 'lote.php', 8, 'fa-solid fa-dolly', 'Aquí irán los registros de cada lote por planta.', 0),
(22, 'Cambiar Etapa ', 'cambiarEtapa.php', 10, 'fa-solid fa-right-left', 'Mueve plantas de una etapa a otra con control de mermas', 0),
(23, 'Auditoria', 'auditoria.php', 13, 'fa-solid fa-list-check', 'Concilia el inventario físico con el sistema', 0),
(24, 'Ubicaciones ', 'ubicaciones.php', 5, 'fa-solid fa-location-crosshairs', 'crear ubicaciones dentro del vivero ', 0),
(25, 'Movimientos de Inventario', 'movimientos.php', 12, 'fa-solid fa-rotate', 'Historial completo de todas las transacciones del vivero', 0),
(26, 'Salidas ', 'salidas.php', 9, 'fa-solid fa-share', 'Permite registrar y controlar entradas y salida de plantas del vivero , actualizando automáticamente el inventario y asegurando la trazabilidad de cada movimiento.', 0),
(27, 'destinos ', 'destino.php', 14, 'fa-solid fa-truck-fast', 'ubicación final donde llega la planta.', 0),
(28, 'Origen', 'origen.php', 15, 'fa-solid fa-location-dot', 'Ubicación inicial de donde sale la planta.', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantas`
--

CREATE TABLE `plantas` (
  `id` int(11) NOT NULL,
  `nombre_cientifico` varchar(100) NOT NULL,
  `nombre_comun` varchar(100) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` varchar(600) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `fecha_creacion`) VALUES
(1, 'administrador', '2026-02-20 13:37:24'),
(2, 'viverista', '2026-02-20 13:37:41'),
(3, 'usuario', '2026-02-20 13:38:05'),
(9, 'Restaurador', '2026-04-16 14:39:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_pages`
--

CREATE TABLE `role_pages` (
  `page_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_pages`
--

INSERT INTO `role_pages` (`page_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(10, 1),
(11, 1),
(12, 1),
(16, 1),
(17, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(2, 9),
(3, 9),
(11, 9),
(26, 9),
(2, 2),
(3, 2),
(4, 2),
(11, 2),
(17, 2),
(22, 2),
(23, 2),
(25, 2),
(26, 2),
(2, 3),
(3, 3),
(11, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `nombre`, `descripcion`) VALUES
(2, 'Tingua', 'vivero principal '),
(3, 'Arrieros ', 'vivero  secundario ubicado en el humedal arrieros.\r\n');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` longtext NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_completo`, `cedula`, `password`, `id_rol`, `is_active`, `fecha_registro`) VALUES
(1, 'kely Quilindo', '1075677182', '$2y$10$RxTSpuokPWbQatM1DwJLXOLVAWC8jhb0WSlgCUDGROnJviz7leyii', 1, 1, '2026-02-20 13:51:56');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_stock_total_especie`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_stock_total_especie` (
`planta_id` int(11)
,`nombre_comun` varchar(100)
,`unidad_medida` enum('unidades','gramos')
,`stock_total` decimal(32,2)
,`cantidad_lotes_activos` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_stock_total_especie`
--
DROP TABLE IF EXISTS `vista_stock_total_especie`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_stock_total_especie`  AS SELECT `p`.`id` AS `planta_id`, `p`.`nombre_comun` AS `nombre_comun`, `l`.`unidad_medida` AS `unidad_medida`, sum(`i`.`cantidad_actual`) AS `stock_total`, count(distinct `i`.`lote_id`) AS `cantidad_lotes_activos` FROM ((`inventario` `i` join `lotes` `l` on(`i`.`lote_id` = `l`.`id`)) join `plantas` `p` on(`l`.`planta_id` = `p`.`id`)) GROUP BY `p`.`id`, `p`.`nombre_comun`, `l`.`unidad_medida` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `destino`
--
ALTER TABLE `destino`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `etapas`
--
ALTER TABLE `etapas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_etapas`
--
ALTER TABLE `historial_etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lote_id` (`lote_id`),
  ADD KEY `etapa_anterior_id` (`etapa_anterior_id`),
  ADD KEY `etapa_nueva_id` (`etapa_nueva_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_lote_etapa_ubi` (`lote_id`,`etapa_id`,`ubicacion_id`),
  ADD KEY `etapa_id` (`etapa_id`),
  ADD KEY `ubicacion_id` (`ubicacion_id`);

--
-- Indices de la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planta_id` (`planta_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `origen_id` (`origen_id`),
  ADD KEY `lote_padre_id` (`lote_padre_id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lote_id` (`lote_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `tipo_movimiento` (`tipo_movimiento`),
  ADD KEY `etapa_id` (`etapa_id`),
  ADD KEY `ubicacion_id` (`ubicacion_id`),
  ADD KEY `destino_id` (`destino_id`);

--
-- Indices de la tabla `origen`
--
ALTER TABLE `origen`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `plantas`
--
ALTER TABLE `plantas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_pages`
--
ALTER TABLE `role_pages`
  ADD KEY `page_id` (`page_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `rol_id` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `destino`
--
ALTER TABLE `destino`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `etapas`
--
ALTER TABLE `etapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `historial_etapas`
--
ALTER TABLE `historial_etapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes`
--
ALTER TABLE `lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `origen`
--
ALTER TABLE `origen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `plantas`
--
ALTER TABLE `plantas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_etapas`
--
ALTER TABLE `historial_etapas`
  ADD CONSTRAINT `historial_etapas_ibfk_1` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`),
  ADD CONSTRAINT `historial_etapas_ibfk_2` FOREIGN KEY (`etapa_anterior_id`) REFERENCES `etapas` (`id`),
  ADD CONSTRAINT `historial_etapas_ibfk_3` FOREIGN KEY (`etapa_nueva_id`) REFERENCES `etapas` (`id`),
  ADD CONSTRAINT `historial_etapas_ibfk_4` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`),
  ADD CONSTRAINT `inventario_ibfk_3` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`);

--
-- Filtros para la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_ibfk_1` FOREIGN KEY (`planta_id`) REFERENCES `plantas` (`id`),
  ADD CONSTRAINT `lotes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `lotes_ibfk_3` FOREIGN KEY (`origen_id`) REFERENCES `origen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lotes_ibfk_4` FOREIGN KEY (`lote_padre_id`) REFERENCES `lotes` (`id`);

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_3` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_4` FOREIGN KEY (`ubicacion_id`) REFERENCES `ubicaciones` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_5` FOREIGN KEY (`destino_id`) REFERENCES `destino` (`id`);

--
-- Filtros para la tabla `role_pages`
--
ALTER TABLE `role_pages`
  ADD CONSTRAINT `role_pages_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_pages_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

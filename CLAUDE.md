# Vista inventario

Realiza toda esta viosta que debe estar ubicada en views/inventario.php su controlador en la carpeta controllers y el modelo que está nubicado en models/InventarioModel.php, completa con los metodos que hagan falta, recuerda que esta vista en netamente informativa mostrando solo cada inventario, puedes aplicar filtrados avanzados etc.

## Base de datos

```sql
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2026 a las 17:25:34
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
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `unidad_medida` enum('unidades','gramos','kilogramos') NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `origen_id` int(11) NOT NULL
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
  `descripcion` varchar(500) NOT NULL,
  `tipo` enum('Interno','compra','Donaciones','externo') NOT NULL
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_pages`
--

CREATE TABLE `role_pages` (
  `page_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(200) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_stock_total_especie`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_stock_total_especie` (
`planta_id` int(11)
,`nombre_comun` varchar(100)
,`unidad_medida` enum('unidades','gramos','kilogramos')
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `etapas`
--
ALTER TABLE `etapas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plantas`
--
ALTER TABLE `plantas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

```

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-03-2026 a las 15:48:19
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
-- Base de datos: `vivero-app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_entradas_salidas`
--

CREATE TABLE `detalle_entradas_salidas` (
  `id_detalle` int(11) NOT NULL,
  `id_movimiento` int(11) NOT NULL,
  `id_planta` int(11) NOT NULL,
  `id_etapa` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `unidad_medida` enum('kg','unidad') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradas_salidas`
--

CREATE TABLE `entradas_salidas` (
  `id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `id_origen_destino` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entradas_salidas`
--

INSERT INTO `entradas_salidas` (`id`, `tipo_movimiento`, `id_origen_destino`, `descripcion`, `id_usuario`, `fecha`) VALUES
(32, 'entrada', 5, 'hkjhkfdhkjdf', 7, '2026-03-19 20:49:02'),
(33, 'entrada', 2, 'wefrewtfer', 1, '2026-03-19 20:49:21'),
(34, 'entrada', 5, 'rrgraergaeg', 5, '2026-03-24 14:30:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapa`
--

CREATE TABLE `etapa` (
  `id_etapa` int(11) NOT NULL,
  `nombre_etapa` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etapa`
--

INSERT INTO `etapa` (`id_etapa`, `nombre_etapa`) VALUES
(1, 'semillas'),
(2, 'germinacion'),
(3, 'crecimiento y desrrollo'),
(4, 'adaptacion'),
(5, 'rustificacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `id_planta` int(11) NOT NULL,
  `id_etapa` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `medida` enum('kg','unidad') NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `id_planta`, `id_etapa`, `id_ubicacion`, `cantidad`, `medida`, `fecha`) VALUES
(1, 6, 2, 1, 122.00, 'unidad', '2026-03-13 22:05:15'),
(2, 7, 2, 1, 15.00, 'unidad', '2026-03-13 22:05:15'),
(3, 5, 2, 1, 63.00, 'unidad', '2026-03-13 22:05:15'),
(4, 9, 1, 1, 3.00, 'kg', '2026-03-13 22:05:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_etapa`
--

CREATE TABLE `movimiento_etapa` (
  `id` int(11) NOT NULL,
  `id_planta` int(11) NOT NULL,
  `id_etapa_origen` int(11) NOT NULL,
  `id_etapa_destino` int(11) NOT NULL,
  `id_ubicacion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `medida` enum('kg','unidad') NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimiento_etapa`
--

INSERT INTO `movimiento_etapa` (`id`, `id_planta`, `id_etapa_origen`, `id_etapa_destino`, `id_ubicacion`, `cantidad`, `medida`, `id_usuario`, `fecha`) VALUES
(7, 6, 4, 5, 1, 122, 'unidad', 1, '2026-02-23 12:32:15'),
(9, 7, 3, 4, 1, 122, 'unidad', 1, '2026-02-23 12:33:29'),
(18, 7, 3, 4, 2, 122, 'unidad', 1, '2026-03-13 14:01:12'),
(20, 7, 3, 4, 2, 122, 'unidad', 9, '2026-03-13 18:41:55'),
(26, 6, 3, 4, 2, 14, 'unidad', 9, '2026-03-13 18:41:42'),
(27, 19, 4, 5, 1, 22, 'unidad', 1, '2026-03-16 16:55:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `origen_destino`
--

CREATE TABLE `origen_destino` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `origen_destino`
--

INSERT INTO `origen_destino` (`id`, `nombre`) VALUES
(1, 'Arboles para mi pais '),
(2, 'Resiembras '),
(3, 'Enriquecimiento '),
(4, 'Otros '),
(5, 'Recolecta de semillas ');

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
(2, 'Inicio', 'home.php', 1, 'fa-solid fa-house', 'Bienvenido al sistema de gestión del vghfgfdg', 1),
(3, 'Inventario', 'inventario.php', 5, 'fa-solid fa-boxes-stacked', ' Visualizar y controlar las plantas disponibles en el vivero y su etapa de crecimiento.', 0),
(4, 'Registro de Material Vegeta', 'plantas.php', 6, 'fa-solid fa-seedling', 'Registrar nuevas plantas o especies en el sistema', 0),
(5, 'Movimientos', 'movimientos.php', 7, 'fa-solid fa-arrows-turn-to-dots', 'Registrar y consultar los movimientos de las plantas dentro del vivero.', 0),
(10, 'Roles', 'role.php', 2, 'fa-solid fa-shield-halved', 'Gestiona los permisos que tiene cada pagina.', 0),
(11, 'Estadisticas', 'estadisticas.php', 10, 'fa-solid fa-chart-line', 'Muestra datos del vivero para facilitar el análisis del inventario', 0),
(12, 'Usuarios', 'users.php', 3, 'fa-regular fa-user', 'listado de todos los usuarios.', 0),
(13, 'Etradas/Salidas ', 'entradas_salidas.php', 8, 'fa-solid fa-right-left', 'Registrar las entradas y salidas de plantas del vivero para mantener actualizado el inventario.', 0),
(14, 'Informe ', 'informe.php', 10, 'fa-solid fa-file-lines', 'Generar y consultar informes sobre el inventario, movimientos y registros del vivero para facilitar el seguimiento y control de la información.', 0),
(15, 'detalle de entradas/salidas ', 'detalle_movimiento.php', 9, 'fa-solid fa-receipt', 'detalle de entradas y salidas ', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planta`
--

CREATE TABLE `planta` (
  `id_planta` int(11) NOT NULL,
  `nombre_cientifico` varchar(100) NOT NULL,
  `nombre_comun` varchar(100) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planta`
--

INSERT INTO `planta` (`id_planta`, `nombre_cientifico`, `nombre_comun`, `imagen`, `descripcion`, `fecha_registro`) VALUES
(5, 'Myrcianthes leucoxyla', 'Arrayán', NULL, 'árbol o arbusto perennifolio (hoja perenne), aromático y de crecimiento lento, nativo de América del Sur, famoso por su corteza lisa que se desprende, mostrando tonos canela, rojo o blanco. Alcanza entre 4 y 15 metros de altura, con hojas pequeñas, brilla', '2026-03-12 13:51:39'),
(6, ' Smallanthus pyramidalis.', 'árbol loco', '1111.png', 'es un árbol nativo de los Andes (1700-3000 msnm), reconocido por su crecimiento extremadamente rápido (pionero), alcanzando 10-15 metros de altura con una copa piramidal. Se caracteriza por un tronco con médula esponjosa, hojas simples, opuestas y pubesce', '2026-03-13 14:30:38'),
(7, 'Montanoa quadrangularis', 'árbol loco manizaleño ', NULL, 'es un árbol nativo de los Andes (Colombia, Venezuela, Ecuador) de crecimiento rápido, alcanzando hasta 15-20 metros de altura. Pertenece a la familia Asteraceae, caracterizándose por sus flores amarillas melíferas, tallos jóvenes cuadrangulares con médula', '2026-03-12 14:09:02'),
(8, 'Oreopanax incisus.', 'mano de oso', NULL, ' árbol nativo de los Andes (0-3000 msnm) caracterizado por sus hojas palmeadas de hasta 25 cm, que parecen manos con dedos extendidos. Alcanza hasta 15 m de altura, tiene corteza lisa, flores crema en panículas y frutos rojizos que atraen aves. Es un', '2026-03-12 14:04:58'),
(9, 'Citharexylum subflavescens', 'cajeto', NULL, 'árbol nativo de la región andina, que alcanza entre 15 y 20 metros de altura, ideal para la restauración ecológica y protección de cuencas debido a su rápido crecimiento. Se caracteriza por sus ramas cuadrangulares, hojas verde oscuro suaves y frutos drup', '2026-03-12 14:04:47'),
(10, 'Senna multiglandulosa', 'alcaparro enano', NULL, 'arbusto ornamental pequeño, generalmente de 1 a 4 metros de altura, muy ramificado y de crecimiento rápido. Se caracteriza por sus abundantes flores amarillas, follaje denso y pubescente (peludo), adaptándose bien a climas fríos y andinos (1800-3400 msnm)', '2026-03-12 14:02:47'),
(11, 'Abatia parviflora', 'duraznillo ', NULL, 'árbol nativo de los Andes colombianos (2000-3500 msnm), perteneciente a la familia Salicaceae. Puede alcanzar hasta 20 m de altura, con hojas simples opuestas, pubescentes (vellosas) y flores amarillas en racimos erectos. Es de crecimiento rápido, valorad', '2026-03-12 14:07:23'),
(14, 'Ficus tequendamae', 'caucho tequendama', NULL, 'es un árbol nativo de Colombia, perteneciente a la familia Moraceae, que puede alcanzar hasta 40 metros de altura. Se caracteriza por su copa densa, tronco recto y hojas coriáceas de color verde, siendo una especie importante para la biodiversidad y el ec', '2026-03-12 14:22:17'),
(16, 'Monnina aestuans', 'tinto', NULL, 'arbusto nativo de 3-4 metros, valorado en restauración ecológica y por atraer fauna. Presenta hojas simples, flores púrpuras o amarillas (según la especie) y bayas violetas/negras al madurar, siendo usado tradicionalmente en medicina popular.', '2026-03-12 14:31:35'),
(19, 'Erythrina rubrinervia', 'Chocho', 'planta.jpg', 'Árbol nativo de porte pequeño a mediano, que alcanza de 5 a 15 metros de altura. Se caracteriza por sus espinas en el tallo, flores rojas agrupadas en racimos llamativos, y legumbres negras que contienen semillas rojas brillantes, muy utilizadas en artesa', '2026-03-16 16:29:28');

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
(3, 'usuario', '2026-02-20 13:38:05');

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
(2, 3),
(3, 3),
(13, 3),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id_ubicacion` int(11) NOT NULL,
  `nombre_ubicacion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`id_ubicacion`, `nombre_ubicacion`) VALUES
(1, 'tingua'),
(2, 'arrieros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(200) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `cedula`, `password`, `id_rol`, `fecha_registro`) VALUES
(1, 'kely quilindo', '1075677182', '$2y$10$RxTSpuokPWbQatM1DwJLXOLVAWC8jhb0WSlgCUDGROnJviz7leyii', 1, '2026-02-20 13:51:56'),
(5, 'Brad', '3123123', '$2a$12$A0lgsYkv6FZ6wQJ9AZI/7ON9dwOu2z.stKuTsQSeoXjtMfNVQPeym', 1, '2026-03-05 17:20:39'),
(7, 'maryluz contador ', '40081754', '$2y$10$IUhoUpGQwxG4BDbzAo/OMugXsFxlxvwBn3iEpWdrrZ/MmedKnj86i', 3, '2026-03-09 16:26:49'),
(9, 'pedro mesa ', '40081755', '$2y$10$dYa2fk8wuhbxwc2P4/J1L.SWP65DrnbM35ofk5ni0kWot107FQWWG', 3, '2026-03-13 16:07:21'),
(10, 'Sergio Romero ', '11111111111', '$2y$10$5oT/2AG06RwLonYT08Gvvu//SaBrCBI/F4xNBxzRMnvJk10gNcAQm', 2, '2026-03-17 20:26:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_entradas_salidas`
--
ALTER TABLE `detalle_entradas_salidas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_movimiento_inventario` (`id_movimiento`),
  ADD KEY `id_planta` (`id_planta`),
  ADD KEY `id_etapa` (`id_etapa`),
  ADD KEY `id_ubicacion` (`id_ubicacion`),
  ADD KEY `id_movimiento` (`id_movimiento`);

--
-- Indices de la tabla `entradas_salidas`
--
ALTER TABLE `entradas_salidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_usuario_2` (`id_usuario`),
  ADD KEY `id_origen_destino` (`id_origen_destino`);

--
-- Indices de la tabla `etapa`
--
ALTER TABLE `etapa`
  ADD PRIMARY KEY (`id_etapa`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_planta` (`id_planta`),
  ADD KEY `id_etapa` (`id_etapa`),
  ADD KEY `id_ubicacion` (`id_ubicacion`);

--
-- Indices de la tabla `movimiento_etapa`
--
ALTER TABLE `movimiento_etapa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_etapa` (`id_etapa_origen`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_etapa_destino` (`id_etapa_destino`),
  ADD KEY `id_planta` (`id_planta`),
  ADD KEY `id_ubicacion` (`id_ubicacion`),
  ADD KEY `id_ubicacion_2` (`id_ubicacion`),
  ADD KEY `id_ubicacion_3` (`id_ubicacion`);

--
-- Indices de la tabla `origen_destino`
--
ALTER TABLE `origen_destino`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `planta`
--
ALTER TABLE `planta`
  ADD PRIMARY KEY (`id_planta`);

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
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `rol_id` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_entradas_salidas`
--
ALTER TABLE `detalle_entradas_salidas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `entradas_salidas`
--
ALTER TABLE `entradas_salidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `etapa`
--
ALTER TABLE `etapa`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movimiento_etapa`
--
ALTER TABLE `movimiento_etapa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `origen_destino`
--
ALTER TABLE `origen_destino`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `planta`
--
ALTER TABLE `planta`
  MODIFY `id_planta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_entradas_salidas`
--
ALTER TABLE `detalle_entradas_salidas`
  ADD CONSTRAINT `detalle_entradas_salidas_ibfk_1` FOREIGN KEY (`id_planta`) REFERENCES `planta` (`id_planta`),
  ADD CONSTRAINT `detalle_entradas_salidas_ibfk_2` FOREIGN KEY (`id_etapa`) REFERENCES `etapa` (`id_etapa`),
  ADD CONSTRAINT `detalle_entradas_salidas_ibfk_3` FOREIGN KEY (`id_movimiento`) REFERENCES `entradas_salidas` (`id`),
  ADD CONSTRAINT `detalle_entradas_salidas_ibfk_4` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `entradas_salidas`
--
ALTER TABLE `entradas_salidas`
  ADD CONSTRAINT `entradas_salidas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entradas_salidas_ibfk_2` FOREIGN KEY (`id_origen_destino`) REFERENCES `origen_destino` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_planta`) REFERENCES `planta` (`id_planta`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_etapa`) REFERENCES `etapa` (`id_etapa`),
  ADD CONSTRAINT `inventario_ibfk_3` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `movimiento_etapa`
--
ALTER TABLE `movimiento_etapa`
  ADD CONSTRAINT `movimiento_etapa_ibfk_2` FOREIGN KEY (`id_etapa_origen`) REFERENCES `etapa` (`id_etapa`),
  ADD CONSTRAINT `movimiento_etapa_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `movimiento_etapa_ibfk_4` FOREIGN KEY (`id_etapa_destino`) REFERENCES `etapa` (`id_etapa`),
  ADD CONSTRAINT `movimiento_etapa_ibfk_5` FOREIGN KEY (`id_planta`) REFERENCES `planta` (`id_planta`),
  ADD CONSTRAINT `movimiento_etapa_ibfk_6` FOREIGN KEY (`id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`);

--
-- Filtros para la tabla `role_pages`
--
ALTER TABLE `role_pages`
  ADD CONSTRAINT `role_pages_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_pages_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

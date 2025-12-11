-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-12-2025 a las 04:06:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `primeelectrove`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id`, `id_usuario`, `id_producto`, `cantidad`, `creado_en`) VALUES
(20, 3, 7, 1, '2025-12-07 18:25:00'),
(21, 3, 2, 1, '2025-12-07 18:25:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_carrito` int(11) DEFAULT NULL,
  `precio_final` decimal(10,2) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `id_usuario`, `id_carrito`, `precio_final`, `creado_en`) VALUES
(1, 1, NULL, 429.00, '2025-12-07 16:36:10'),
(2, 1, NULL, 1728.00, '2025-12-07 16:37:25'),
(3, 1, NULL, 1898.00, '2025-12-07 16:43:58'),
(4, 1, NULL, 2198.00, '2025-12-07 17:24:07'),
(5, 3, NULL, 2198.00, '2025-12-07 17:34:01'),
(6, 3, NULL, 1799.00, '2025-12-07 17:57:32'),
(7, 5, NULL, 1799.00, '2025-12-07 18:35:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `precio_producto` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre_producto`, `precio_producto`, `descripcion`, `imagen_url`, `stock`, `creado_en`) VALUES
(1, 'iPhone 15 Pro', 1299.00, 'Tecnología profesional en un cuerpo de titanio.\r\nFabricado en titanio y con el chip A17 Pro, este modelo ofrece el máximo rendimiento en juegos, fotografía y video. Una verdadera herramienta profesional con diseño ultraligero.\r\n\r\nDestacado:\r\n\r\n- Estructura de titanio\r\n\r\n- Chip A17 Pro\r\n\r\n- Cámaras de nivel profesional', 'imagenes/producto_6935be0bd95b25.84816632.jpg', 10, '2025-12-07 03:37:08'),
(2, 'iPhone 15', 899.00, 'Innovación con Dynamic Island.\r\nEl iPhone 15 incorpora Dynamic Island para una interacción más intuitiva, además del nuevo puerto USB-C. Su cámara captura detalles impresionantes y su rendimiento es ideal para multitarea y contenido multimedia.\r\n\r\nDestacado:\r\n\r\n- Dynamic Island\r\n\r\n- USB-C\r\n\r\n- Cámara renovada', 'imagenes/producto_6935be294c23b1.22290129.jpg', 15, '2025-12-07 03:37:08'),
(3, 'iPhone 14', 699.00, 'Rendimiento sólido y cámara mejorada.\r\nEl iPhone 14 destaca por su eficiencia, su doble cámara mejorada y una experiencia fluida en apps y juegos. Un dispositivo equilibrado y moderno para el día a día.\r\n\r\nDestacado:\r\n\r\n- Doble cámara de alto nivel\r\n\r\n- Excelente rendimiento\r\n\r\n- Diseño actualizado', 'imagenes/producto_6935bdc796a567.69783623.jpg', 20, '2025-12-07 03:37:08'),
(4, 'iPhone 13', 599.00, 'Equilibrio perfecto entre rendimiento y precio.\r\nEl iPhone 13 ofrece una cámara versátil, buena batería y una pantalla brillante en un diseño moderno. Ideal para usuarios que quieren un iPhone confiable sin gastar demasiado.\r\n\r\nDestacado:\r\n\r\n- Gran relación calidad-precio\r\n\r\n- Cámaras de alto rendimiento\r\n\r\n- Batería optimizada', 'imagenes/producto_6935bd98dc91c1.90106598.jpg', 25, '2025-12-07 03:37:08'),
(5, 'iPhone SE (3ra gen)', 429.00, 'Compacto, rápido y muy accesible.\r\nDiseño clásico con rendimiento moderno. El iPhone SE ofrece potencia con su chip avanzado, un tamaño cómodo y el icónico Touch ID. Perfecto para quienes buscan un iPhone eficiente y económico.\r\n\r\nDestacado:\r\n\r\n- Diseño compacto\r\n\r\n- Desbloqueo con Touch ID\r\n\r\n- Excelente rendimiento-precio', 'imagenes/producto_6935bbe363fe78.20509715.jpg', 30, '2025-12-07 03:37:08'),
(6, 'Iphone 14 pro max', 899.00, 'Potencia profesional en tus manos.\r\nCon su pantalla Super Retina XDR de 6.7\", el iPhone 14 Pro Max ofrece colores vibrantes y fluidez excepcional. Su sistema de cámaras avanzadas y su alto rendimiento lo convierten en un dispositivo perfecto para creadores de contenido y usuarios exigentes.\r\n\r\nDestacado:\r\n\r\n- Pantalla amplia y brillante\r\n\r\n- Cámaras profesionales\r\n\r\n- Diseño elegante y resistente', 'imagenes/producto_6935bd5def9461.52098748.jpg', 10, '2025-12-07 17:05:15'),
(7, 'Iphone 17 Pro Max', 1799.00, 'Potencia y elegancia de última generación.\r\nEl iPhone 17 Pro Max redefine el rendimiento con su nuevo chip ultraeficiente, diseño premium y cámaras de nivel profesional capaces de capturar fotos y videos con una nitidez sorprendente. Ideal para quienes buscan lo mejor de lo mejor en tecnología móvil.\r\n\r\nDestacado:\r\n\r\n- Rendimiento extremo\r\n\r\n- Cámaras Pro de última generación\r\n\r\n- Batería de larga duración', 'imagenes/producto_6935baf15645a8.03886120.jpg', 11, '2025-12-07 17:35:45'),
(8, 'Iphone 16 Pro Max', 999.00, '', 'imagenes/producto_6935e5dcca9272.99011618.jpg', 0, '2025-12-07 20:38:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('user','admin') NOT NULL DEFAULT 'user',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `password_hash`, `rol`, `creado_en`) VALUES
(1, 'Jose david', 'Rodriguez', 'josedavid@gmail.com', '$2y$10$lfWZp/00NrHvqcaNOJjlS.b6HqMAOaIOaGWAsSjsN2Klr1H0aA/6.', 'user', '2025-12-07 05:05:56'),
(3, 'Jose David', 'Rodriguez', 'josedavidrm764@gmail.com', '$2y$10$QkDpUuNKJjLTkYSKKfyC9ObA4J1wxvP1uCJD9ZSKEEYV8GO91xh7i', 'admin', '2025-12-07 16:57:11'),
(4, 'Miguel', 'Suarez', 'miguelS@gmail.com', '$2y$10$ZttJJSdGjEDSxs8bbhHnSeECZTL9W6Wxkq1hdYVm/RubBIF0vZcZq', 'user', '2025-12-07 17:59:50'),
(5, 'fabricio', 'Moya', 'fabrim@gmail.com', '$2y$10$E0/0321SJwoPH8CQVTM8MuwAGMyuu0ZIO8.u4aauEarOZigb4oTbG', 'user', '2025-12-07 18:35:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_carrito` (`id_carrito`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

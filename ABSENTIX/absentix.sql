-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2025 a las 21:41:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `absentix`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) UNSIGNED NOT NULL,
  `num_control` varchar(10) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `grado` varchar(5) NOT NULL,
  `grupo` varchar(5) NOT NULL,
  `turno` enum('Matutino','Vespertino') NOT NULL,
  `especialidad` varchar(50) NOT NULL,
  `estatus` enum('Activo','Baja','Suspendido') DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `num_control`, `nombre_completo`, `grado`, `grupo`, `turno`, `especialidad`, `estatus`, `fecha_registro`) VALUES
(1, '12345678', 'ayuda', '5', 'b', 'Matutino', 'HOSPEDAJE', 'Activo', '2025-10-24 18:39:25'),
(2, '123456433', 'sofia edith ', '3', 'b', 'Matutino', 'MECANICA', 'Activo', '2025-10-24 18:45:57'),
(3, '1234567333', 'mariana chapideidad', '5', 'C', 'Matutino', 'PROGRAMACIÓN', 'Activo', '2025-10-24 19:10:38'),
(4, '144554555', 'Jorge martinez', '5', 'B', 'Matutino', 'RUSA', 'Activo', '2025-10-24 19:13:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justificantes`
--

CREATE TABLE `justificantes` (
  `id_justificante` int(11) UNSIGNED NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `grado` varchar(5) NOT NULL,
  `grupo` varchar(5) NOT NULL,
  `especialidad` varchar(50) NOT NULL,
  `num_control` varchar(20) NOT NULL,
  `turno` enum('Matutino','Vespertino') NOT NULL,
  `fechas_ausencia` text NOT NULL COMMENT 'Rango o lista de fechas de inasistencia (Ej: 30/Sep/2025 o 15-17/Oct/2025)',
  `motivo` varchar(50) NOT NULL COMMENT 'Código del motivo (medico, familiar, oficial, otro)',
  `notas_adicionales` text DEFAULT NULL,
  `doc_soporte_ruta` varchar(255) DEFAULT NULL COMMENT 'Ruta del archivo adjunto (si se guarda en el servidor)',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `justificantes`
--

INSERT INTO `justificantes` (`id_justificante`, `nombre_completo`, `grado`, `grupo`, `especialidad`, `num_control`, `turno`, `fechas_ausencia`, `motivo`, `notas_adicionales`, `doc_soporte_ruta`, `fecha_registro`) VALUES
(1, '', '', '', '', '12345678', 'Matutino', '15/10/09 a 17/10/09', 'oficial', 'LKLLLLLLLLLLLKLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL', 'uploads/documentos/12345678_1761331968.png', '2025-10-24 18:52:48'),
(2, 'TADEO ALBERTO', '5', 'A', 'PROGRAMACIÓN', '12345678', 'Matutino', '15/10/09 a 17/10/09', 'familiar', 'GDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDFDF', 'uploads/documentos/12345678_1761332427.png', '2025-10-24 19:00:27'),
(3, '', '', '', '', '1234567333', 'Matutino', '67/09/90 a 69/09/90', 'familiar', 'GHDSFGDDDDFFDDDDDDDDDDDDDD', 'uploads/documentos/1234567333_1761333038.png', '2025-10-24 19:10:38'),
(4, '', '', '', '', '144554555', 'Matutino', '00/1/20000', 'familiar', 'NACO', NULL, '2025-10-24 19:13:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_conducta`
--

CREATE TABLE `reportes_conducta` (
  `id_reporte` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre_alumno` varchar(255) NOT NULL,
  `grado` varchar(10) NOT NULL,
  `grupo` varchar(10) NOT NULL,
  `turno` varchar(50) NOT NULL,
  `especialidad` varchar(100) NOT NULL,
  `num_control` varchar(20) NOT NULL,
  `fecha_incidente` date NOT NULL,
  `nombre_reportante` varchar(255) NOT NULL,
  `tipo_falta` enum('leve','moderada','grave') NOT NULL,
  `descripcion_incidente` text NOT NULL,
  `estatus` enum('pendiente','notificado','resuelto') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `usuario` varchar(50) NOT NULL COMMENT 'Nombre de usuario (Ej: orientacionedu258)',
  `contrasena` varchar(255) NOT NULL COMMENT 'Contraseña hasheada (Debe ser hasheada, NO texto plano)',
  `rol` enum('admin','orientador') NOT NULL DEFAULT 'orientador',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `contrasena`, `rol`, `fecha_creacion`) VALUES
(1, 'orientacionedu258', 'orien258', 'orientador', '2025-10-24 14:12:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `num_control` (`num_control`);

--
-- Indices de la tabla `justificantes`
--
ALTER TABLE `justificantes`
  ADD PRIMARY KEY (`id_justificante`);

--
-- Indices de la tabla `reportes_conducta`
--
ALTER TABLE `reportes_conducta`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `idx_num_control` (`num_control`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `justificantes`
--
ALTER TABLE `justificantes`
  MODIFY `id_justificante` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `reportes_conducta`
--
ALTER TABLE `reportes_conducta`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

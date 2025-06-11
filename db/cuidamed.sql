-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-06-2025 a las 09:11:09
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
-- Base de datos: `cuidamed`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `id_medicamento` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`id_medicamento`, `nombre`, `descripcion`) VALUES
(1, 'Paracetamol', 'Analgésico para dolores leves o moderados'),
(2, 'Ibuprofeno', 'Antiinflamatorio para dolores e inflamaciones'),
(3, 'Omeprazol', 'Protector gástrico para evitar acidez'),
(4, 'Amoxicilina', 'Antibiótico para infecciones bacterianas'),
(5, 'Metformina', 'Medicamento para controlar la diabetes tipo 2'),
(6, 'Loratadina', 'Antihistamínico para alergias'),
(7, 'Simvastatina', 'Reductor de colesterol'),
(8, 'Aspirina', 'Analgésico y antiinflamatorio'),
(9, 'Losartán', 'Antihipertensivo para controlar la presión arterial'),
(10, 'Metoprolol', 'Betabloqueante para hipertensión y angina'),
(11, 'Salbutamol', 'Broncodilatador para el asma'),
(12, 'Clonazepam', 'Ansiolítico para la ansiedad y convulsiones'),
(13, 'Levotiroxina', 'Hormona tiroidea para hipotiroidismo'),
(14, 'Enalapril', 'Inhibidor de la ECA para hipertensión'),
(15, 'Diazepam', 'Ansiolítico y sedante'),
(16, 'Diclofenaco', 'Antiinflamatorio para dolor e inflamación'),
(17, 'Acetaminofén', 'Analgésico y antipirético'),
(18, 'Doxiciclina', 'Antibiótico de amplio espectro'),
(19, 'Azitromicina', 'Antibiótico macrólido'),
(20, 'Prednisona', 'Corticosteroide para inflamación'),
(21, 'Atorvastatina', 'Reductor de colesterol'),
(22, 'Furosemida', 'Diurético para retención de líquidos'),
(23, 'Gabapentina', 'Antiepiléptico y analgésico neuropático'),
(24, 'Ranitidina', 'Antiácido y protector gástrico'),
(25, 'Sertralina', 'Antidepresivo inhibidor de la recaptación de serotonina'),
(26, 'Amlodipino', 'Antihipertensivo y antianginoso'),
(27, 'Cetirizina', 'Antihistamínico para alergias'),
(28, 'Tramadol', 'Analgésico para dolores moderados a severos'),
(29, 'Insulina', 'Hormona para controlar la diabetes'),
(30, 'Naproxeno', 'Antiinflamatorio no esteroideo'),
(31, 'Fluconazol', 'Antifúngico para infecciones por hongos'),
(32, 'Warfarina', 'Anticoagulante para prevenir trombos'),
(33, 'Clopidogrel', 'Antiagregante plaquetario'),
(34, 'Amiodarona', 'Antiarrítmico para arritmias cardíacas'),
(35, 'Lansoprazol', 'Inhibidor de la bomba de protones para acidez'),
(36, 'Tamsulosina', 'Relajante muscular para próstata'),
(37, 'Bisoprolol', 'Betabloqueante para hipertensión y arritmias'),
(38, 'Aciclovir', 'Antiviral para herpes'),
(39, 'Insulina glargina', 'Insulina de acción prolongada para diabetes tipo 1 y 2'),
(40, 'Valaciclovir', 'Antiviral para infecciones virales como herpes y varicela');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios`
--

CREATE TABLE `recordatorios` (
  `id_recordatorio` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_medicamento` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `creado_por_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recordatorios`
--

INSERT INTO `recordatorios` (`id_recordatorio`, `id_usuario`, `id_medicamento`, `fecha_hora`, `dosis`, `creado_por_admin`) VALUES
(13, 4, 38, '2025-06-10 16:00:00', 'Una pastilla', 0),
(14, 4, 38, '2025-06-11 16:00:00', 'Una pastilla', 0),
(15, 4, 38, '2025-06-17 16:00:00', 'Una pastilla', 0),
(17, 4, 38, '2025-06-13 15:15:00', '1 tableta', 0),
(18, 4, 38, '2025-06-14 15:15:00', '1 tableta', 0),
(19, 4, 37, '2025-06-10 14:02:00', 'Una pastilla', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tomas_medicamento`
--

CREATE TABLE `tomas_medicamento` (
  `id_toma` int(11) NOT NULL,
  `id_recordatorio` int(11) NOT NULL,
  `estado` enum('pendiente','tomado','omitido') NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tomas_medicamento`
--

INSERT INTO `tomas_medicamento` (`id_toma`, `id_recordatorio`, `estado`, `fecha_registro`) VALUES
(5, 13, 'tomado', '2025-06-10 02:53:01'),
(6, 14, 'pendiente', '2025-06-10 02:53:01'),
(7, 15, 'pendiente', '2025-06-10 02:53:01'),
(9, 17, 'pendiente', '2025-06-10 11:00:33'),
(10, 18, 'pendiente', '2025-06-10 11:00:33'),
(11, 19, 'tomado', '2025-06-10 11:04:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('paciente','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `telefono`, `contrasena`, `rol`) VALUES
(4, 'Andres Benjumea', 'andresbenju@gmail.com', '3023344972', '$2y$10$PynBsL.w4RK3XHNsfEYWoeumeTOnDt24PLaxB6FUGrphNaM36t4Dq', 'paciente'),
(5, 'Admin de Cuidamed', 'admin@cuidamed.com', '3113113131', '$2y$10$sLHc/cu7E1SzG2fcSM6Fqu7LHIPmm39YAJI1uIWPMUONkfD/R1Dw6', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`id_medicamento`);

--
-- Indices de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  ADD PRIMARY KEY (`id_recordatorio`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_medicamento` (`id_medicamento`);

--
-- Indices de la tabla `tomas_medicamento`
--
ALTER TABLE `tomas_medicamento`
  ADD PRIMARY KEY (`id_toma`),
  ADD KEY `id_recordatorio` (`id_recordatorio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  MODIFY `id_recordatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tomas_medicamento`
--
ALTER TABLE `tomas_medicamento`
  MODIFY `id_toma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `recordatorios`
--
ALTER TABLE `recordatorios`
  ADD CONSTRAINT `recordatorios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `recordatorios_ibfk_2` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamentos` (`id_medicamento`);

--
-- Filtros para la tabla `tomas_medicamento`
--
ALTER TABLE `tomas_medicamento`
  ADD CONSTRAINT `tomas_medicamento_ibfk_1` FOREIGN KEY (`id_recordatorio`) REFERENCES `recordatorios` (`id_recordatorio`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

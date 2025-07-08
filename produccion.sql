SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `color` (
  `id_color` int(3) NOT NULL,
  `codigo_color` varchar(6) DEFAULT NULL,
  `descripcion_color` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `jornada_laboral` (
  `id_jornada` int(4) NOT NULL,
  `tipo_jornada` varchar(10) DEFAULT NULL,
  `horario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `maquinas` (
  `id_maquina` int(3) NOT NULL,
  `marca_maquina` varchar(20) NOT NULL,
  `nro_cabezas` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `registro_diario` (
  `id_transaccion` int(10) NOT NULL,
  `marca_temporal` datetime DEFAULT NULL,
  `colaborador` varchar(50) DEFAULT NULL,
  `maquina_operada` varchar(50) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `orden_produccion` int(10) DEFAULT NULL,
  `referencia` int(10) DEFAULT NULL,
  `tipo_bordado` varchar(20) DEFAULT NULL,
  `tamaño_pieza` varchar(20) DEFAULT NULL,
  `puntadas_diseño` int(10) DEFAULT NULL,
  `cantidad_unidades` int(10) DEFAULT NULL,
  `total_puntadas` int(10) DEFAULT NULL,
  `color_realizado` varchar(10) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tamaño_pieza` (
  `id_tamaño_pieza` int(3) NOT NULL,
  `tamaño_pieza` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipo_bordado` (
  `id_bordado` int(3) NOT NULL,
  `tipo_bordado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `tipo_identificacion` enum('CC','CE') NOT NULL COMMENT 'CC: Cédula de Ciudadanía, CE: Cédula de Extranjería',
  `numero_identificacion` varchar(11) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `rol` enum('administrador','operario') NOT NULL DEFAULT 'operario',
  `password` varchar(255) NOT NULL,
  `primer_ingreso` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Debe cambiar clave, 0 = Ya la cambió',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `color`
  ADD PRIMARY KEY (`id_color`);

ALTER TABLE `jornada_laboral`
  ADD PRIMARY KEY (`id_jornada`);

ALTER TABLE `maquinas`
  ADD PRIMARY KEY (`id_maquina`);

ALTER TABLE `registro_diario`
  ADD PRIMARY KEY (`id_transaccion`);

ALTER TABLE `tamaño_pieza`
  ADD PRIMARY KEY (`id_tamaño_pieza`);

ALTER TABLE `tipo_bordado`
  ADD PRIMARY KEY (`id_bordado`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `numero_identificacion` (`numero_identificacion`);


ALTER TABLE `color`
  MODIFY `id_color` int(3) NOT NULL AUTO_INCREMENT;

ALTER TABLE `jornada_laboral`
  MODIFY `id_jornada` int(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `maquinas`
  MODIFY `id_maquina` int(3) NOT NULL AUTO_INCREMENT;

ALTER TABLE `registro_diario`
  MODIFY `id_transaccion` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tamaño_pieza`
  MODIFY `id_tamaño_pieza` int(3) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tipo_bordado`
  MODIFY `id_bordado` int(3) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

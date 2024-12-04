-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2024 a las 18:16:16
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
-- Base de datos: `proyecto web`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `usuario` varchar(20) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `calificacion` int(11) NOT NULL,
  `comentario` varchar(1024) NOT NULL,
  `libro` varchar(20) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`usuario`, `titulo`, `calificacion`, `comentario`, `libro`, `fecha`) VALUES
('Charly Morales', 'maomeno', 0, 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Debitis blanditiis eius id magnam modi, saepe, corrupti repellat quas quaerat illo accusamus fugit. Possimus voluptatem aperiam laboriosam dolorem eos, culpa nostrum.', 'La Ilíada', '2024-11-27'),
('Reviewer name', 'Excelente', 5, 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempore ab rem eius quia. Delectus eum est, corporis iure facilis animi temporibus voluptatum error perspiciatis magni corrupti omnis enim sequi commodi ex molestias dolorum, esse consequuntur ratione laborum alias quae! Praesentium odit asperiores porro quisquam, vitae officia similique blanditiis autem quidem laborum quis expedita, cumque ducimus illum eum reiciendis accusamus unde officiis, maiores reprehenderit rem sit. Cumque distinctio quasi aut molestias magnam nulla nesciunt ipsam odit?', 'La Ilíada', '2024-11-27'),
('charly morales', 'ZZZZZ ', 3, 'no me gusto es dog shit', 'La Ilíada', '2024-11-28'),
('charly morales', 'me dormi', 4, 'no le saben al chipotle', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 1, 'no le saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'me dormi', 3, 'skgkjslgjfdg', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', 'ZZZZZ ', 3, 'no l saben', 'La Ilíada', '2024-11-28'),
('charly morales', '', 2, 'No le sabe el Arturin', 'El Negociador', '2024-12-02'),
('charly morales', '', 0, '', 'El Negociador', '2024-12-03'),
('Patito', '', 3, 'CUAK CUAK CUAK', 'La Ilíada', '2024-12-03'),
('Patito', '', 0, '', 'La Ilíada', '2024-12-03'),
('Patito', '', 0, '', 'El negociador', '2024-12-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guardados`
--

CREATE TABLE `guardados` (
  `usuario` varchar(20) NOT NULL,
  `libro` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guardados`
--

INSERT INTO `guardados` (`usuario`, `libro`) VALUES
('charly morales', '1984'),
('charly morales', 'El negociador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro`
--

CREATE TABLE `libro` (
  `nombre` varchar(20) NOT NULL,
  `autor` varchar(20) NOT NULL,
  `pt` float NOT NULL,
  `genero` varchar(20) NOT NULL,
  `sinopsis` varchar(500) NOT NULL,
  `pag` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `dueño` varchar(11) NOT NULL,
  `disponible` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libro`
--

INSERT INTO `libro` (`nombre`, `autor`, `pt`, `genero`, `sinopsis`, `pag`, `img`, `dueño`, `disponible`) VALUES
('El negociador', 'Arturo Elias', 5, 'Negocios', 'asdasdasdasd', 95, 'uploads/Papu.jpg', 'Patito', 'SI'),
('El negociador', 'Arturo Elias', 5, 'Negocios', 'asdasdasdasdasfasasfa', 95, 'uploads/Papu.jpg', 'Patito', 'SI'),
('La Iliada', 'Homero', 5, 'Distopía', 'asdajhsdfsdfnshcnhsnkchsdjkhjhsdfjshfjshksjdfsf', 97, 'uploads/iliada.png', 'Patito', 'SI'),
('Cien años', 'Gabriel Garcia', 3, 'historia', 'aaddhfjshdndvnnxzvnznccccccccccccccccccccccccccccccccc', 98, 'uploads/soledad.jpg', 'charly mora', 'SI');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `usuario_sol` varchar(20) NOT NULL,
  `libro` varchar(30) NOT NULL,
  `usuario_pres` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `user` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `telefono` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`user`, `password`, `email`, `telefono`) VALUES
('usuario1', '123', 'correo@correo.com', 123456889),
('Charly Morales', '1234', 'Moralescarlos@correo.com', 27215483),
('Cris56', '1572', 'beviro4209@morxin.com', 27215483),
('Patito', '12345678', 'hicehe4817@janfab.com', 23546789);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

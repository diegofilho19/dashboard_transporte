-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/02/2025 às 18:25
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `app_sistema`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `nome`, `email`, `senha`) VALUES
(1, 'teste', 'teste@gmail.com', '$2y$10$Ea7MXt7OMUsXRh44fMWK2.pUphV1ti0LxyhYavMlZQfSg4qbh0.U.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `matricula` varchar(20) DEFAULT NULL,
  `numero_tel` varchar(20) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `id_faculdade` int(11) DEFAULT NULL,
  `curso` varchar(100) DEFAULT NULL,
  `id_cidade` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo',
  `data_insercao` datetime NOT NULL DEFAULT current_timestamp(),
  `id_fiscal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome_completo`, `cpf`, `matricula`, `numero_tel`, `senha`, `id_faculdade`, `curso`, `id_cidade`, `foto`, `status`, `data_insercao`, `id_fiscal`) VALUES
(26, 'Diego', '123.456.789-00', '12345465', '(81) 99999-9999', '$2y$10$fUWBeA9H/CnXPHTuPxFN5Oc/CEap9KR.4RzRKW1QGEPVfAyLwHgVW', 8, 'curso', 0, '1740663572_foto-1740663571921.jpg', 'Ativo', '2025-02-27 10:39:32', NULL),
(27, 'Rafa', '287.371.624-76', '123654', '(81) 99999-9999', '$2y$10$rebxA3mgx4mb3uPj9WlU.u3HLliQvUzG7Kg0819OSoF/kVcKIodQu', 10, 'curso', 0, NULL, 'Ativo', '2025-02-27 11:29:38', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_fiscais`
--

CREATE TABLE `alunos_fiscais` (
  `id` int(11) NOT NULL,
  `id_aluno` int(11) NOT NULL,
  `id_fiscal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos_fiscais`
--

INSERT INTO `alunos_fiscais` (`id`, `id_aluno`, `id_fiscal`) VALUES
(15, 26, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `faculdades`
--

CREATE TABLE `faculdades` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sigla` varchar(20) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `faculdades`
--

INSERT INTO `faculdades` (`id`, `nome`, `sigla`, `cidade`, `tipo`) VALUES
(1, 'Universidade Federal de Pernambuco', 'UFPE', 'Recife', 'Pública'),
(2, 'Universidade de Pernambuco', 'UPE', 'Recife', 'Pública'),
(3, 'Universidade Católica de Pernambuco', 'UNICAP', 'Recife', 'Privada'),
(4, 'Universidade Federal Rural de Pernambuco', 'UFRPE', 'Recife', 'Pública'),
(5, 'Faculdade Boa Viagem', 'FBV', 'Recife', 'Privada'),
(6, 'Instituto Federal de Pernambuco', 'IFPE', 'Recife', 'Pública'),
(7, 'Faculdade de Comunicação e Design do Recife', 'FCDR', 'Recife', 'Privada'),
(8, 'Centro Universitário Maurício de Nassau', 'UNINASSAU', 'Recife', 'Privada'),
(9, 'Centro Universitário Maurício de Nassau', 'UNINASSAU', 'Caruaru', 'Privada'),
(10, 'Centro Universitário Maurício de Nassau', 'UNINASSAU', 'Petrolina', 'Privada'),
(11, 'Centro Universitário do Vale do Ipojuca', 'UNIFAVIP', 'Recife', 'Privada'),
(12, 'Centro Universitário do Vale do Ipojuca', 'UNIFAVIP', 'Caruaru', 'Privada'),
(13, 'Universidade Federal do Vale do São Francisco', 'UNIVASF', 'Petrolina', 'Pública'),
(14, 'Faculdade de Ciências Aplicadas e Sociais de Petrolina', 'FACAPE', 'Petrolina', 'Pública'),
(15, 'Faculdade de Medicina de Olinda', 'FMO', 'Olinda', 'Privada'),
(16, 'Faculdade de Ciências Humanas de Olinda', 'FACHO', 'Olinda', 'Privada'),
(17, 'Faculdade de Tecnologia e Ciências de Caruaru', 'FATEC', 'Caruaru', 'Privada'),
(18, 'Universidade Federal de Pernambuco - Campus Agreste', 'UFPE-CAA', 'Caruaru', 'Pública'),
(19, 'Faculdade Asces-Unita', 'ASCES', 'Caruaru', 'Privada'),
(20, 'Universidade Federal de Pernambuco - Recife', 'UFPE', 'Recife', 'Federal'),
(21, 'Universidade Federal de Pernambuco - Caruaru', 'UFPE', 'Caruaru', 'Federal'),
(22, 'Universidade Federal de Pernambuco - Vitória de Santo Antão', 'UFPE', 'Vitória de Santo Antão', 'Federal'),
(23, 'Universidade de Pernambuco - Caruaru', 'UPE', 'Caruaru', 'Estadual'),
(24, 'Universidade de Pernambuco - Garanhuns', 'UPE', 'Garanhuns', 'Estadual'),
(25, 'Universidade de Pernambuco - Recife', 'UPE', 'Recife', 'Estadual');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fiscais`
--

CREATE TABLE `fiscais` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cnh` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `nome_carro` varchar(50) DEFAULT NULL,
  `placa` varchar(10) DEFAULT NULL,
  `destino` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fiscais`
--

INSERT INTO `fiscais` (`id`, `nome`, `cnh`, `senha`, `numero`, `nome_carro`, `placa`, `destino`) VALUES
(3, 'Rafael Bloys', '12873742', '', '(81) 96968-1465', 'Voyage', 'VDO-2424', 'Recife'),
(4, 'Manu', '92831623', '', '(81) 92313-2454', 'Sprinter', 'OCU-0800', 'Caruaru');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_faculdade` (`id_faculdade`);

--
-- Índices de tabela `alunos_fiscais`
--
ALTER TABLE `alunos_fiscais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aluno` (`id_aluno`),
  ADD KEY `id_fiscal` (`id_fiscal`);

--
-- Índices de tabela `faculdades`
--
ALTER TABLE `faculdades`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fiscais`
--
ALTER TABLE `fiscais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnh` (`cnh`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `alunos_fiscais`
--
ALTER TABLE `alunos_fiscais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `faculdades`
--
ALTER TABLE `faculdades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `fiscais`
--
ALTER TABLE `fiscais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`id_faculdade`) REFERENCES `faculdades` (`id`);

--
-- Restrições para tabelas `alunos_fiscais`
--
ALTER TABLE `alunos_fiscais`
  ADD CONSTRAINT `alunos_fiscais_ibfk_1` FOREIGN KEY (`id_aluno`) REFERENCES `alunos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alunos_fiscais_ibfk_2` FOREIGN KEY (`id_fiscal`) REFERENCES `fiscais` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

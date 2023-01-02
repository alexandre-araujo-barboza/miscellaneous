-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 01-Jan-2023 às 16:05
-- Versão do servidor: 10.4.6-MariaDB
-- versão do PHP: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `testes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `contatos`
--

CREATE TABLE `contatos` (
  `codigo` int(11) UNSIGNED NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `sobrenome` varchar(50) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `contatos`
--

INSERT INTO `contatos` (`codigo`, `nome`, `sobrenome`, `email`) VALUES
(1, 'Alex', 'Barbosa', 'alexandrearaujobarboza@gmail.com'),
(2, 'Alexandre', 'Araujo Barbosa', 'alexandre.araujo.barboza@gmail.com'),
(3, 'Emengarda', 'De Souza', 'emengarda@gmail.com'),
(4, 'Paulo', 'Pedro', 'paupedra@gmail.com'),
(5, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `codigo` int(11) UNSIGNED NOT NULL,
  `codigo_contato` int(11) UNSIGNED NOT NULL,
  `cep` int(8) UNSIGNED DEFAULT NULL,
  `logradouro` varchar(80) DEFAULT NULL,
  `numero` int(5) DEFAULT NULL,
  `complemento` varchar(20) DEFAULT NULL,
  `bairro` varchar(30) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `uf` enum('AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `telefones`
--

CREATE TABLE `telefones` (
  `codigo` int(11) UNSIGNED NOT NULL,
  `codigo_contato` int(11) UNSIGNED NOT NULL,
  `ddd` varchar(4) DEFAULT NULL,
  `numero` int(9) DEFAULT NULL,
  `tipo` enum('Celular','Fixo','WhatsApp','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `telefones`
--

INSERT INTO `telefones` (`codigo`, `codigo_contato`, `ddd`, `numero`, `tipo`) VALUES
(3, 1, '01', 77777777, 'Celular'),
(4, 1, '02', 88888888, 'Fixo'),
(5, 2, '0021', 32648354, 'Fixo'),
(6, 1, '03', 99999999, 'WhatsApp'),
(7, 2, '021', 99401876, 'Celular'),
(8, 3, '0242', 24242424, 'WhatsApp'),
(9, 3, '99', 99999999, 'WhatsApp'),
(10, 3, '99', 99401876, 'Celular'),
(12, 3, '1234', 123456789, 'Celular'),
(13, 4, '1234', 567890123, 'Fixo');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `UK_EMAIL` (`email`);

--
-- Índices para tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `IDX_CONTATO` (`codigo_contato`);

--
-- Índices para tabela `telefones`
--
ALTER TABLE `telefones`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `IDX_CONTATO` (`codigo_contato`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `telefones`
--
ALTER TABLE `telefones`
  MODIFY `codigo` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `FK_CONTATO_ENDERECO` FOREIGN KEY (`codigo_contato`) REFERENCES `contatos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `telefones`
--
ALTER TABLE `telefones`
  ADD CONSTRAINT `FK_CONTATO_TELEFONE` FOREIGN KEY (`codigo_contato`) REFERENCES `contatos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

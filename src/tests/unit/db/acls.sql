CREATE DATABASE `acls`;
USE `acls`;
CREATE TABLE `Role` (`parents` varchar(30) NOT NULL,`id` int(11) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `Permission` (`level` int(11) NOT NULL,`id` int(11) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `Resource` (`value` varchar(30) NOT NULL,`id` int(11) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `AclElement` (`id` int(11) NOT NULL,`idRole` int(11) NOT NULL,`idPermission` int(11) NOT NULL,`idResource` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `Role` ADD PRIMARY KEY (`id`);
ALTER TABLE `Role` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `Permission` ADD PRIMARY KEY (`id`);
ALTER TABLE `Permission` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `Resource` ADD PRIMARY KEY (`id`);
ALTER TABLE `Resource` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `AclElement` ADD PRIMARY KEY (`id`);
ALTER TABLE `AclElement` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `AclElement` ADD KEY (`idRole`);
ALTER TABLE `AclElement` ADD KEY (`idPermission`);
ALTER TABLE `AclElement` ADD KEY (`idResource`);
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Role` FOREIGN KEY (`idRole`) REFERENCES `Role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Permission` FOREIGN KEY (`idPermission`) REFERENCES `Permission` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Resource` FOREIGN KEY (`idResource`) REFERENCES `Resource` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Déchargement des données de la table `permission`
--

INSERT INTO `Permission` (`level`, `id`, `name`) VALUES
(1, 1, 'READ'),
(2, 2, 'WRITE');

--
-- Déchargement des données de la table `resource`
--

INSERT INTO `Resource` (`value`, `id`, `name`) VALUES
('/home', 1, 'Home'),
('/admin', 2, 'Admin');

--
-- Déchargement des données de la table `role`
--

INSERT INTO `Role` (`parents`, `id`, `name`) VALUES
('', 1, 'USER'),
('USER', 2, 'ADMIN');

--
-- Déchargement des données de la table `aclelement`
--

INSERT INTO `AclElement` (`id`, `idRole`, `idPermission`, `idResource`) VALUES
(1, 1, 1, 1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

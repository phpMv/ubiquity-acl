CREATE DATABASE `acls`;
USE `acls`;
CREATE TABLE `Role` (`parents` varchar(30) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `Permission` (`level` int(11) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `Resource` (`value` varchar(30) NOT NULL,`name` varchar(30) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `AclElement` (`id` int(11) NOT NULL,`roleName` int(11) NOT NULL,`permissionName` int(11) NOT NULL,`resourceName` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `Role` ADD PRIMARY KEY (`name`);
ALTER TABLE `Permission` ADD PRIMARY KEY (`name`);
ALTER TABLE `Resource` ADD PRIMARY KEY (`name`);
ALTER TABLE `AclElement` ADD PRIMARY KEY (`id`);
ALTER TABLE `AclElement` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `AclElement` ADD KEY (`roleName`);
ALTER TABLE `AclElement` ADD KEY (`permissionName`);
ALTER TABLE `AclElement` ADD KEY (`resourceName`);
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Role` FOREIGN KEY (`roleName`) REFERENCES `Role` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Permission` FOREIGN KEY (`permissionName`) REFERENCES `Permission` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `AclElement` ADD CONSTRAINT `fk_AclElement_Resource` FOREIGN KEY (`resourceName`) REFERENCES `Resource` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION;

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

INSERT INTO `Permission` (`level`, `name`) VALUES
(1, 'READ'),
(2, 'WRITE');

--
-- Déchargement des données de la table `resource`
--

INSERT INTO `Resource` (`value`,`name`) VALUES
('/home','Home'),
('/admin','Admin');

--
-- Déchargement des données de la table `role`
--

INSERT INTO `Role` (`parents`, `name`) VALUES
('','USER'),
('USER','ADMIN');

--
-- Déchargement des données de la table `aclelement`
--

INSERT INTO `AclElement` (`id`, `roleName`, `permissionName`, `resourceName`) VALUES
(1, 'USER', 'READ', 'Home');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

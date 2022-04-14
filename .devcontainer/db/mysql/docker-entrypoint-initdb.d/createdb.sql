CREATE DATABASE IF NOT EXISTS `devcontainer_testing` COLLATE 'utf8_general_ci' ;
GRANT ALL ON `devcontainer_testing`.* TO 'default'@'%' ;

CREATE DATABASE IF NOT EXISTS `devcontainer` COLLATE 'utf8_general_ci' ;
GRANT ALL ON `devcontainer`.* TO 'default'@'%' ;

FLUSH PRIVILEGES ;

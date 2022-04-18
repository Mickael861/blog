CREATE TABLE IF NOT EXISTS `comments` (
  
    `comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `post_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `content` TEXT NOT NULL,
    `statut` ENUM('valider', 'refuser', 'en_attente') NOT NULL DEFAULT 'en_attente',
    `date_add` DATE NOT NULL DEFAULT '0000-00-00',
        PRIMARY KEY (`comment_id`),
        KEY `post_id` (`post_id`),
        KEY `user_id` (`user_id`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `posts` (
    `post_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `chapo` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `statut` ENUM('en_attente', 'publier') NOT NULL DEFAULT 'en_attente',
    `date_add` DATE NOT NULL DEFAULT '0000-00-00',
    `user_add` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `date_upd` DATE NOT NULL DEFAULT '0000-00-00',
    `user_upd` INT(11) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`post_id`),
        KEY `user_id` (`user_id`),
        KEY `user_add` (`user_add`),
        KEY `user_upd` (`user_upd`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
    `user_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `role` ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    `pseudo` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `statut` ENUM('valider', 'refuser', 'en_attente') NOT NULL DEFAULT 'en_attente',
    `date_add` DATE NOT NULL DEFAULT '0000-00-00',
        PRIMARY KEY (`user_id`)
) 
ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* test local user admin */
INSERT INTO `users` (`user_id`, `role`, `pseudo`, `firstname`, `lastname`, `email`, `password`, `statut`, `date_add`) 
VALUES (NULL, 'admin', 'Admin', 'Admin', 'Admin', 'adminblog@admin.fr', '$argon2i$v=19$m=65536,t=4,p=1$YmVmS3JyT2w4YVBqNy9OUQ$UOir+HzuEpwwXXDHBDN26jjohZ5UzidP84UoFEG5WWs', 'valider', '2022-04-05');

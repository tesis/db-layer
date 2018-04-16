<?php

return [
    'users' => [
                  'CREATE TABLE IF NOT EXISTS `users` (
                  `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `name` varchar(50) NOT NULL,
                  `email` varchar(50) NOT NULL,
                  `phone` varchar(100) NOT NULL,
                  `password` varchar(200) NOT NULL,
                  `address` varchar(50) NOT NULL,
                  `city` varchar(50) NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB;'
            ],

    'user_data' => [
                  'CREATE TABLE IF NOT EXISTS `user_data` (
                  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `user_id` int(11) NOT NULL,
                  `cat_name` varchar(50) NOT NULL,
                  `dog_name` varchar(50) NOT NULL
               ) ENGINE=InnoDB;'
           ],
];
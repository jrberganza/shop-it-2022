CREATE TABLE `users` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(100) UNIQUE NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` ENUM ('user', 'employee', 'admin') NOT NULL,
  `shop_id` int,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `shops` (
  `shop_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `shop_photos` (
  `shop_photo_id` int PRIMARY KEY AUTO_INCREMENT,
  `shop_id` int NOT NULL,
  `photo` blob NOT NULL
);

CREATE TABLE `products` (
  `product_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `shop_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `product_photos` (
  `product_photo_id` int PRIMARY KEY AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `photo` blob NOT NULL
);

CREATE TABLE `shop_categories` (
  `shop_cat_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `shop_cat_relation` (
  `shop_cat_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`shop_cat_id`, `shop_id`)
);

CREATE TABLE `product_categories` (
  `product_cat_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `prod_cat_relation` (
  `product_cat_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`product_cat_id`, `product_id`)
);

CREATE TABLE `comments` (
  `comment_id` int PRIMARY KEY AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `content` varchar(512) NOT NULL,
  `parent_comment_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL
);

CREATE TABLE `shop_ratings` (
  `shop_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  PRIMARY KEY (`shop_id`, `user_id`)
);

CREATE TABLE `product_ratings` (
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  PRIMARY KEY (`product_id`, `user_id`)
);

CREATE TABLE `comment_votes` (
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `value` int NOT NULL,
  PRIMARY KEY (`comment_id`, `user_id`)
);

CREATE TABLE `sessions` (
  `session_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) UNIQUE NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `last_access_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `forgot_password_tokens` (
  `token_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
);

ALTER TABLE `shops` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `users` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `shop_photos` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `products` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `product_photos` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `shop_cat_relation` ADD FOREIGN KEY (`shop_cat_id`) REFERENCES `shop_categories` (`shop_cat_id`);

ALTER TABLE `shop_cat_relation` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `prod_cat_relation` ADD FOREIGN KEY (`product_cat_id`) REFERENCES `product_categories` (`product_cat_id`);

ALTER TABLE `prod_cat_relation` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`);

ALTER TABLE `shop_ratings` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `shop_ratings` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `product_ratings` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `product_ratings` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `comment_votes` ADD FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`);

ALTER TABLE `comment_votes` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `sessions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `forgot_password_tokens` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);


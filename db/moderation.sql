CREATE TABLE `moderation_events` (
  `moderation_event_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_type` ENUM ('shop', 'product', 'comment') NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` varchar(512) NOT NULL,
  `item_created_at` datetime NOT NULL,
  `item_updated_at` datetime DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `published` bool NOT NULL,
  `date` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `$moderation$shops` (
  `shop_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `zone` int NOT NULL,
  `municipality_id` int NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `user_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `$moderation$products` (
  `product_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `shop_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);

CREATE TABLE `$moderation$shop_photo` (
  `photo_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `shop_id`)
);

CREATE TABLE `$moderation$product_photo` (
  `photo_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `product_id`)
);

CREATE TABLE `$moderation$shop_category` (
  `category_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `shop_id`)
);

CREATE TABLE `$moderation$product_category` (
  `category_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `product_id`)
);

CREATE TABLE `$moderation$comments` (
  `comment_id` int PRIMARY KEY AUTO_INCREMENT,
  `shop_id` int,
  `product_id` int,
  `author_id` int NOT NULL,
  `content` varchar(512) NOT NULL,
  `parent_comment_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
);

ALTER TABLE `$moderation$shops` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `$moderation$shops` ADD FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`municipality_id`);

ALTER TABLE `$moderation$products` ADD FOREIGN KEY (`shop_id`) REFERENCES `$moderation$shops` (`shop_id`);

ALTER TABLE `$moderation$shop_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);

ALTER TABLE `$moderation$shop_photo` ADD FOREIGN KEY (`shop_id`) REFERENCES `$moderation$shops` (`shop_id`);

ALTER TABLE `$moderation$product_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);

ALTER TABLE `$moderation$product_photo` ADD FOREIGN KEY (`product_id`) REFERENCES `$moderation$products` (`product_id`);

ALTER TABLE `$moderation$shop_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

ALTER TABLE `$moderation$shop_category` ADD FOREIGN KEY (`shop_id`) REFERENCES `$moderation$shops` (`shop_id`);

ALTER TABLE `$moderation$product_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

ALTER TABLE `$moderation$product_category` ADD FOREIGN KEY (`product_id`) REFERENCES `$moderation$products` (`product_id`);

ALTER TABLE `$moderation$comments` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);

ALTER TABLE `$moderation$comments` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `$moderation$comments` ADD FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `$moderation$comments` ADD FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`);
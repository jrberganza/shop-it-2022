<?php

require_once './request.php';

$stmt = $req->prepareQuery("SHOW TABLES LIKE 'homepage'", []);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->fetch_row()) {
    $req->prepareQuery("CREATE TABLE `users` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(100) UNIQUE NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` ENUM ('user', 'employee', 'admin') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `departments` (
  `department_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(128) NOT NULL
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `municipalities` (
  `municipality_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `department_id` int NOT NULL
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `shops` (
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
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `products` (
  `product_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `shop_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `photos` (
  `photo_id` int PRIMARY KEY AUTO_INCREMENT,
  `photo` blob NOT NULL
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `shop_photo` (
  `photo_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `shop_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `product_photo` (
  `photo_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `product_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `categories` (
  `category_id` int PRIMARY KEY AUTO_INCREMENT,
  `type` ENUM ('shop', 'product') NOT NULL,
  `name` varchar(50) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `shop_category` (
  `category_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `shop_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `product_category` (
  `category_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `product_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `comments` (
  `comment_id` int PRIMARY KEY AUTO_INCREMENT,
  `shop_id` int,
  `product_id` int,
  `author_id` int NOT NULL,
  `content` varchar(512) NOT NULL,
  `parent_comment_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `shop_ratings` (
  `shop_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  PRIMARY KEY (`shop_id`, `user_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `product_ratings` (
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  PRIMARY KEY (`product_id`, `user_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `comment_votes` (
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `value` int NOT NULL,
  PRIMARY KEY (`comment_id`, `user_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `sessions` (
  `session_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) UNIQUE NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `last_access_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `forgot_password_tokens` (
  `token_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `homepage_blocks` (
  `block_id` int PRIMARY KEY AUTO_INCREMENT,
  `type` ENUM ('feed', 'banner') NOT NULL,
  `position` int NOT NULL,
  `size` ENUM ('full', 'half', 'third', 'fourth', 'twelfth') NOT NULL DEFAULT \"full\"
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `feed_blocks` (
  `feed_block_id` int PRIMARY KEY AUTO_INCREMENT,
  `block_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` ENUM ('auto_top_rated', 'auto_trending', 'auto_recent', 'manual') NOT NULL,
  `item_type` ENUM ('shop', 'product') NOT NULL,
  `max_size` int NOT NULL
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `feed_block_items` (
  `feed_block_item_id` int PRIMARY KEY AUTO_INCREMENT,
  `feed_block_id` int NOT NULL,
  `shop_id` int,
  `product_id` int
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `banner_blocks` (
  `banner_block_id` int PRIMARY KEY AUTO_INCREMENT,
  `block_id` int NOT NULL,
  `title` varchar(255),
  `text` varchar(512),
  `photo_id` int
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `moderation_events` (
  `moderation_event_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_owner_id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `item_type` ENUM ('shop', 'product', 'comment') NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` varchar(512) NOT NULL,
  `item_created_at` datetime NOT NULL,
  `item_updated_at` datetime DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `published` bool NOT NULL,
  `date` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("ALTER TABLE `municipalities` ADD FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shops` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shops` ADD FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`municipality_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `products` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_photo` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_photo` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_category` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_category` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comments` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comments` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comments` ADD FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comments` ADD FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_ratings` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `shop_ratings` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_ratings` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `product_ratings` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comment_votes` ADD FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `comment_votes` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `sessions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `forgot_password_tokens` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `feed_blocks` ADD FOREIGN KEY (`block_id`) REFERENCES `homepage_blocks` (`block_id`) ON DELETE CASCADE;", [])->execute();

    $req->prepareQuery("ALTER TABLE `feed_block_items` ADD FOREIGN KEY (`feed_block_id`) REFERENCES `feed_blocks` (`feed_block_id`) ON DELETE CASCADE;", [])->execute();

    $req->prepareQuery("ALTER TABLE `feed_block_items` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `feed_block_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `banner_blocks` ADD FOREIGN KEY (`block_id`) REFERENCES `homepage_blocks` (`block_id`) ON DELETE CASCADE;", [])->execute();

    $req->prepareQuery("ALTER TABLE `banner_blocks` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `moderation_events` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `moderation_events` ADD FOREIGN KEY (`item_owner_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$shops` (
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
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$products` (
  `product_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `description` varchar(512) NOT NULL,
  `disabled` bool NOT NULL DEFAULT TRUE,
  `shop_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT (now()),
  `updated_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$shop_photo` (
  `photo_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `shop_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$product_photo` (
  `photo_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`photo_id`, `product_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$shop_category` (
  `category_id` int NOT NULL,
  `shop_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `shop_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$product_category` (
  `category_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`category_id`, `product_id`)
);", [])->execute();

    $req->prepareQuery("CREATE TABLE `\$moderation\$comments` (
  `comment_id` int PRIMARY KEY AUTO_INCREMENT,
  `shop_id` int,
  `product_id` int,
  `author_id` int NOT NULL,
  `content` varchar(512) NOT NULL,
  `parent_comment_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT (now())
);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shops` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shops` ADD FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`municipality_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$products` ADD FOREIGN KEY (`shop_id`) REFERENCES `\$moderation\$shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shop_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shop_photo` ADD FOREIGN KEY (`shop_id`) REFERENCES `\$moderation\$shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$product_photo` ADD FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$product_photo` ADD FOREIGN KEY (`product_id`) REFERENCES `\$moderation\$products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shop_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$shop_category` ADD FOREIGN KEY (`shop_id`) REFERENCES `\$moderation\$shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$product_category` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$product_category` ADD FOREIGN KEY (`product_id`) REFERENCES `\$moderation\$products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$comments` ADD FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$comments` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$comments` ADD FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);", [])->execute();

    $req->prepareQuery("ALTER TABLE `\$moderation\$comments` ADD FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`);", [])->execute();

    $req->prepareQuery("CREATE VIEW trending_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
WHERE
    s.disabled = FALSE
ORDER BY
    (comments * average_rating) DESC;", [])->execute();

    $req->prepareQuery("CREATE VIEW top_rated_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
WHERE
    s.disabled = FALSE
ORDER BY
    average_rating DESC;", [])->execute();

    $req->prepareQuery("CREATE VIEW recent_shops AS SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
LEFT JOIN
    (SELECT count(*) as comments, shop_id FROM comments GROUP BY shop_id) c USING (shop_id)
WHERE
    s.disabled = FALSE
ORDER BY
    s.updated_at DESC;", [])->execute();

    $req->prepareQuery("CREATE VIEW trending_products AS SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    s.name as shopName,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
WHERE
    p.disabled = FALSE
ORDER BY
    (comments * average_rating) DESC;", [])->execute();

    $req->prepareQuery("CREATE VIEW top_rated_products AS SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    s.name as shopName,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
WHERE
    p.disabled = FALSE
ORDER BY
    average_rating DESC;", [])->execute();

    $req->prepareQuery("CREATE VIEW recent_products AS SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    s.name as shopName,
    COALESCE(c.comments, 0) as comments,
    COALESCE(r.average_rating, 0) as average_rating
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
LEFT JOIN
    (SELECT count(*) as comments, product_id FROM comments GROUP BY product_id) c USING (product_id)
WHERE
    p.disabled = FALSE
ORDER BY
    p.updated_at DESC;", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (1, \"Alta Verapaz\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (1, \"Cobán\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (2, \"San Pedro Carchá\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (3, \"San Juan Chamelco\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (4, \"San Cristóbal Verapaz\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (5, \"Tactic\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (6, \"Tucuru\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (7, \"Tamahú\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (8, \"Panzós\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (9, \"Senahú\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (10, \"Cahabón\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (11, \"Lanquín\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (12, \"Chahal\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (13, \"Fray Bartolomé de las Casas\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (14, \"Chisec\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (15, \"Santa Cruz Verapaz\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (16, \"Santa Catalina La Tinta\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (17, \"Raxruhá\", 1);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (2, \"Baja Verapaz\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (18, \"Cubulco\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (19, \"Santa Cruz el Chol\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (20, \"Granados\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (21, \"Purulhá\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (22, \"Rabinal\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (23, \"Salamá\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (24, \"San Miguel Chicaj\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (25, \"San Jerónimo\", 2);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (3, \"Chimaltenango\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (26, \"Chimaltenango\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (27, \"San José Poaquíl\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (28, \"San Martín Jilotepeque\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (29, \"San Juan Comalapa\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (30, \"Santa Apolonia\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (31, \"Tecpán Guatemala\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (32, \"Patzún\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (33, \"Pochuta\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (34, \"Patzicía\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (35, \"Santa Cruz Balanyá\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (36, \"Acatenango\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (37, \"San Pedro Yepocapa\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (38, \"San Andrés Itzapa\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (39, \"Parramos\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (40, \"Zaragoza\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (41, \"El Tejar\", 3);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (4, \"Chiquimula\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (42, \"Chiquimula\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (43, \"Jocotán\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (44, \"Esquipulas\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (45, \"Camotán\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (46, \"Quezaltepeque\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (47, \"Olopa\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (48, \"Ipala\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (49, \"San Juan Ermita\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (50, \"Concepción Las Minas\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (51, \"San Jacinto\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (52, \"San José la Arada\", 4);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (5, \"El Progreso\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (53, \"El Jícaro\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (54, \"Guastatoya\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (55, \"Morazán\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (56, \"Sanarate\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (57, \"Sansare\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (58, \"San Agustín Acasaguastlán\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (59, \"San Antonio La Paz\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (60, \"San Cristóbal Acasaguastlán\", 5);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (6, \"Escuintla\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (61, \"Escuintla\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (62, \"Guanagazapa\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (63, \"Iztapa\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (64, \"La Democracia\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (65, \"La Gomera\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (66, \"Masagua\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (67, \"Nueva Concepción\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (68, \"Palín\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (69, \"San José\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (70, \"San Vicente Pacaya\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (71, \"Santa Lucía Cotzumalguapa\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (72, \"Sipacate\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (73, \"Siquinalá\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (74, \"Tiquisate\", 6);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (7, \"Guatemala\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (75, \"Guatemala\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (76, \"Villa Nueva\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (77, \"Mixco\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (78, \"Santa Catarina Pinula\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (79, \"San José Pinula\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (80, \"San José del Golfo\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (81, \"Palencia\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (82, \"Chinautla\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (83, \"San Pedro Ayampuc\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (84, \"San Pedro Sacatepéquez\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (85, \"San Juan Sacatepéquez\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (86, \"San Raymundo\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (87, \"Chuarrancho\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (88, \"Fraijanes\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (89, \"Amatitlán\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (90, \"Villa Canales\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (91, \"San Miguel Petapa\", 7);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (8, \"Huehuetenango\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (92, \"Aguacatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (93, \"Chiantla\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (94, \"Colotenango\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (95, \"Concepción Huista\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (96, \"Cuilco\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (97, \"Huehuetenango\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (98, \"Jacaltenango\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (99, \"La Democracia\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (100, \"La Libertad\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (101, \"Malacatancito\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (102, \"Nentón\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (103, \"Petatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (104, \"San Antonio Huista\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (105, \"San Gaspar Ixchil\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (106, \"Ixtahuacán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (107, \"San Juan Atitán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (108, \"San Juan Ixcoy\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (109, \"San Mateo Ixtatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (110, \"San Miguel Acatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (111, \"San Pedro Necta\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (112, \"San Pedro Soloma\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (113, \"San Rafael La Independencia\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (114, \"San Rafael Petzal\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (115, \"San Sebastián Coatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (116, \"San Sebastián Huehuetenango\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (117, \"Santa Ana Huista.\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (118, \"Santa Bárbara\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (119, \"Barillas\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (120, \"Santa Eulalia\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (121, \"Santiago Chimaltenango\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (122, \"Tectitán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (123, \"Todos Santos Cuchumatán\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (124, \"Unión Cantinil\", 8);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (9, \"Izabal\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (125, \"Puerto Barrios\", 9);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (126, \"Livingston\", 9);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (127, \"El Estor\", 9);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (128, \"Morales\", 9);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (129, \"Los Amates\", 9);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (10, \"Jalapa\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (130, \"Jalapa\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (131, \"Mataquescuintla\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (132, \"Monjas\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (133, \"San Carlos Alzatate\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (134, \"San Luis Jilotepeque\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (135, \"San Pedro Pinula\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (136, \"San Manuel Chaparrón\", 10);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (11, \"Jutiapa\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (137, \"Agua Blanca\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (138, \"Asunción Mita\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (139, \"Atescatempa\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (140, \"Comapa\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (141, \"Conguaco\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (142, \"El Adelanto\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (143, \"El Progreso\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (144, \"Jalpatagua\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (145, \"Jerez\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (146, \"Jutiapa\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (147, \"Moyuta\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (148, \"Pasaco\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (149, \"Quesada\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (150, \"San José Acatempa\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (151, \"Santa Catarina Mita\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (152, \"Yupiltepeque\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (153, \"Zapotitlán\", 11);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (12, \"Petén\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (154, \"Dolores\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (155, \"Flores\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (156, \"La Libertad\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (157, \"Melchor de Mencos\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (158, \"Poptún\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (159, \"San Andrés\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (160, \"San Benito\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (161, \"San Francisco\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (162, \"San José\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (163, \"San Luis\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (164, \"Santa Ana\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (165, \"Sayaxché\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (166, \"Las Cruces\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (167, \"El Chal\", 12);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (13, \"Quetzaltenango\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (168, \"Almolonga\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (169, \"Cabricán\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (170, \"Cajolá\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (171, \"Cantel\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (172, \"Coatepeque\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (173, \"Colomba\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (174, \"Concepción Chiquirichapa\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (175, \"El Palmar\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (176, \"Flores Costa Cuca\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (177, \"Génova\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (178, \"Huitán\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (179, \"La Esperanza\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (180, \"Olintepeque\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (181, \"Palestina de Los Altos\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (182, \"Quetzaltenango\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (183, \"Salcajá\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (184, \"San Carlos Sija\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (185, \"San Juan Ostuncalco\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (186, \"San Francisco La Unión\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (187, \"San Martín Sacatepéquez\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (188, \"San Mateo\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (189, \"San Miguel Sigüilá\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (190, \"Sibilia\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (191, \"Zunil\", 13);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (14, \"Quiché\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (192, \"Santa Cruz del Quiché\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (193, \"Canillá\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (194, \"Chajul\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (195, \"Chicamán\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (196, \"Chiché\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (197, \"Chichicastenango\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (198, \"Chinique\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (199, \"Cunén\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (200, \"Ixcán\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (201, \"Joyabaj\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (202, \"Pachalum\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (203, \"Patzité\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (204, \"Sacapulas\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (205, \"San Andrés Sajcabajá\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (206, \"San Antonio Ilotenango\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (207, \"San Bartolomé Jocotenango\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (208, \"San Juan Cotzal\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (209, \"San Pedro Jocopilas\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (210, \"Santa María Nebaj\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (211, \"Uspantán\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (212, \"Zacualpa\", 14);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (15, \"Retalhuleu\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (213, \"Retalhuleu\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (214, \"San Sebastián\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (215, \"Santa Cruz Muluá\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (216, \"San Martín Zapotitlán\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (217, \"San Felipe\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (218, \"San Andrés Villa Seca\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (219, \"Champerico\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (220, \"Nuevo San Carlos\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (221, \"El Asintal\", 15);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (16, \"Sacatepéquez\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (222, \"Alotenango\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (223, \"Antigua Guatemala\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (224, \"Ciudad Vieja\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (225, \"Jocotenango\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (226, \"Magdalena Milpas Altas\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (227, \"Pastores\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (228, \"San Antonio Aguas Calientes\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (229, \"San Bartolomé Milpas Altas\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (230, \"San Lucas Sacatepéquez\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (231, \"San Miguel Dueñas\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (232, \"Santa Catarina Barahona\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (233, \"Santa Lucía Milpas Altas\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (234, \"Santa María de Jesús\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (235, \"Santiago Sacatepéquez\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (236, \"Santo Domingo Xenacoj\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (237, \"Sumpango\", 16);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (17, \"San Marcos\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (238, \"San Marcos\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (239, \"Ayutla\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (240, \"Catarina\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (241, \"Comitancillo\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (242, \"Concepción Tutuapa\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (243, \"El Quetzal\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (244, \"El Rodeo\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (245, \"El Tumbador\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (246, \"Ixchiguán\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (247, \"La Reforma\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (248, \"Malacatán\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (249, \"Nuevo Progreso\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (250, \"Ocós\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (251, \"Pajapita\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (252, \"Esquipulas Palo Gordo\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (253, \"San Antonio Sacatepéquez\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (254, \"San Cristóbal Cucho\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (255, \"San José Ojetenam\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (256, \"San Lorenzo\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (257, \"San Miguel Ixtahuacán\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (258, \"San Pablo\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (259, \"San Pedro Sacatepéquez\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (260, \"San Rafael Pie de la Cuesta\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (261, \"Sibinal\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (262, \"Sipacapa\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (263, \"Tacaná\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (264, \"Tajumulco\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (265, \"Tejutla\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (266, \"Río Blanco\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (267, \"La Blanca\", 17);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (18, \"Santa Rosa\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (268, \"Barberena\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (269, \"Casillas\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (270, \"Chiquimulilla\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (271, \"Cuilapa\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (272, \"Guazacapán\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (273, \"Nueva Santa Rosa\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (274, \"Oratorio\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (275, \"Pueblo Nuevo Viñas\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (276, \"San Juan Tecuaco\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (277, \"San Rafael Las Flores\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (278, \"Santa Cruz Naranjo\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (279, \"Santa María Ixhuatán\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (280, \"Santa Rosa de Lima\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (281, \"Taxisco\", 18);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (19, \"Sololá\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (282, \"Sololá\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (283, \"Concepción\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (284, \"Nahualá\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (285, \"Panajachel\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (286, \"San Andrés Semetabaj\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (287, \"San Antonio Palopó\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (288, \"San José Chacayá\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (289, \"San Juan La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (290, \"San Lucas Tolimán\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (291, \"San Marcos La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (292, \"San Pablo La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (293, \"San Pedro La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (294, \"Santa Catarina Ixtahuacán\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (295, \"Santa Catarina Palopó\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (296, \"Santa Clara La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (297, \"Santa Cruz La Laguna\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (298, \"Santa Lucía Utatlán\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (299, \"Santa María Visitación\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (300, \"Santiago Atitlán\", 19);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (20, \"Suchitepéquez\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (301, \"Chicacao\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (302, \"Cuyotenango\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (303, \"Mazatenango\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (304, \"Patulul\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (305, \"Pueblo Nuevo\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (306, \"Río Bravo\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (307, \"Samayac\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (308, \"San Antonio Suchitepéquez\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (309, \"San Bernardino\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (310, \"San Francisco Zapotitlán\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (311, \"San Gabriel\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (312, \"San José El Ídolo\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (313, \"San José La Máquina\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (314, \"San Juan Bautista\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (315, \"San Lorenzo\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (316, \"San Miguel Panán\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (317, \"San Pablo Jocopilas\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (318, \"Santa Bárbara\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (319, \"Santo Domingo Suchitepéquez\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (320, \"Santo Tomás La Unión\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (321, \"Zunilito\", 20);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (21, \"Totonicapán\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (322, \"Momostenango\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (323, \"San Andrés Xecul\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (324, \"San Bartolo\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (325, \"San Cristóbal Totonicapán\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (326, \"San Francisco El Alto\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (327, \"Santa Lucía La Reforma\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (328, \"Santa María Chiquimula\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (329, \"Totonicapán\", 21);", [])->execute();

    $req->prepareQuery("INSERT INTO departments(department_id, name) VALUES (22, \"Zacapa\");", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (330, \"Cabañas\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (331, \"Estanzuela\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (332, \"Gualán\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (333, \"Huité\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (334, \"La Unión\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (335, \"Río Hondo\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (336, \"San Diego\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (337, \"San Jorge\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (338, \"Teculután\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (339, \"Usumatlán\", 22);", [])->execute();

    $req->prepareQuery("INSERT INTO municipalities(municipality_id, name, department_id) VALUES (340, \"Zacapa\", 22);", [])->execute();

    $pHash = password_hash("12345", PASSWORD_BCRYPT);
    $req->prepareQuery("INSERT INTO users(user_id, email, display_name, password_hash, role) VALUES (1, @{s:email}, @{s:displayName}, @{s:passwordHash}, @{s:role})", [
        "email" => "admin@example.com",
        "displayName" => "Admin",
        "passwordHash" => $pHash,
        "role" => "admin"
    ])->execute();
}

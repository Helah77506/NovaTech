-- =========================================
-- EXPANSION FOR NOVATECH DATABASE
-- Keeps all existing column names unchanged
-- =========================================

-- --------------------------------------------------------
-- 1️⃣ USERS TABLE - Add role column
-- --------------------------------------------------------

ALTER TABLE `users`
ADD COLUMN `Role` ENUM('admin','customer') NOT NULL DEFAULT 'customer' AFTER `Password_Hash`;



-- --------------------------------------------------------
-- 2️⃣ PRODUCT TABLE - Add image column
-- --------------------------------------------------------

ALTER TABLE `product`
ADD COLUMN `Image` VARCHAR(255) DEFAULT NULL AFTER `Stock`;



-- --------------------------------------------------------
-- 3️⃣ ORDERS TABLE - Add missing ecommerce fields
-- --------------------------------------------------------

ALTER TABLE `orders`
ADD COLUMN `Status` ENUM('Pending','Processing','Shipped','Completed') NOT NULL DEFAULT 'Pending' AFTER `Total`,
ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `Status`;



-- --------------------------------------------------------
-- 4️⃣ CREATE ORDER_ITEMS TABLE
-- --------------------------------------------------------

CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order`
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`ID`)
      ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product`
      FOREIGN KEY (`product_id`) REFERENCES `product`(`ID`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------
-- 5️⃣ CREATE REVIEWS TABLE
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `rating` INT(1) NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_reviews_user` (`user_id`),
  KEY `fk_reviews_product` (`product_id`),
  CONSTRAINT `fk_reviews_user`
      FOREIGN KEY (`user_id`) REFERENCES `users`(`ID`)
      ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_product`
      FOREIGN KEY (`product_id`) REFERENCES `product`(`ID`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------
-- 6️⃣ CREATE RETURNS TABLE
-- --------------------------------------------------------

CREATE TABLE `returns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('Requested','Approved','Rejected') NOT NULL DEFAULT 'Requested',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_returns_order` (`order_id`),
  CONSTRAINT `fk_returns_order`
      FOREIGN KEY (`order_id`) REFERENCES `orders`(`ID`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- --------------------------------------------------------
-- 7️⃣ ADD FOREIGN KEY FROM ORDERS TO USERS
-- --------------------------------------------------------

ALTER TABLE `orders`
ADD CONSTRAINT `fk_orders_user`
FOREIGN KEY (`User_ID`) REFERENCES `users`(`ID`)
ON DELETE CASCADE;

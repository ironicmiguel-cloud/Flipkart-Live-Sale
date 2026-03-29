CREATE TABLE IF NOT EXISTS fk_users (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  name VARCHAR(190) NOT NULL,
  mobile VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  joined VARCHAR(32) NOT NULL,
  gender VARCHAR(32) NOT NULL DEFAULT '',
  dob VARCHAR(32) NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fk_state (
  owner_type VARCHAR(10) NOT NULL,
  owner_key VARCHAR(191) NOT NULL,
  cart_json LONGTEXT NOT NULL,
  wishlist_json LONGTEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (owner_type, owner_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fk_orders (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  checkout_token VARCHAR(64) NOT NULL UNIQUE,
  owner_email VARCHAR(190) NOT NULL DEFAULT '',
  guest_session VARCHAR(128) NOT NULL DEFAULT '',
  created_at VARCHAR(40) NOT NULL,
  order_json LONGTEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_owner_email (owner_email),
  INDEX idx_guest_session (guest_session)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fk_products` (
    `id`          VARCHAR(10)      NOT NULL,
    `num`         SMALLINT UNSIGNED NOT NULL,
    `brand`       VARCHAR(255)     NOT NULL DEFAULT '',
    `name`        VARCHAR(500)     NOT NULL DEFAULT '',
    `price`       DECIMAL(10,2)    NOT NULL DEFAULT 0,
    `mrp`         DECIMAL(10,2)    NOT NULL DEFAULT 0,
    `off`         TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `badge`       VARCHAR(50)      NOT NULL DEFAULT '',
    `stock`       SMALLINT         NOT NULL DEFAULT 100,
    `rating`      DECIMAL(3,2)     NOT NULL DEFAULT 4.00,
    `rcount`      INT UNSIGNED     NOT NULL DEFAULT 0,
    `category`    VARCHAR(100)     NOT NULL DEFAULT '',
    `subcategory` VARCHAR(100)     NOT NULL DEFAULT '',
    `description` TEXT,
    `images`      JSON,
    `model`       VARCHAR(255)     NOT NULL DEFAULT '',
    `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_num` (`num`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

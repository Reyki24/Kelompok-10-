CREATE DATABASE IF NOT EXISTS recyclehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recyclehub;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS balance_transactions, withdrawals, withdrawal_requests, deposits, waste_types, password_reset_tokens, sessions, users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(150) NOT NULL,
 username VARCHAR(50) NOT NULL,
 email VARCHAR(150) NOT NULL,
 phone VARCHAR(20) NULL,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','nasabah') NOT NULL DEFAULT 'nasabah',
 balance DECIMAL(15,2) NOT NULL DEFAULT 0.00,
 profile_photo VARCHAR(255) NULL,
 status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
 email_verified_at TIMESTAMP NULL,
 remember_token VARCHAR(100) NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 deleted_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 UNIQUE KEY users_username_unique(username),
 UNIQUE KEY users_email_unique(email),
 KEY users_role_status_index(role,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE waste_types (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(100) NOT NULL,
 price_per_kg DECIMAL(12,2) NOT NULL,
 description TEXT NULL,
 icon VARCHAR(255) NULL,
 status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 deleted_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 UNIQUE KEY waste_types_name_unique(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE deposits (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 transaction_code VARCHAR(30) NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 waste_type_id BIGINT UNSIGNED NOT NULL,
 transaction_date DATE NOT NULL,
 weight DECIMAL(10,2) NOT NULL,
 price_per_kg DECIMAL(12,2) NOT NULL,
 total_amount DECIMAL(15,2) NOT NULL,
 notes TEXT NULL,
 processed_by BIGINT UNSIGNED NULL,
 status ENUM('berhasil','dibatalkan') NOT NULL DEFAULT 'berhasil',
 cancellation_reason TEXT NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 UNIQUE KEY deposits_transaction_code_unique(transaction_code),
 KEY deposits_user_id_index(user_id),
 CONSTRAINT deposits_user_fk FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT deposits_waste_fk FOREIGN KEY(waste_type_id) REFERENCES waste_types(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT deposits_processor_fk FOREIGN KEY(processed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE withdrawal_requests (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 request_code VARCHAR(30) NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 amount DECIMAL(15,2) NOT NULL,
 request_date DATETIME NOT NULL,
 status ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
 notes TEXT NULL,
 rejection_reason TEXT NULL,
 processed_by BIGINT UNSIGNED NULL,
 processed_at DATETIME NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 UNIQUE KEY withdrawal_requests_code_unique(request_code),
 CONSTRAINT withdrawal_requests_user_fk FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT withdrawal_requests_processor_fk FOREIGN KEY(processed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE withdrawals (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 transaction_code VARCHAR(30) NOT NULL,
 withdrawal_request_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NOT NULL,
 amount DECIMAL(15,2) NOT NULL,
 transaction_date DATETIME NOT NULL,
 processed_by BIGINT UNSIGNED NOT NULL,
 notes TEXT NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 UNIQUE KEY withdrawals_code_unique(transaction_code),
 UNIQUE KEY withdrawals_request_unique(withdrawal_request_id),
 CONSTRAINT withdrawals_request_fk FOREIGN KEY(withdrawal_request_id) REFERENCES withdrawal_requests(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT withdrawals_user_fk FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT withdrawals_processor_fk FOREIGN KEY(processed_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE balance_transactions (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 user_id BIGINT UNSIGNED NOT NULL,
 type ENUM('deposit','withdrawal','adjustment') NOT NULL,
 reference_id BIGINT UNSIGNED NULL,
 reference_type VARCHAR(50) NULL,
 amount DECIMAL(15,2) NOT NULL,
 balance_before DECIMAL(15,2) NOT NULL,
 balance_after DECIMAL(15,2) NOT NULL,
 description TEXT NOT NULL,
 created_by BIGINT UNSIGNED NULL,
 created_at TIMESTAMP NULL,
 updated_at TIMESTAMP NULL,
 PRIMARY KEY(id),
 KEY balance_user_type_index(user_id,type),
 CONSTRAINT balance_user_fk FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
 CONSTRAINT balance_creator_fk FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users(name,username,email,phone,password,role,balance,status,created_at,updated_at) VALUES
('Administrator RecycleHub','admin','admin@recyclehub.test','081000000001','$2y$12$UvWD4v0wCg9sgwQpvPEYgef2BaOBz0erbMh8bLobRBiBJIYuApgFu','admin',0,'aktif',NOW(),NOW()),
('Siti Rahma','sitirahma','sitirahma@example.test','081234567891','$2y$12$nu0ZwfWmFOkn/3UIgpDJLue6xNqSZ4zDDMWf2xPnU6003ZSp6u9.e','nasabah',45000,'aktif',NOW(),NOW()),
('Budi Santoso','budisantoso','budi@example.test','081234567892','$2y$12$nu0ZwfWmFOkn/3UIgpDJLue6xNqSZ4zDDMWf2xPnU6003ZSp6u9.e','nasabah',25000,'aktif',NOW(),NOW()),
('Andi Pratama','andipratama','andi@example.test','081234567893','$2y$12$nu0ZwfWmFOkn/3UIgpDJLue6xNqSZ4zDDMWf2xPnU6003ZSp6u9.e','nasabah',12000,'aktif',NOW(),NOW());

INSERT INTO waste_types(name,price_per_kg,description,status,created_at,updated_at) VALUES
('Botol Plastik',3000,'Botol plastik PET bersih dan kering','aktif',NOW(),NOW()),
('Kardus',2000,'Kardus bekas yang masih utuh','aktif',NOW(),NOW()),
('Kertas',2500,'Kertas bekas dan koran','aktif',NOW(),NOW()),
('Kaleng',5000,'Kaleng aluminium dan besi ringan','aktif',NOW(),NOW()),
('Plastik Campuran',1500,'Plastik campuran yang sudah dipilah','aktif',NOW(),NOW());

INSERT INTO deposits(transaction_code,user_id,waste_type_id,transaction_date,weight,price_per_kg,total_amount,notes,processed_by,status,created_at,updated_at)
VALUES('DEP-DEMO-001',2,1,CURDATE(),5,3000,15000,'Setoran contoh',1,'berhasil',NOW(),NOW());

create database alojamientos;
use alojamientos;
-- Tabla de Usuarios
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Almacena el hash de la contraseña
    email VARCHAR(100) NOT NULL UNIQUE,
    user_role ENUM('user', 'admin') DEFAULT 'user' NOT NULL
);

-- Tabla de Alojamientos
CREATE TABLE accommodations (
    accommodation_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);

-- Tabla de Relación (Alojamientos seleccionados por el usuario)
CREATE TABLE user_accommodations (
    selection_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    accommodation_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (accommodation_id) REFERENCES accommodations(accommodation_id) ON DELETE CASCADE,
    UNIQUE KEY user_accommodation_unique (user_id, accommodation_id)
);

-- Usuario Administrador (IMPORTANTE: la clave del admin es: 12345678)
INSERT INTO users (username, password_hash, email, user_role) VALUES
('admin', '$2y$10$Aj5JR411kLwccshAV8JsaeiR/VWFbzpJNZUkAuhA3Rh9KYwBwnIOO', 'admin@app.com', 'admin');
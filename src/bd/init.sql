-- Crear base de datos si no existe (sin usar variables de entorno)
CREATE DATABASE IF NOT EXISTS pandora_prueba_tecnica;

-- Usar la base de datos
USE pandora_prueba_tecnica;

CREATE TABLE patient (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  dni VARCHAR(9) UNIQUE NOT NULL,
  telephone VARCHAR(15) NOT NULL,
  email VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE appointment_type (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type_name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO appointment_type (type_name) VALUES ('Primera Consulta');
INSERT INTO appointment_type (type_name) VALUES ('Revisión');


CREATE TABLE appointment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  appointment_date DATETIME NOT NULL,
  appointment_type_id INT NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patient(id) ON DELETE CASCADE,
  FOREIGN KEY (appointment_type_id) REFERENCES appointment_type(id)
);

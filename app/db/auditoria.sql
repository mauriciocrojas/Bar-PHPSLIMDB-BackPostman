CREATE TABLE auditoria (
    idauditoria INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(255) NOT NULL,
    usuario VARCHAR(255) NOT NULL,
    tipousuario VARCHAR(255) NOT NULL,
    fechaaccion DATETIME NOT NULL
);

ALTER TABLE `auditoria` ADD `entidad` VARCHAR(255);
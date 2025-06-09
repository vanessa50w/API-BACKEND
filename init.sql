CREATE DATABASE loja;
USE loja;

CREATE TABLE taxas_juros (
    id CHAR(36) PRIMARY KEY,
    data_inicio DATE NOT NULL,
    data_final DATE NOT NULL,
    taxa DECIMAL(5,2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE DATABASE IF NOT EXISTS av1_treinamento;
USE av1_treinamento;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('normal', 'adm') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    tipo ENUM('multipla', 'discursiva') NOT NULL,
    opcoes JSON NULL,
    resposta_correta VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS respostas_questionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_usuario VARCHAR(100) NOT NULL,
    data DATETIME NOT NULL,
    respostas JSON NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

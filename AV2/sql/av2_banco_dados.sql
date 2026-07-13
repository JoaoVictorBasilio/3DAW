-- Cria o banco de dados principal (se não existir) e o seleciona para uso
CREATE DATABASE IF NOT EXISTS almeidas_retreat;
USE almeidas_retreat;

-- Tabela para armazenar os dados de todos os usuários (clientes e administradores)
CREATE TABLE av2_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0
);

-- Tabela que guarda as características, preços e imagens dos quartos do hotel
CREATE TABLE av2_quartos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    banheiros INT,
    camas INT,
    pessoas INT,
    preco DECIMAL(10,2) NOT NULL,
    imagem_url VARCHAR(255)
);

-- Insere os 3 quartos padrões para o sistema não começar vazio
INSERT INTO av2_quartos (nome, banheiros, camas, pessoas, preco, imagem_url) VALUES
('Quarto Duplo Comum', 1, 2, 2, 100.00, 'img_quarto_comum.jpg'),
('Suite com Varanda', 1, 2, 2, 150.00, 'img_suite_varanda.jpg'),
('Suite Família com Varanda', 1, 2, 3, 150.00, 'img_suite_familia.jpg');

-- Tabela responsável por gerenciar as hospedagens
-- O status 'Manutencao' é usado pelo Admin para bloquear o quarto
CREATE TABLE av2_reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quarto_id INT,
    data_inicio DATE,
    data_fim DATE,
    cpf VARCHAR(14),
    status ENUM('Ativa', 'Cancelada', 'Manutencao') DEFAULT 'Ativa',
    FOREIGN KEY (user_id) REFERENCES av2_usuarios(id),
    FOREIGN KEY (quarto_id) REFERENCES av2_quartos(id)
);

-- Cria a conta principal do Administrador (Email: admin@gmail.com | Senha: password)
INSERT INTO av2_usuarios (nome, email, senha, is_admin) VALUES 
('Administrador', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Tabela para guardar as notas (estrelinhas) e os comentários deixados pelos hóspedes após a estadia
CREATE TABLE av2_avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quarto_id INT,
    nota INT CHECK(nota >= 1 AND nota <= 5),
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES av2_usuarios(id),
    FOREIGN KEY (quarto_id) REFERENCES av2_quartos(id)
);
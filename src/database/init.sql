-- Tabela de Viações
CREATE TABLE IF NOT EXISTS viacoes (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       nome VARCHAR(255) NOT NULL,
                                       url TEXT NOT NULL,
                                       cidade VARCHAR(255) NOT NULL,
                                       status ENUM('ativo', 'inativo') DEFAULT 'ativo',
                                       logo VARCHAR(255) DEFAULT NULL,
                                       criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                       alterado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nome VARCHAR(255) NOT NULL,
                                        email VARCHAR(255) NOT NULL UNIQUE,
                                        senha VARCHAR(255) NOT NULL,
                                        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Histórico (Preserva logs mesmo se a viação for removida)
CREATE TABLE IF NOT EXISTS viacoes_historico (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 viacao_id INT NULL,
                                                 usuario_id INT NULL,
                                                 acao VARCHAR(50),
                                                 detalhes TEXT,
                                                 data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                                 CONSTRAINT fk_viacao FOREIGN KEY (viacao_id) REFERENCES viacoes(id) ON DELETE SET NULL,
                                                 CONSTRAINT fk_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Inserir Usuário Administrador (E-mail: admin@admin.com | Senha: admin123)
-- O hash abaixo é o padrão Bcrypt reconhecido pelo password_verify do PHP
INSERT INTO usuarios (nome, email, senha)
VALUES ('Administrador', 'admin@admin.com', '$2y$12$Vzb0YtHtU3du8MzVrXw6SuWg.Fu/oOqQVU7zXE9i8CByO5ZHzON/G')
ON DUPLICATE KEY UPDATE email=email;
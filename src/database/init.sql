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


CREATE TABLE IF NOT EXISTS usuarios (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nome VARCHAR(255) NOT NULL,
                                        email VARCHAR(255) NOT NULL UNIQUE,
                                        senha VARCHAR(255) NOT NULL,
                                        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS viacoes_historico (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 viacao_id INT NULL,
                                                 usuario_id INT NULL,
                                                 acao VARCHAR(50),
                                                 detalhes TEXT,
                                                 data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Índices para otimizar consultas no banco de dados
                                                 INDEX idx_viacao (viacao_id),
                                                 INDEX idx_usuario (usuario_id)
);


-- Login: admin@admin.com / Senha: admin123
-- O hash abaixo é gerado via Bcrypt, compatível com a função password_verify do PHP
INSERT INTO usuarios (nome, email, senha)
VALUES ('Administrador', 'admin@admin.com', '$2y$12$Vzb0YtHtU3du8MzVrXw6SuWg.Fu/oOqQVU7zXE9i8CByO5ZHzON/G')
ON DUPLICATE KEY UPDATE email=email;

CREATE TABLE IF NOT EXISTS logs_acesso (
                                           id INT AUTO_INCREMENT PRIMARY KEY,
                                           email_tentativa VARCHAR(255) NOT NULL,
                                           usuario_id INT NULL,
                                           ip_origem VARCHAR(45) NOT NULL,
                                           user_agent TEXT,
                                           status ENUM('sucesso', 'falha', 'logout') NOT NULL,
                                           detalhes TEXT,
                                           data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                           INDEX idx_email (email_tentativa),
                                           INDEX idx_status (status)
);
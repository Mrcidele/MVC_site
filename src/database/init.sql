CREATE TABLE IF NOT EXISTS viacoes (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       nome VARCHAR(255) NOT NULL,
                                       url VARCHAR(255) NOT NULL,
                                       cidade VARCHAR(255) NOT NULL,
                                       status ENUM('ativo', 'inativo') DEFAULT 'ativo',
                                       logo VARCHAR(255) DEFAULT NULL,
                                       criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                       alterado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS viacoes_historico (
                                                 id INT AUTO_INCREMENT PRIMARY KEY,
                                                 viacao_id INT,
                                                 acao VARCHAR(50),
                                                 detalhes TEXT,
                                                 data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

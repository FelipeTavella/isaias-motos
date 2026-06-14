-- =========================================================
-- BANCO DE DADOS - SISTEMA ISAIAS MOTOS
-- MYSQL
-- =========================================================

CREATE DATABASE IF NOT EXISTS isaias_motos;

USE isaias_motos;

-- =========================================================
-- TABELA: USUARIOS
-- =========================================================

CREATE TABLE usuarios (
    codusuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),

    ultimo_login DATETIME NULL,
    conta_criada DATETIME DEFAULT CURRENT_TIMESTAMP,
    senha_expira_em DATE NULL,

    nivel_usuario ENUM(
        'ADMIN',
        'GERENTE',
        'MECANICO',
        'ATENDENTE'
    ) DEFAULT 'ATENDENTE'
);

-- =========================================================
-- TABELA: CLIENTES
-- =========================================================

CREATE TABLE clientes (
    coduser INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    cpf CHAR(11) UNIQUE,
    endereco VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado CHAR(2),
    cep VARCHAR(9),

    usuario_cadastro INT,

    CONSTRAINT fk_cliente_usuario
        FOREIGN KEY (usuario_cadastro)
        REFERENCES usuarios(codusuario)
);

-- =========================================================
-- TABELA: MOTOS
-- =========================================================

CREATE TABLE motos (
    codmoto INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL UNIQUE,
    modelo VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    ano INT,
    cor VARCHAR(50),
    descricao TEXT,
    coduser INT NOT NULL,
    CONSTRAINT fk_motos_clientes
        FOREIGN KEY (coduser)
        REFERENCES clientes(coduser)
);

-- =========================================================
-- TABELA: PECAS
-- =========================================================

CREATE TABLE pecas (
    codpeca INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    marca VARCHAR(100),
    preco_medio DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(100),
    estoque INT NOT NULL,
    localizacao VARCHAR(100),
    descricao TEXT
);

-- =========================================================
-- TABELA: SERVICOS
-- =========================================================
CREATE TABLE servicos (
    codservico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(150),
    duracao_min INT,
    preco DECIMAL(10,2) NOT NULL,
    dificuldade VARCHAR(50),
    tipo VARCHAR(50),
    status VARCHAR(20),
    observacoes TEXT

);

-- =========================================================
-- TABELA: ORDENS DE SERVICO
-- =========================================================

CREATE TABLE ordens_servico (
    codordem INT AUTO_INCREMENT PRIMARY KEY,

    coduser INT NOT NULL,
    codmoto INT NOT NULL,

    usuario_abertura INT NOT NULL,
    usuario_fechamento INT NULL,

    status INT NOT NULL,

    data_abertura DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_prevista DATE,
    data_fechamento DATETIME NULL,

    descricao_problema TEXT,
    valor_total DECIMAL(10,2) DEFAULT 0.00,

    CONSTRAINT fk_ordem_cliente
        FOREIGN KEY (coduser)
        REFERENCES clientes(coduser),

    CONSTRAINT fk_ordem_moto
        FOREIGN KEY (codmoto)
        REFERENCES motos(codmoto),

    CONSTRAINT fk_ordem_usuario_abertura
        FOREIGN KEY (usuario_abertura)
        REFERENCES usuarios(codusuario),

    CONSTRAINT fk_ordem_usuario_fechamento
        FOREIGN KEY (usuario_fechamento)
        REFERENCES usuarios(codusuario)
);

-- =========================================================
-- TABELA: ORDEM X SERVICOS
-- =========================================================

CREATE TABLE ordem_servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codordem INT NOT NULL,
    codservico INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_ordemservico_ordem
        FOREIGN KEY (codordem)
        REFERENCES ordens_servico(codordem),

    CONSTRAINT fk_ordemservico_servico
        FOREIGN KEY (codservico)
        REFERENCES servicos(codservico)
);

-- =========================================================
-- TABELA: ORDEM X PECAS
-- =========================================================

CREATE TABLE ordem_pecas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codordem INT NOT NULL,
    codpeca INT NOT NULL,
    quantidade INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_ordempeca_ordem
        FOREIGN KEY (codordem)
        REFERENCES ordens_servico(codordem),

    CONSTRAINT fk_ordempeca_peca
        FOREIGN KEY (codpeca)
        REFERENCES pecas(codpeca)
);

-- =========================================================
-- TABELA: LOGS DAS ORDENS
-- =========================================================

CREATE TABLE logs_ordens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codordem INT NOT NULL,
    acao VARCHAR(100),
    descricao TEXT,

    CONSTRAINT fk_logs_ordem
        FOREIGN KEY (codordem)
        REFERENCES ordens_servico(codordem)
);

-- =========================================================
-- TRIGGER: BAIXA ESTOQUE
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_baixa_estoque
AFTER INSERT ON ordem_pecas
FOR EACH ROW
BEGIN
    UPDATE pecas
    SET estoque = estoque - NEW.quantidade
    WHERE codpeca = NEW.codpeca;
END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: TOTAL SERVICOS + PECAS
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_total_servicos
AFTER INSERT ON ordem_servicos
FOR EACH ROW
BEGIN
    UPDATE ordens_servico
    SET valor_total =
        (SELECT IFNULL(SUM(valor),0) FROM ordem_servicos WHERE codordem = NEW.codordem)
        +
        (SELECT IFNULL(SUM(valor),0) FROM ordem_pecas WHERE codordem = NEW.codordem)
    WHERE codordem = NEW.codordem;
END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: TOTAL PECAS
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_total_pecas
AFTER INSERT ON ordem_pecas
FOR EACH ROW
BEGIN
    UPDATE ordens_servico
    SET valor_total =
        (SELECT IFNULL(SUM(valor),0) FROM ordem_servicos WHERE codordem = NEW.codordem)
        +
        (SELECT IFNULL(SUM(valor),0) FROM ordem_pecas WHERE codordem = NEW.codordem)
    WHERE codordem = NEW.codordem;
END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: LOG + FECHAMENTO
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_log_status_ordem
AFTER UPDATE ON ordens_servico
FOR EACH ROW
BEGIN
    IF OLD.status <> NEW.status THEN

        INSERT INTO logs_ordens (
            codordem,
            acao,
            descricao
        )
        VALUES (
            NEW.codordem,
            'ALTERACAO_STATUS',
            CONCAT('Status alterado de ', OLD.status, ' para ', NEW.status)
        );

        IF NEW.status = 4 THEN
            UPDATE ordens_servico
            SET data_fechamento = NOW()
            WHERE codordem = NEW.codordem;
        END IF;

    END IF;
END$$

DELIMITER ;

-- =========================================================
-- INSERTS BASE
-- =========================================================

-- USUARIOS
INSERT INTO usuarios (
    nome,
    email,
    senha,
    telefone,
    ultimo_login,
    conta_criada,
    senha_expira_em,
    nivel_usuario
) VALUES
('Felipe Pedroso Tavella','felipe@motos.com','123456','11988880001','2026-06-14 08:30:00','2025-01-10 09:00:00','2026-12-31','ADMIN'),
('Alex Pedroso Tavella','alex@motos.com','123456','11988880002','2026-06-14 08:15:00','2025-02-01 08:00:00','2026-12-31','ADMIN'),
('Luiz Fernando Ferreira Alves Boeno','luiz@motos.com','123456','11988880003','2026-06-13 17:45:00','2025-02-15 09:00:00','2026-12-31','ADMIN'),
('Rafael Lima','rafael@motos.com','123456','11988880004','2026-06-14 07:50:00','2025-03-01 08:30:00','2026-12-31','ADMIN'),
('Isaias Silva','isaias@motos.com','123456','11988880001','2026-06-14 08:30:00','2025-01-10 09:00:00','2026-12-31','ADMIN'),
('Carlos Mecânico','carlos@motos.com','123456','11988880002','2026-06-14 08:15:00','2025-02-01 08:00:00','2026-12-31','MECANICO'),
('Ana Souza','ana@motos.com','123456','11988880003','2026-06-13 17:45:00','2025-02-15 09:00:00','2026-12-31','ATENDENTE'),
('Bruno Alves','bruno@motos.com','123456','11988880005','2026-06-13 18:00:00','2025-03-20 09:00:00','2026-12-31','MECANICO'),
('Marcos Dias','marcos@motos.com','123456','11988880006','2026-06-14 08:05:00','2025-04-01 08:00:00','2026-12-31','MECANICO'),
('Juliana Costa','juliana@motos.com','123456','11988880007','2026-06-14 08:10:00','2025-04-15 09:00:00','2026-12-31','ATENDENTE'),
('Patrícia Rocha','patricia@motos.com','123456','11988880008','2026-06-13 16:40:00','2025-05-01 09:00:00','2026-12-31','ATENDENTE'),
('Diego Martins','diego@motos.com','123456','11988880009','2026-06-14 07:55:00','2025-05-20 08:00:00','2026-12-31','MECANICO'),
('Fernanda Lima','fernanda@motos.com','123456','11988880010','2026-06-13 17:20:00','2025-06-01 09:00:00','2026-12-31','GERENTE');

-- CLIENTES
INSERT INTO clientes (
    nome_completo,
    email,
    telefone,
    cpf,
    endereco,
    bairro,
    cidade,
    estado,
    cep,
    usuario_cadastro
) VALUES
('João Pereira','joao@gmail.com','11977770001','12345678901','Rua A,100','Centro','Tatui','SP','18270000',3),
('Maria Oliveira','maria@gmail.com','11977770002','12345678902','Rua B,200','Vila Nova','Tatui','SP','18270001',3),
('Pedro Santos','pedro@gmail.com','11977770003','12345678903','Rua C,300','Jardim Brasil','Tatui','SP','18270002',4),
('Lucas Mendes','lucas@gmail.com','11977770004','12345678904','Rua D,400','Centro','Tatui','SP','18270003',4),
('Camila Souza','camila@gmail.com','11977770005','12345678905','Rua E,500','Vila Nova','Tatui','SP','18270004',7),
('Thiago Alves','thiago@gmail.com','11977770006','12345678906','Rua F,600','Jardim Brasil','Tatui','SP','18270005',7),
('Bruna Lima','bruna@gmail.com','11977770007','12345678907','Rua G,700','Centro','Tatui','SP','18270006',8),
('Felipe Rocha','felipe@gmail.com','11977770008','12345678908','Rua H,800','Vila Nova','Tatui','SP','18270007',8),
('Gabriel Torres','gabriel@gmail.com','11977770009','12345678909','Rua I,900','Jardim Brasil','Tatui','SP','18270008',3),
('Larissa Nunes','larissa@gmail.com','11977770010','12345678910','Rua J,1000','Centro','Tatui','SP','18270009',4);

-- MOTOS
INSERT INTO motos (placa,modelo,marca,ano,cor,descricao,coduser) VALUES
('ABC1D23','CG 160 Fan','Honda',2020,'Preta','Uso diário',1),
('DEF2E34','Factor 150','Yamaha',2021,'Vermelha','Urbana',2),
('GHI3F45','CB 300R','Honda',2019,'Azul','Esportiva',3),
('JKL4G56','XRE 300','Honda',2022,'Branca','Trail',4),
('MNO5H67','NMAX 160','Yamaha',2023,'Preta','Scooter',5),
('PQR6I78','Bros 160','Honda',2021,'Vermelha','Trilha leve',6),
('STU7J89','MT-03','Yamaha',2020,'Cinza','Esportiva média',7),
('VWX8K90','Z400','Kawasaki',2022,'Verde','Naked esportiva',8),
('YZA9L12','PCX 150','Honda',2023,'Branca','Urbana econômica',9),
('BCD0M34','Twister 250','Honda',2018,'Preta','Estrada',1);

-- PECAS
INSERT INTO pecas (nome,marca,preco_medio,categoria,estoque,localizacao,descricao) VALUES
('Pastilha','Cobreq',80,'Freios',20,'A1','Freio'),
('Óleo','Lubrax',35,'Lubrificante',50,'B1','Motor'),
('Vela','NGK',25,'Motor',30,'C1','Ignição'),
('Corrente','DID',120,'Transmissão',10,'D1','Kit'),
('Filtro','Honda',60,'Motor',15,'E1','Filtro'),
('Bateria','Moura',180,'Elétrica',12,'F1','12V'),
('Cabo','Universal',40,'Controle',25,'G1','Embreagem'),
('Pneu D','Pirelli',300,'Rodas',8,'H1','Dianteiro'),
('Pneu T','Pirelli',350,'Rodas',8,'H2','Traseiro'),
('Lâmpada','Osram',30,'Elétrica',40,'I1','Farol');

-- SERVICOS
INSERT INTO servicos (nome,descricao,categoria,duracao_min,preco,dificuldade,tipo,status,observacoes) VALUES
('Troca óleo','Completa','Manutenção',30,50,'Baixa','Presencial','Ativo','1L'),
('Revisão','Completa','Revisão',120,180,'Alta','Presencial','Ativo',''),
('Freio','Pastilhas','Freios',60,90,'Média','Presencial','Ativo',''),
('Carburador','Limpeza','Motor',90,120,'Média','Presencial','Ativo',''),
('Corrente','Troca','Transmissão',120,200,'Alta','Presencial','Ativo',''),
('Scanner','Diagnóstico','Elétrica',40,80,'Baixa','Presencial','Ativo',''),
('Suspensão','Revisão','Suspensão',100,150,'Média','Presencial','Ativo',''),
('Bateria','Troca','Elétrica',30,50,'Baixa','Presencial','Ativo',''),
('Pneu','Troca','Rodas',60,70,'Baixa','Presencial','Ativo',''),
('Lavagem','Completa','Estética',25,40,'Baixa','Presencial','Ativo','');

-- ORDENS
INSERT INTO ordens_servico (
    coduser,
    codmoto,
    usuario_abertura,
    status,
    data_prevista,
    descricao_problema
) VALUES
(1,1,3,2,'2026-06-20','Motor'),
(2,2,3,1,'2026-06-21','Óleo'),
(3,3,4,3,'2026-06-22','Freio'),
(4,4,4,2,'2026-06-23','Suspensão'),
(5,5,7,1,'2026-06-24','Revisão'),
(6,6,7,2,'2026-06-25','Corrente'),
(7,7,8,3,'2026-06-26','Elétrica'),
(8,8,8,2,'2026-06-27','Pneu'),
(9,9,3,1,'2026-06-28','Motor'),
(10,10,4,2,'2026-06-29','Óleo');

-- ORDEM SERVICOS
INSERT INTO ordem_servicos (codordem,codservico,valor) VALUES
(1,2,180),(2,1,50),(3,3,90),(4,7,150),(5,2,180),
(6,5,200),(7,6,80),(8,9,70),(9,4,120),(10,1,50);

-- ORDEM PECAS
INSERT INTO ordem_pecas (codordem,codpeca,quantidade,valor) VALUES
(1,3,1,25),(2,2,1,35),(3,1,1,80),(4,7,1,40),(5,5,1,60),
(6,4,1,120),(7,10,1,30),(8,9,1,350),(9,6,1,180),(10,2,1,35);

-- ORDENS FECHADAS
INSERT INTO ordens_servico (
    coduser,
    codmoto,
    usuario_abertura,
    usuario_fechamento,
    status,
    data_abertura,
    data_prevista,
    data_fechamento,
    descricao_problema,
    valor_total
) VALUES
(1,2,3,2,4,'2026-05-05 09:00:00','2026-05-08','2026-05-06 15:30:00','Revisão geral',230),
(2,3,3,5,4,'2026-05-10 10:00:00','2026-05-12','2026-05-11 17:10:00','Freio',320),
(3,4,4,6,4,'2026-05-15 08:30:00','2026-05-18','2026-05-16 16:45:00','Corrente',410),
(4,5,7,2,4,'2026-06-02 09:15:00','2026-06-04','2026-06-03 14:20:00','Revisão',380),
(5,6,8,5,4,'2026-06-06 11:00:00','2026-06-08','2026-06-07 18:00:00','Bateria',260),
(6,7,3,6,4,'2026-06-10 08:00:00','2026-06-12','2026-06-11 13:40:00','Óleo + freio',210);

-- ORDEM SERVICOS FECHADAS
INSERT INTO ordem_servicos (codordem,codservico,valor) VALUES
(11,2,180),(11,3,90),
(12,3,90),(12,6,80),
(13,5,200),
(14,2,180),
(15,8,50),
(16,1,50),(16,3,90);

-- ORDEM PECAS FECHADAS
INSERT INTO ordem_pecas (codordem,codpeca,quantidade,valor) VALUES
(11,3,1,25),
(12,1,1,80),
(13,4,1,120),
(14,5,1,60),
(15,6,1,180),
(16,2,1,35);

-- LOGS
INSERT INTO logs_ordens (codordem,acao,descricao) VALUES
(1,'CRIACAO','OS aberta'),
(2,'CRIACAO','OS aberta'),
(3,'CRIACAO','OS aberta'),
(4,'CRIACAO','OS aberta'),
(5,'CRIACAO','OS aberta'),
(6,'CRIACAO','OS aberta'),
(7,'CRIACAO','OS aberta'),
(8,'CRIACAO','OS aberta'),
(9,'CRIACAO','OS aberta'),
(10,'CRIACAO','OS aberta');
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
    telefone VARCHAR(20)
);

-- =========================================================
-- TABELA: CLIENTES
-- =========================================================

CREATE TABLE clientes (
    coduser INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(150) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    telefone2 VARCHAR(20),
    email VARCHAR(150) UNIQUE,
    endereco VARCHAR(255),
    estado CHAR(2)
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
    preco_medio DECIMAL(10,2) NOT NULL,
    marca VARCHAR(100),
    estoque INT NOT NULL,
    estoque_minimo INT NOT NULL,
    descricao TEXT
);

-- =========================================================
-- TABELA: SERVICOS
-- =========================================================

CREATE TABLE servicos (
    codservico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    duracao_min INT,
    descricao TEXT
);

-- =========================================================
-- TABELA: ORDENS DE SERVICO
-- =========================================================

CREATE TABLE ordens_servico (
    codordem INT AUTO_INCREMENT PRIMARY KEY,

    coduser INT NOT NULL,
    codmoto INT NOT NULL,

    status INT NOT NULL,
    -- 1 = Aberto
    -- 2 = Em andamento
    -- 3 = Aguardando aprovação
    -- 4 = Concluído
    -- 5 = Cancelado

    data_prevista DATE,
    descricao_problema TEXT,
    valor_total DECIMAL(10,2) DEFAULT 0.00,

    CONSTRAINT fk_ordem_cliente
        FOREIGN KEY (coduser)
        REFERENCES clientes(coduser),

    CONSTRAINT fk_ordem_moto
        FOREIGN KEY (codmoto)
        REFERENCES motos(codmoto)
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
-- TRIGGER: BAIXA AUTOMATICA DE ESTOQUE
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
-- TRIGGER: SOMAR TOTAL DOS SERVICOS
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_total_servicos
AFTER INSERT ON ordem_servicos
FOR EACH ROW
BEGIN

    UPDATE ordens_servico
    SET valor_total = (
        SELECT IFNULL(SUM(valor),0)
        FROM ordem_servicos
        WHERE codordem = NEW.codordem
    )
    +
    (
        SELECT IFNULL(SUM(valor),0)
        FROM ordem_pecas
        WHERE codordem = NEW.codordem
    )
    WHERE codordem = NEW.codordem;

END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: SOMAR TOTAL DAS PECAS
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_total_pecas
AFTER INSERT ON ordem_pecas
FOR EACH ROW
BEGIN

    UPDATE ordens_servico
    SET valor_total = (
        SELECT IFNULL(SUM(valor),0)
        FROM ordem_servicos
        WHERE codordem = NEW.codordem
    )
    +
    (
        SELECT IFNULL(SUM(valor),0)
        FROM ordem_pecas
        WHERE codordem = NEW.codordem
    )
    WHERE codordem = NEW.codordem;

END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: GERAR LOG AO CRIAR ORDEM
-- =========================================================

DELIMITER $$

CREATE TRIGGER trg_log_criacao_ordem
AFTER INSERT ON ordens_servico
FOR EACH ROW
BEGIN

    INSERT INTO logs_ordens (
        codordem,
        acao,
        descricao
    )
    VALUES (
        NEW.codordem,
        'CRIACAO',
        'Ordem de serviço criada no sistema'
    );

END$$

DELIMITER ;

-- =========================================================
-- TRIGGER: GERAR LOG AO ALTERAR STATUS
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
            CONCAT(
                'Status alterado de ',
                OLD.status,
                ' para ',
                NEW.status
            )
        );

    END IF;

END$$

DELIMITER ;

-- =========================================================
-- INSERTS - USUARIOS
-- =========================================================

INSERT INTO usuarios (
    nome,
    email,
    senha,
    telefone
)
VALUES
(
    'Felipe Pedroso Tavella',
    'felipetavella@gmail.com',
    '123456',
    '(19)99999-1111'
),
(
    'Alex Pedroso Tavella',
    'alextavella@gmail.com',
    '654321',
    '(19)99999-2222'
);

-- =========================================================
-- INSERTS - CLIENTES
-- =========================================================

INSERT INTO clientes (
    nome_completo,
    telefone,
    telefone2,
    email,
    endereco,
    estado
)
VALUES
(
    'João Carlos da Silva',
    '(19)98888-1111',
    '(19)97777-1111',
    'joao.silva@email.com',
    'Rua A, 120',
    'SP'
),
(
    'Maria Aparecida Oliveira',
    '(19)98888-2222',
    '(19)97777-2222',
    'maria.oliveira@email.com',
    'Rua B, 300',
    'SP'
);

-- =========================================================
-- INSERTS - MOTOS
-- =========================================================

INSERT INTO motos (
    placa,
    modelo,
    marca,
    ano,
    cor,
    descricao,
    coduser
)
VALUES
(
    'ABC1D23',
    'CG 160 Titan',
    'Honda',
    2021,
    'Preta',
    'Moto em ótimo estado',
    1
),
(
    'XYZ9K87',
    'Fazer 250',
    'Yamaha',
    2021,
    'Vermelha',
    'Necessita revisão',
    2
);

-- =========================================================
-- INSERTS - PECAS
-- =========================================================

INSERT INTO pecas (
    nome,
    preco_medio,
    marca,
    estoque,
    estoque_minimo,
    descricao
)
VALUES
(
    'Óleo 10W30',
    45.00,
    'Lubrificantes',
    20,
    5,
    'Óleo para motores 4 tempos'
),
(
    'Pastilha de Freio Dianteira',
    80.00,
    'freio',
    15,
    3,
    'Pastilha dianteira'
);

-- =========================================================
-- INSERTS - SERVICOS
-- =========================================================

INSERT INTO servicos (
    nome,
    preco,
    duracao_min,
    descricao
)
VALUES
(
    'Substituição do óleo do motor e verificação do nível',
    120.00,
    40,
    'Substituição do óleo do motor'
),
(
    'Verificação completa do sistema elétrico e bateria',
    350.00,
    180,
    'Revisão geral da motocicleta'
);

-- =========================================================
-- INSERTS - ORDENS DE SERVICO
-- =========================================================

INSERT INTO ordens_servico (
    coduser,
    codmoto,
    status,
    data_prevista,
    descricao_problema
)
VALUES
(
    1,
    1,
    1,
    '2026-05-25',
    'Moto com barulho no motor'
),
(
    2,
    2,
    2,
    '2026-05-28',
    'Freio traseiro falhando'
);

-- =========================================================
-- INSERTS - ORDEM X SERVICOS
-- =========================================================

INSERT INTO ordem_servicos (
    codordem,
    codservico,
    valor
)
VALUES
(
    1,
    1,
    120.00
),
(
    2,
    2,
    350.00
);

-- =========================================================
-- INSERTS - ORDEM X PECAS
-- =========================================================

INSERT INTO ordem_pecas (
    codordem,
    codpeca,
    quantidade,
    valor
)
VALUES
(
    1,
    1,
    2,
    70.00
),
(
    2,
    2,
    1,
    80.00
);

-- =========================================================
-- CONSULTAS PARA TESTE
-- =========================================================

-- CONSULTA 1
-- LISTAR ORDENS COM CLIENTE E MOTO

/*
SELECT
    os.codordem,
    c.nome_completo AS cliente,
    m.modelo,
    m.placa,
    os.status,
    os.valor_total
FROM ordens_servico os
INNER JOIN clientes c
    ON os.coduser = c.coduser
INNER JOIN motos m
    ON os.codmoto = m.codmoto;
*/

-- CONSULTA 2
-- LISTAR PECAS UTILIZADAS NAS ORDENS

/*
SELECT
    op.codordem,
    p.nome AS peca,
    op.quantidade,
    op.valor
FROM ordem_pecas op
INNER JOIN pecas p
    ON op.codpeca = p.codpeca;
*/

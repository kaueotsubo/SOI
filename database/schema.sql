CREATE DATABASE IF NOT EXISTS soi
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE soi;

-- ============================================
-- TABELA: CURSO
-- ============================================
CREATE TABLE curso (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo BOOLEAN NOT NULL DEFAULT TRUE
);

-- ============================================
-- TABELA: GRAVIDADE
-- ============================================
CREATE TABLE gravidade (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    nivel TINYINT UNSIGNED NOT NULL UNIQUE
);

-- ============================================
-- TABELA: USUARIO
-- ============================================
CREATE TABLE usuario (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    cargo ENUM('DIRECAO', 'PROFESSOR', 'MONITOR') NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- TABELA: ALUNO
-- ============================================
CREATE TABLE aluno (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(30) NOT NULL UNIQUE,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    nome VARCHAR(150) NOT NULL,
    curso_id INT UNSIGNED NOT NULL,
    status ENUM('ATIVO', 'INATIVO', 'TRANSFERIDO', 'CONCLUIDO')
        NOT NULL DEFAULT 'ATIVO',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_aluno_curso
        FOREIGN KEY (curso_id)
        REFERENCES curso(id)
);

-- ============================================
-- TABELA: TIPO DE OCORRENCIA
-- ============================================
CREATE TABLE tipo_ocorrencia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL UNIQUE,
    descricao TEXT NULL,
    gravidade_inicial_id INT UNSIGNED NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT fk_tipo_ocorrencia_gravidade
        FOREIGN KEY (gravidade_inicial_id)
        REFERENCES gravidade(id)
);

-- ============================================
-- TABELA: OCORRENCIA
-- ============================================
CREATE TABLE ocorrencia (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    aluno_id INT UNSIGNED NOT NULL,
    curso_id INT UNSIGNED NOT NULL,

    tipo_ocorrencia_id INT UNSIGNED NOT NULL,
    gravidade_id INT UNSIGNED NOT NULL,

    registrado_por_id INT UNSIGNED NOT NULL,

    relato TEXT NOT NULL,

    data_ocorrencia DATE NOT NULL,
    data_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ano_letivo YEAR NOT NULL,

    status ENUM('ATIVA', 'CANCELADA')
        NOT NULL DEFAULT 'ATIVA',

    cancelada_por_id INT UNSIGNED NULL,
    cancelada_em TIMESTAMP NULL,
    motivo_cancelamento TEXT NULL,

    CONSTRAINT fk_ocorrencia_aluno
        FOREIGN KEY (aluno_id)
        REFERENCES aluno(id),

    CONSTRAINT fk_ocorrencia_curso
        FOREIGN KEY (curso_id)
        REFERENCES curso(id),

    CONSTRAINT fk_ocorrencia_tipo
        FOREIGN KEY (tipo_ocorrencia_id)
        REFERENCES tipo_ocorrencia(id),

    CONSTRAINT fk_ocorrencia_gravidade
        FOREIGN KEY (gravidade_id)
        REFERENCES gravidade(id),

    CONSTRAINT fk_ocorrencia_registrado_por
        FOREIGN KEY (registrado_por_id)
        REFERENCES usuario(id),

    CONSTRAINT fk_ocorrencia_cancelada_por
        FOREIGN KEY (cancelada_por_id)
        REFERENCES usuario(id)
);

-- ============================================
-- TABELA: MEDIDA DISCIPLINAR
-- ============================================
CREATE TABLE medida_disciplinar (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ocorrencia_id INT UNSIGNED NOT NULL,
    aplicada_por_id INT UNSIGNED NOT NULL,

    tipo VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,

    data_aplicacao DATE NOT NULL,
    status ENUM('ATIVA', 'CUMPRIDA', 'CANCELADA')
        NOT NULL DEFAULT 'ATIVA',

    CONSTRAINT fk_medida_ocorrencia
        FOREIGN KEY (ocorrencia_id)
        REFERENCES ocorrencia(id),

    CONSTRAINT fk_medida_aplicada_por
        FOREIGN KEY (aplicada_por_id)
        REFERENCES usuario(id)
);

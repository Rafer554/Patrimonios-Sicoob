CREATE TABLE CentrodeCusto( 
      id number(10)   , 
      CentroCusto varchar  (20)   , 
      Descricao varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Grupo( 
      id number(10)    NOT NULL , 
      CodGrupoPatrimonio varchar  (20)   , 
      tipoDepreciacao number(10)   , 
      valorDepreciacao binary_double   , 
      column_5 number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Local( 
      id number(10)   , 
      Descricao varchar  (100)   , 
      CentrodeCusto_id number(10)    NOT NULL , 
      Local varchar  (20)   , 
      responsavel varchar  (100)    NOT NULL , 
      chapa number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacao( 
      id number(10)    NOT NULL , 
      localAntigo number(10)    NOT NULL , 
      patrimonioId number(10)    NOT NULL , 
      dataInspecao date    NOT NULL , 
      Descricao varchar(3000)   , 
      imagem varchar  (200)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacaoDepreciacao( 
      id number(10)    NOT NULL , 
      patrimonioId number(10)    NOT NULL , 
      dataDepreciacao number(10)    NOT NULL , 
      valor binary_double    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Patrimonio( 
      id number(10)   , 
      CodigodoPatrimonio varchar  (20)   , 
      descricao varchar  (100)    NOT NULL , 
      ativo number(10)   , 
      responsavel varchar  (100)   , 
      chapa number(10)   , 
      Local_id number(10)    NOT NULL , 
      Grupo_id number(10)    NOT NULL , 
      ValorOriginal binary_double   , 
      ValorAtual binary_double   , 
      DataEntrada date   , 
      imagem varchar  (200)   , 
      tido_baixa_id number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_baixa( 
      id number(10)    NOT NULL , 
      Descricao varchar(3000)    NOT NULL , 
      observacao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

 
 ALTER TABLE CentrodeCusto ADD UNIQUE (id);
 ALTER TABLE CentrodeCusto ADD UNIQUE (CentroCusto);
 ALTER TABLE Local ADD UNIQUE (id);
 ALTER TABLE Patrimonio ADD UNIQUE (id);
 ALTER TABLE Patrimonio ADD UNIQUE (CodigodoPatrimonio);
  
 ALTER TABLE Local ADD CONSTRAINT fk_Local_1 FOREIGN KEY (CentrodeCusto_id) references CentrodeCusto(id); 
ALTER TABLE movimentacao ADD CONSTRAINT fk_movimentacao_1 FOREIGN KEY (localAntigo) references Local(id); 
ALTER TABLE movimentacao ADD CONSTRAINT fk_movimentacao_2 FOREIGN KEY (patrimonioId) references Patrimonio(id); 
ALTER TABLE movimentacaoDepreciacao ADD CONSTRAINT fk_movimentacaoDepreciacao_1 FOREIGN KEY (patrimonioId) references Patrimonio(id); 
ALTER TABLE Patrimonio ADD CONSTRAINT fk_Patrimonio_1 FOREIGN KEY (tido_baixa_id) references tipo_baixa(id); 
ALTER TABLE Patrimonio ADD CONSTRAINT fk_Patrimonio_2 FOREIGN KEY (Grupo_id) references Grupo(id); 
 CREATE SEQUENCE CentrodeCusto_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER CentrodeCusto_id_seq_tr 

BEFORE INSERT ON CentrodeCusto FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT CentrodeCusto_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE Grupo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER Grupo_id_seq_tr 

BEFORE INSERT ON Grupo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT Grupo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE Local_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER Local_id_seq_tr 

BEFORE INSERT ON Local FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT Local_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE movimentacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER movimentacao_id_seq_tr 

BEFORE INSERT ON movimentacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT movimentacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE movimentacaoDepreciacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER movimentacaoDepreciacao_id_seq_tr 

BEFORE INSERT ON movimentacaoDepreciacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT movimentacaoDepreciacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE Patrimonio_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER Patrimonio_id_seq_tr 

BEFORE INSERT ON Patrimonio FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT Patrimonio_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_baixa_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_baixa_id_seq_tr 

BEFORE INSERT ON tipo_baixa FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_baixa_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 
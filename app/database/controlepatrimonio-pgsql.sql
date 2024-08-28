CREATE TABLE CentrodeCusto( 
      id  SERIAL    , 
      CentroCusto varchar  (20)   , 
      Descricao varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Grupo( 
      id  SERIAL    NOT NULL  , 
      CodGrupoPatrimonio varchar  (20)   , 
      tipoDepreciacao integer   , 
      valorDepreciacao float   , 
      column_5 integer   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Local( 
      id  SERIAL    , 
      Descricao varchar  (100)   , 
      CentrodeCusto_id integer   NOT NULL  , 
      Local varchar  (20)   , 
      responsavel varchar  (100)   NOT NULL  , 
      chapa integer   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacao( 
      id  SERIAL    NOT NULL  , 
      localAntigo integer   NOT NULL  , 
      patrimonioId integer   NOT NULL  , 
      dataInspecao date   NOT NULL  , 
      Descricao text   , 
      imagem varchar  (200)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacaoDepreciacao( 
      id  SERIAL    NOT NULL  , 
      patrimonioId integer   NOT NULL  , 
      dataDepreciacao integer   NOT NULL  , 
      valor float   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Patrimonio( 
      id  SERIAL    , 
      CodigodoPatrimonio varchar  (20)   , 
      descricao varchar  (100)   NOT NULL  , 
      ativo integer   , 
      responsavel varchar  (100)   , 
      chapa integer   , 
      Local_id integer   NOT NULL  , 
      Grupo_id integer   NOT NULL  , 
      ValorOriginal float   , 
      ValorAtual float   , 
      DataEntrada date   , 
      imagem varchar  (200)   , 
      tido_baixa_id integer   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_baixa( 
      id  SERIAL    NOT NULL  , 
      Descricao text   NOT NULL  , 
      observacao text   , 
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
 
 CREATE index idx_Local_CentrodeCusto_id on Local(CentrodeCusto_id); 
CREATE index idx_movimentacao_localAntigo on movimentacao(localAntigo); 
CREATE index idx_movimentacao_patrimonioId on movimentacao(patrimonioId); 
CREATE index idx_movimentacaoDepreciacao_patrimonioId on movimentacaoDepreciacao(patrimonioId); 
CREATE index idx_Patrimonio_tido_baixa_id on Patrimonio(tido_baixa_id); 
CREATE index idx_Patrimonio_Grupo_id on Patrimonio(Grupo_id); 

CREATE TABLE CentrodeCusto( 
      id  INT IDENTITY    , 
      CentroCusto varchar  (20)   , 
      Descricao varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Grupo( 
      id  INT IDENTITY    NOT NULL  , 
      CodGrupoPatrimonio varchar  (20)   , 
      tipoDepreciacao int   , 
      valorDepreciacao float   , 
      column_5 int   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Local( 
      id  INT IDENTITY    , 
      Descricao varchar  (100)   , 
      CentrodeCusto_id int   NOT NULL  , 
      Local varchar  (20)   , 
      responsavel varchar  (100)   NOT NULL  , 
      chapa int   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacao( 
      id  INT IDENTITY    NOT NULL  , 
      localAntigo int   NOT NULL  , 
      patrimonioId int   NOT NULL  , 
      dataInspecao date   NOT NULL  , 
      Descricao nvarchar(max)   , 
      imagem varchar  (200)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacaoDepreciacao( 
      id  INT IDENTITY    NOT NULL  , 
      patrimonioId int   NOT NULL  , 
      dataDepreciacao int   NOT NULL  , 
      valor float   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Patrimonio( 
      id  INT IDENTITY    , 
      CodigodoPatrimonio varchar  (20)   , 
      descricao varchar  (100)   NOT NULL  , 
      ativo int   , 
      responsavel varchar  (100)   , 
      chapa int   , 
      Local_id int   NOT NULL  , 
      Grupo_id int   NOT NULL  , 
      ValorOriginal float   , 
      ValorAtual float   , 
      DataEntrada date   , 
      imagem varchar  (200)   , 
      tido_baixa_id int   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_baixa( 
      id  INT IDENTITY    NOT NULL  , 
      Descricao nvarchar(max)   NOT NULL  , 
      observacao nvarchar(max)   , 
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

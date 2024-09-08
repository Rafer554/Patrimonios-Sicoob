PRAGMA foreign_keys=OFF; 

CREATE TABLE CentrodeCusto( 
      id  INTEGER    , 
      CentroCusto varchar  (20)   , 
      Descricao varchar  (100)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Grupo( 
      id  INTEGER    NOT NULL  , 
      CodGrupoPatrimonio varchar  (20)   , 
      tipoDepreciacao int   , 
      valorDepreciacao double   , 
      column_5 int   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE Local( 
      id  INTEGER    , 
      Descricao varchar  (100)   , 
      CentrodeCusto_id int   NOT NULL  , 
      Local varchar  (20)   , 
      responsavel varchar  (100)   NOT NULL  , 
      chapa int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(CentrodeCusto_id) REFERENCES CentrodeCusto(id)) ; 

CREATE TABLE movimentacao( 
      id  INTEGER    NOT NULL  , 
      localAntigo int   NOT NULL  , 
      patrimonioId int   NOT NULL  , 
      dataInspecao date   NOT NULL  , 
      Descricao text   , 
      imagem varchar  (200)   , 
 PRIMARY KEY (id),
FOREIGN KEY(localAntigo) REFERENCES Local(id),
FOREIGN KEY(patrimonioId) REFERENCES Patrimonio(id)) ; 

CREATE TABLE movimentacaoDepreciacao( 
      id  INTEGER    NOT NULL  , 
      patrimonioId int   NOT NULL  , 
      dataDepreciacao int   NOT NULL  , 
      valor double   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(patrimonioId) REFERENCES Patrimonio(id)) ; 

CREATE TABLE Patrimonio( 
      id  INTEGER    , 
      CodigodoPatrimonio varchar  (20)   , 
      descricao varchar  (100)   NOT NULL  , 
      ativo int   , 
      responsavel varchar  (100)   , 
      chapa int   , 
      Local_id int   NOT NULL  , 
      Grupo_id int   NOT NULL  , 
      ValorOriginal double   , 
      ValorAtual double   , 
      DataEntrada date   , 
      imagem varchar  (200)   , 
      tido_baixa_id int   , 
 PRIMARY KEY (id),
FOREIGN KEY(tido_baixa_id) REFERENCES tipo_baixa(id),
FOREIGN KEY(Grupo_id) REFERENCES Grupo(id)) ; 

CREATE TABLE tipo_baixa( 
      id  INTEGER    NOT NULL  , 
      Descricao text   NOT NULL  , 
      observacao text   , 
 PRIMARY KEY (id)) ; 

 
 CREATE UNIQUE INDEX unique_idx_CentrodeCusto_id ON CentrodeCusto(id);
 CREATE UNIQUE INDEX unique_idx_CentrodeCusto_CentroCusto ON CentrodeCusto(CentroCusto);
 CREATE UNIQUE INDEX unique_idx_Local_id ON Local(id);
 CREATE UNIQUE INDEX unique_idx_Patrimonio_id ON Patrimonio(id);
 CREATE UNIQUE INDEX unique_idx_Patrimonio_CodigodoPatrimonio ON Patrimonio(CodigodoPatrimonio);
 
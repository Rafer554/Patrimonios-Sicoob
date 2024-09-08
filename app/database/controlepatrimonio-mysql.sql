CREATE TABLE CentrodeCusto( 
      `id`  INT  AUTO_INCREMENT    , 
      `CentroCusto` varchar  (20)   , 
      `Descricao` varchar  (100)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE Grupo( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `CodGrupoPatrimonio` varchar  (20)   , 
      `tipoDepreciacao` int   , 
      `valorDepreciacao` double   , 
      `column_5` int   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE Local( 
      `id`  INT  AUTO_INCREMENT    , 
      `Descricao` varchar  (100)   , 
      `CentrodeCusto_id` int   NOT NULL  , 
      `Local` varchar  (20)   , 
      `responsavel` varchar  (100)   NOT NULL  , 
      `chapa` int   NOT NULL  , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE movimentacao( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `localAntigo` int   NOT NULL  , 
      `patrimonioId` int   NOT NULL  , 
      `dataInspecao` date   NOT NULL  , 
      `Descricao` text   , 
      `imagem` varchar  (200)   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE movimentacaoDepreciacao( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `patrimonioId` int   NOT NULL  , 
      `dataDepreciacao` int   NOT NULL  , 
      `valor` double   NOT NULL  , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE Patrimonio( 
      `id`  INT  AUTO_INCREMENT    , 
      `CodigodoPatrimonio` varchar  (20)   , 
      `descricao` varchar  (100)   NOT NULL  , 
      `ativo` int   , 
      `responsavel` varchar  (100)   , 
      `chapa` int   , 
      `Local_id` int   NOT NULL  , 
      `Grupo_id` int   NOT NULL  , 
      `ValorOriginal` double   , 
      `ValorAtual` double   , 
      `DataEntrada` date   , 
      `imagem` varchar  (200)   , 
      `tido_baixa_id` int   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

CREATE TABLE tipo_baixa( 
      `id`  INT  AUTO_INCREMENT    NOT NULL  , 
      `Descricao` text   NOT NULL  , 
      `observacao` text   , 
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 

 
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

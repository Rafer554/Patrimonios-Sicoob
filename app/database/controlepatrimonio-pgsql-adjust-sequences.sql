SELECT setval('CentrodeCusto_id_seq', coalesce(max(id),0) + 1, false) FROM CentrodeCusto;
SELECT setval('Grupo_id_seq', coalesce(max(id),0) + 1, false) FROM Grupo;
SELECT setval('Local_id_seq', coalesce(max(id),0) + 1, false) FROM Local;
SELECT setval('movimentacao_id_seq', coalesce(max(id),0) + 1, false) FROM movimentacao;
SELECT setval('movimentacaoDepreciacao_id_seq', coalesce(max(id),0) + 1, false) FROM movimentacaoDepreciacao;
SELECT setval('Patrimonio_id_seq', coalesce(max(id),0) + 1, false) FROM Patrimonio;
SELECT setval('tipo_baixa_id_seq', coalesce(max(id),0) + 1, false) FROM tipo_baixa;
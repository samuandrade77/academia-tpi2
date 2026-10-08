# Academia Força Viva — Painel Administrativo (TPI II · Momento I)

Sistema administrativo de uma academia de musculação: alunos, instrutores, planos, matrículas e avaliações físicas.

| Aluno | RA | GitHub | Branch |
|---|---|---|---|
| [Samuel Andrade Silva] | [5175015] | [samuandrade77] | `branch-samuel` |
| [Joel Silva dos Santos ] | [5172861] | [JoelSilva97] | `branch-joel` |

## Executar
```bash
php -S localhost:8000
```
Acesse `http://localhost:8000`.     

## Organização
```
index.html           painel com menu
alunos.html          -> api/aluno.php
instrutores.html     -> api/instrutor.php
planos.html          -> api/plano.php
matriculas.html      -> api/matricula.php
avaliacoes.html      -> api/avaliacao.php
assets/estilo.css    visual
scripts/formulario.js  envia o formulário em JSON via fetch e mostra o retorno
api/Validador.php    classe comum: lê o JSON, acumula erros, responde JSON
```
As páginas são HTML puro; o PHP só devolve JSON (não há HTML misturado com PHP).

## Banco de dados planejado (7 tabelas — ainda não criado)
| Tabela | Campos | Relação |
|---|---|---|
| `aluno` | id, nome, cpf, nascimento, sexo, telefone, email, objetivo, responsavel | 1:N matricula, avaliacao_fisica, treino |
| `instrutor` | id, nome, cref, especialidade, turno, contratacao, email, telefone | 1:N matricula, treino |
| `plano` | id, nome, duracao, mensalidade, desconto, acessos, descricao | 1:N matricula |
| `matricula` | id, aluno_id, plano_id, instrutor_id, inicio, termino, pagamento, vencimento, valor | N:1 aluno, plano, instrutor |
| `avaliacao_fisica` | id, aluno_id, data, peso, altura, cintura, quadril, imc | N:1 aluno |
| `treino` | id, aluno_id, instrutor_id, nome (A/B/C), data_inicio, observacao | N:1 aluno, instrutor; 1:N exercicio |
| `exercicio` | id, treino_id, nome, grupo_muscular, series, repeticoes, carga | N:1 treino |

## Funcionalidades
| Tela | Validações | Lógica adicional |
|---|---|---|
| Alunos | obrigatórios, CPF, e-mail, telefone, objetivo, idade 12–100 | idade, faixa (Teen/Adulto/Melhor idade), exige responsável para menor de 18 |
| Instrutores | obrigatórios, CREF `012345-G/MG`, turno, e-mail, contratação não futura | categoria (Graduado/Provisionado), UF do CREF, carga semanal pelo turno, tempo de casa, se pode supervisionar estagiário |
| Planos | duração 1/3/6/12, mensalidade ≥ R$ 50, desconto 0–40%, acessos 1–7, mensal sem desconto | valor total, economia, mensal efetivo e custo por treino |
| Matrículas | CPF, plano existente, pagamento, vencimento 5/10/15/20, início não passado | data de término, 5% no PIX, parcelas no cartão/boleto |
| Avaliações | CPF, data, faixas de peso/altura/cintura/quadril, sexo | IMC + classificação OMS, relação cintura-quadril e risco, data da próxima avaliação (+90 dias) |

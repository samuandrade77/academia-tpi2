# Comandos git (na ordem)

## 1. [ALUNO 1] — cria o repositório (branch main)
```bash
git init                                  # cria o repositório local
git branch -M main                        # define main como branch principal
git add README.md                         # prepara o README
git commit -m "README com descrição do sistema e modelo de dados"
git remote add origin https://github.com/[USUARIO 1]/academia-tpi2.git   # liga ao GitHub
git push -u origin main                   # envia a main
```
## 2. [ALUNO 1] — branch-aluno-1
```bash
git checkout -b branch-aluno-1            # cria e entra na branch do aluno 1
git add index.html assets/ scripts/ api/Validador.php
git commit -m "Painel inicial, menu, estilo, envio JSON e classe Validador"
git add alunos.html api/aluno.php instrutores.html api/instrutor.php
git commit -m "Cadastros de aluno e instrutor"
git push -u origin branch-aluno-1         # envia a branch
git checkout main                         # volta para a main
git merge --no-ff branch-aluno-1 -m "Merge da branch-aluno-1"   # junta, criando commit de merge
git push origin main                      # atualiza a main no GitHub
```
## 3. [ALUNO 2] — branch-aluno-2
```bash
git clone https://github.com/[USUARIO 1]/academia-tpi2.git      # baixa o projeto
cd academia-tpi2                          # entra na pasta
git checkout -b branch-aluno-2            # cria e entra na branch do aluno 2
git add planos.html api/plano.php
git commit -m "Cadastro de planos"
git add matriculas.html api/matricula.php avaliacoes.html api/avaliacao.php
git commit -m "Matrículas e avaliações físicas"
git add docs/
git commit -m "Questionários de desenvolvimento e comandos git"
git push -u origin branch-aluno-2         # envia a branch
git checkout main                         # volta para a main
git pull origin main                      # baixa a versão mais recente da main
git merge --no-ff branch-aluno-2 -m "Merge da branch-aluno-2"   # junta o trabalho do aluno 2
git push origin main                      # main final com todas as páginas
```
## 4. Conferência
```bash
git log --oneline --graph --all           # mostra o histórico com as duas junções
```

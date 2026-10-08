/*
 * Envio dos formulários em JSON usando fetch.
 * O <form> informa no atributo "action" qual arquivo PHP vai processar os dados.
 */
const form = document.querySelector('form');
const retorno = document.getElementById('retorno');

form.addEventListener('submit', async function (evento) {
  evento.preventDefault();

  // Converte os campos do formulário em um objeto { nome: valor }
  const dados = Object.fromEntries(new FormData(form).entries());

  try {
    const resposta = await fetch(form.getAttribute('action'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(dados),
    });
    const resultado = await resposta.json();
    exibir(resultado);
    if (resultado.ok) form.reset();
  } catch (erro) {
    exibir({ ok: false, mensagens: ['Não foi possível falar com o servidor.'] });
  }
});

function exibir(resultado) {
  retorno.className = resultado.ok ? 'ok' : 'falha';
  retorno.replaceChildren();

  const titulo = document.createElement('strong');
  titulo.textContent = resultado.ok ? resultado.titulo : 'Não foi possível salvar:';
  retorno.append(titulo);

  if (resultado.ok) {
    const tabela = document.createElement('table');
    for (const [rotulo, valor] of Object.entries(resultado.resumo)) {
      const linha = tabela.insertRow();
      linha.insertCell().textContent = rotulo;
      linha.insertCell().textContent = valor;
    }
    retorno.append(tabela);
  } else {
    const lista = document.createElement('ul');
    resultado.mensagens.forEach(function (texto) {
      const item = document.createElement('li');
      item.textContent = texto;
      lista.append(item);
    });
    retorno.append(lista);
  }
}

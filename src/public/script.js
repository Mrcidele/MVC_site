// Array para AutoComplete
const cidades = [
    "Cabo Frio, RJ", "Rio de Janeiro, RJ", "São Paulo, SP",
    "Belo Horizonte, MG", "Curitiba, PR", "Arraial do Cabo, RJ", "Búzios, RJ"
];

// Função para configurar o autocomplete
function configurarAutocomplete(idInput, idLista) {
    const input = document.getElementById(idInput);
    const lista = document.getElementById(idLista);

    if(!input || !lista) return;

    input.addEventListener('input', function() {
        const valorDigitado = this.value.toLowerCase();
        lista.innerHTML = '';

        if (valorDigitado.length === 0) {
            lista.style.display = 'none';
            return;
        }

        const sugestoes = cidades.filter(cidade =>
            cidade.toLowerCase().includes(valorDigitado)
        );

        if (sugestoes.length === 0) {
            lista.style.display = 'none';
            return;
        }

        lista.style.display = 'block';
        sugestoes.forEach(cidade => {
            const li = document.createElement('li');
            li.textContent = cidade;

            li.addEventListener('click', () => {
                input.value = cidade;
                lista.style.display = 'none';
            });

            lista.appendChild(li);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target !== input) {
            lista.style.display = 'none';
        }
    });
}

configurarAutocomplete('origem', 'lista-origem');
configurarAutocomplete('destino', 'lista-destino');

// BOTÃO INVERTER
const botaoInverter = document.querySelector('.botao-inverter');
const inputOrigem = document.getElementById('origem');
const inputDestino = document.getElementById('destino');

if(botaoInverter && inputOrigem && inputDestino) {
    botaoInverter.addEventListener('click', () => {
        const temporario = inputOrigem.value;
        inputOrigem.value = inputDestino.value;
        inputDestino.value = temporario;

        botaoInverter.style.transform = botaoInverter.style.transform === 'rotate(180deg)'
            ? 'rotate(0deg)'
            : 'rotate(180deg)';
    });
}

const campoSaida = document.getElementById('data-saida');
const campoRetorno = document.getElementById('data-retorno');

if(campoSaida && campoRetorno) {
    const hoje = new Date().toISOString().split("T")[0];
    campoSaida.setAttribute('min', hoje);
    campoRetorno.setAttribute('min', hoje);

    campoSaida.addEventListener('change', function () {
        const dataEscolhidaSaida = campoSaida.value;
        campoRetorno.setAttribute('min', dataEscolhidaSaida);

        if (campoRetorno.value && campoRetorno.value < dataEscolhidaSaida) {
            campoRetorno.value = "";
            alert("Atenção: A data de retorno não pode ser anterior à data de saída.");
        }
    });
}

function validarApenasNumeros(e) {
    if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
    }
}

// DADOS DOS DESTINOS EM DESTAQUE / ARRAY
const destinos = [
    {
        cidade: "São Paulo",
        foto: "imagem/sp.jpg",
        alt: "Vista SP",
        posicao: "center center",
        rotas: [
            { origem: "Rio de Janeiro, RJ", preco: "R$104"},
            { origem: "Belo Horizonte, MG", preco: "R$139"},
            { origem: "Ribeirão Preto, SP", preco: "R$144"},
            { origem: "Sorocaba, SP", preco: "R$44"}
        ]
    },
    {
        cidade: "Rio de Janeiro",
        foto: "imagem/rio.jpg",
        alt: "Vista RJ",
        posicao: "center 40%",
        rotas: [
            { origem: "São Paulo, SP", preco: "R$87"},
            { origem: "Belo Horizonte, MG", preco: "R$69"},
            { origem: "Cabo Frio, RJ", preco: "R$75"},
            { origem: "Macaé, RJ", preco: "R$89"}
        ]
    },
    {
        cidade: "Curitiba",
        foto: "imagem/curitiba.jpg",
        alt: "Vista Curitiba",
        posicao: "center 30%",
        rotas: [
            { origem: "Florianópolis,SC", preco: "R$68"},
            { origem: "Porto Alegre, RS", preco: "R$27"},
            { origem: "Joinville, SC", preco: "R$38"},
            { origem: "Rio de Janeiro, RJ", preco: "R$22"}
        ]
    }
];

function renderizarDestinos() {
    const grade = document.getElementById('grade-destinos');
    if (!grade) return;

    grade.innerHTML = destinos.map(destino => `
    <article class="card-destino">
        <figure>
            <img
                src="${destino.foto}"
                alt="${destino.alt}"
                loading="lazy"
                style="object-position: ${destino.posicao}"
                onerror="this.src='https://placehold.co/600x160/0c2044/ffffff?text=${encodeURIComponent(destino.cidade)}'"
            >
        </figure>
        <div class="card-destino-corpo">
            <h3>${destino.cidade}</h3>
            <table class="rotas">
                <thead>
                    <tr>
                        <th>Partindo de</th>
                        <th>A partir de</th>
                    </tr>
                </thead>
                <tbody>
                    ${destino.rotas.map(rota => `
                        <tr>
                            <td>${rota.origem}</td>
                            <td>${rota.preco}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    </article>
`).join('');
}
renderizarDestinos();

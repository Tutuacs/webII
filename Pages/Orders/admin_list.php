<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

//Apenas Admin/Interno
require_internal_user();

$page_title = 'Gestão de Pedidos (Admin)';
include_once __DIR__ . '/../Common/layout_header.php';
?>

<div class="container" style="margin-top: 30px;">
    <h2><span class="glyphicon glyphicon-list-alt"></span> Gestão de Pedidos</h2>
    <hr>

    <div class="row">
        <div class="col-md-12">
            
            <div class="well">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" id="buscaCliente" class="form-control" placeholder="Buscar por nome do cliente...">
                    </div>
                    <div class="col-md-3">
                        <input type="number" id="buscaNumero" class="form-control" placeholder="Nº do Pedido">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" onclick="buscarPedidos()"><span class="glyphicon glyphicon-search"></span> Pesquisar</button>
                        <button class="btn btn-default" onclick="limparBusca()">Limpar</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="border: none;"> 
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Data do Pedido</th>
                            <th>Cliente</th>
                            <th>Situação</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaPedidos">
                        <tr><td colspan="5" class="text-center text-muted">Use a busca acima para carregar os pedidos.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalhes" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Detalhes do Pedido <span id="modalPedidoNumero"></span></h4>
            </div>
            <div class="modal-body">
                
                <div id="carrosselProdutos" class="carousel slide" data-ride="carousel" style="margin-bottom: 20px; background: #f8f8f8; text-align: center;">
                    <div class="carousel-inner" id="carrosselInner">
                        </div>
                    <a class="left carousel-control" href="#carrosselProdutos" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carrosselProdutos" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>

                <h5>Itens Comprados:</h5>
                <ul class="list-group" id="listaItensPedido">
                    </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
// Variável global para armazenar os dados cacheados da API
let pedidosRecentes = [];

// Função AJAX
function buscarPedidos() {
    const cliente = document.getElementById('buscaCliente').value;
    const numero = document.getElementById('buscaNumero').value;
    
    let url = '/Service/Orders/api_consulta.php?';
    if (numero) url += 'numero=' + numero;
    else if (cliente) url += 'cliente=' + encodeURIComponent(cliente);
    else url += 'cliente='; // Busca vazia retorna todos (se a API permitir)

    const tbody = document.getElementById('tabelaPedidos');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Carregando pedidos...</td></tr>';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.erro || data.mensagem) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${data.erro || data.mensagem}</td></tr>`;
                return;
            }

            pedidosRecentes = data.dados;
            tbody.innerHTML = ''; // Limpa a tabela

            // Preenche a tabela 
            data.dados.forEach((pedido, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>#${pedido.pedido_numero}</strong></td>
                   <td>${formatarDataBR(pedido.data_pedido)}</td>
                    <td>${pedido.cliente_nome}</td>
                    <td><span class="label label-info">${pedido.situacao}</span></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="abrirDetalhes(${index})">
                            <span class="glyphicon glyphicon-eye-open"></span> Ver Detalhes
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error("Erro na requisição AJAX:", error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erro de conexão com a API.</td></tr>';
        });
}

// Função para exibir o DETALHE e montar o CARROSSEL
function abrirDetalhes(index) {
    const pedido = pedidosRecentes[index];
    
    // Pega o ID (ajuste para pedido.id se a sua api principal retornar como 'id' ao invés de 'pedido_numero')
    const idDoPedido = pedido.pedido_numero || pedido.id; 
    
    document.getElementById('modalPedidoNumero').innerText = '#' + idDoPedido;

    const lista = document.getElementById('listaItensPedido');
    const carrossel = document.getElementById('carrosselInner');
    
    // 1. Mostra o modal IMEDIATAMENTE com estado de carregamento
    lista.innerHTML = '<li class="list-group-item text-center"><strong>Carregando itens...</strong></li>';
    carrossel.innerHTML = '';
    $('#modalDetalhes').modal('show');

    // 2. Faz a requisição AJAX buscando os itens específicos deste pedido
    fetch('/Service/Orders/api_itens_pedido.php?pedido_id=' + idDoPedido)
        .then(response => response.json())
        .then(data => {
            lista.innerHTML = '';
            carrossel.innerHTML = '';

            if (data.itens && data.itens.length > 0) {
                data.itens.forEach((item, i) => {
                    // Calcula os totais (Qtd * Preço)
                    const valorTotalItem = (item.quantidade * item.preco).toFixed(2).replace('.', ',');
                    const precoUnitario = parseFloat(item.preco).toFixed(2).replace('.', ',');
                    
                    // Preenche a lista HTML
                    lista.innerHTML += `
                        <li class="list-group-item">
                            <strong>Produto ID: ${item.produto_id}</strong><br>
                            Qtd: ${item.quantidade} | Unitário: R$ ${precoUnitario} | <strong>Total: R$ ${valorTotalItem}</strong>
                        </li>
                    `;

                    // Preenche o Carrossel
                    const activeClass = i === 0 ? 'active' : '';
                    carrossel.innerHTML += `
                        <div class="item ${activeClass}">
                            <img src="https://via.placeholder.com/400x200?text=Produto+${item.produto_id}" alt="Foto Produto" style="margin: 0 auto; max-height: 250px;">
                            <div class="carousel-caption" style="color: #333; background: rgba(255,255,255,0.8); border-radius: 4px; padding: 2px 10px;">
                                Qtd: ${item.quantidade} | R$ ${valorTotalItem}
                            </div>
                        </div>
                    `;
                });
            } else {
                lista.innerHTML = '<li class="list-group-item">Nenhum item encontrado para este pedido.</li>';
                carrossel.innerHTML = '<div class="item active"><img src="https://via.placeholder.com/400x200?text=Sem+Itens" style="margin: 0 auto;"></div>';
            }
        })
        .catch(error => {
            console.error("Erro no AJAX ao buscar itens:", error);
            lista.innerHTML = '<li class="list-group-item text-danger">Erro de conexão ao carregar os itens.</li>';
        });
}

function formatarDataBR(dataISO) {
    if (!dataISO) return '';
    // Divide a data e a hora
    const [data, hora] = dataISO.split(' ');
    const [ano, mes, dia] = data.split('-');
    return `${dia}/${mes}/${ano} ${hora}`;
}

function limparBusca() {
    document.getElementById('buscaCliente').value = '';
    document.getElementById('buscaNumero').value = '';
    document.getElementById('tabelaPedidos').innerHTML = '<tr><td colspan="5" class="text-center text-muted">Use a busca acima para carregar os pedidos.</td></tr>';
}

window.onload = function() {
    buscarPedidos();
};
</script>

<?php include_once __DIR__ . '/../Common/layout_footer.php'; ?>
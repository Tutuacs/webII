<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Service/Auth/session.php';

// Apenas Admin/Interno (Se der "Não autorizado", faça login novamente no sistema!)
require_internal_user();

$page_title = 'Gestão de Pedidos (Admin)';
include_once __DIR__ . '/../Common/layout_header.php';


?>

<style>
    .modal-body {
        background-color: #fcfcfc;
    }

    .carousel-control {
        background-image: none !important;
        color: #333 !important;
    }

    .carousel-control:hover {
        color: #000 !important;
    }

    .list-group-item {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px !important;
        margin-bottom: 5px;
    }
</style>


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
                            <th>Ações</th>
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
                    <div class="carousel-inner" id="carrosselInner"></div>
                    <a class="left carousel-control" href="#carrosselProdutos" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carrosselProdutos" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>

                <h5>Itens Comprados:</h5>
                <ul class="list-group" id="listaItensPedido"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Alterar Status do Pedido <span id="statusPedidoNumero"></span></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="statusIdPedido">
                <div class="form-group">
                    <label for="selectStatus">Nova Situação:</label>
                    <select id="selectStatus" class="form-control">
                        <option value="PENDENTE">Pendente</option>
                        <option value="PROCESSANDO">Em processamento</option>
                        <option value="ENVIADO">Enviado</option>
                        <option value="ENTREGUE">Entregue</option>
                        <option value="CANCELADO">Cancelado</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarStatusPedido()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
let pedidosRecentes = [];

function buscarPedidos() {
    const cliente = document.getElementById('buscaCliente').value;
    const numero = document.getElementById('buscaNumero').value;
    
   let url = '/pedidos';
    if (numero) {
        url = '/pedidos/' + numero;
    } else if (cliente) {
        url = '/pedidos/cliente/' + encodeURIComponent(cliente);
    }

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
            tbody.innerHTML = '';

            data.dados.forEach((pedido, index) => {
                const tr = document.createElement('tr');
                const idDoPedido = pedido.pedido_numero || pedido.id;
                
                // Definição dinâmica das cores das labels do Bootstrap
                let labelClass = 'label-info';
                if (pedido.situacao === 'ENTREGUE') labelClass = 'label-success';
                if (pedido.situacao === 'CANCELADO') labelClass = 'label-danger';

                // Mostra alteração de status enquanto não estiver finalizado/cancelado
                let botaoStatus = '';
                if (pedido.situacao !== 'ENTREGUE' && pedido.situacao !== 'CANCELADO') {
                    botaoStatus = `
                        <button class="btn btn-sm btn-info" onclick="abrirAlterarStatus(${index})">
                            <span class="glyphicon glyphicon-edit"></span> Status
                        </button>
                    `;
                }

                tr.innerHTML = `
                    <td><strong>#${idDoPedido}</strong></td>
                    <td>${formatarDataBR(pedido.data_pedido)}</td>
                    <td>${pedido.cliente_nome}</td>
                    <td><span class="label ${labelClass}">${pedido.situacao}</span></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="abrirDetalhes(${index})">
                            <span class="glyphicon glyphicon-eye-open"></span> Ver Detalhes
                        </button>
                        ${botaoStatus}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error("Erro na requisição AJAX:", error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erro de conexão com a API ou sessão expirada.</td></tr>';
        });
}

function abrirDetalhes(index) {
    const pedido = pedidosRecentes[index];
    const idDoPedido = pedido.pedido_numero || pedido.id; 
    
    document.getElementById('modalPedidoNumero').innerText = '#' + idDoPedido;

    const lista = document.getElementById('listaItensPedido');
    const carrossel = document.getElementById('carrosselInner');
    
    lista.innerHTML = '<li class="list-group-item text-center"><strong>Carregando itens...</strong></li>';
    carrossel.innerHTML = '';
    $('#modalDetalhes').modal('show');

    fetch('/Service/Orders/api_itens_pedido.php?pedido_id=' + idDoPedido)
        .then(response => response.json())
        .then(data => {
            lista.innerHTML = '';
            carrossel.innerHTML = '';

            if (data.itens && data.itens.length > 0) {
                data.itens.forEach((item, i) => {
                    const valorTotalItem = (item.quantidade * item.preco).toFixed(2).replace('.', ',');
                    const precoUnitario = parseFloat(item.preco).toFixed(2).replace('.', ',');
                    
                     lista.innerHTML += `
                            <li class="list-group-item" style="border-left: 5px solid #337ab7; margin-bottom: 10px; border-radius: 4px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="text-align: left; flex-grow: 1;">
                                        <h5 style="margin: 0 0 4px 0; font-weight: bold; color: #333;">${item.produto_nome}</h5>
                                        <small style="color: #666; display: block; margin-bottom: 8px;">${item.produto_descricao}</small>
                                    </div>
                                    
                                    <span class="label label-primary" style="font-size: 13px; padding: 6px 12px; margin-left: 15px; flex-shrink: 0;">
                                        Qtd: ${item.quantidade}
                                    </span>
                                </div>
                                
                                <div style="border-top: 1px solid #eee; padding-top: 8px; font-size: 13px; color: #555; text-align: left;">
                                    Unit: R$ ${precoUnitario} | <strong>Total: R$ ${valorTotalItem}</strong>
                                </div>
                            </li>
                        `;

                    const activeClass = i === 0 ? 'active' : '';
                    const srcFoto = item.foto_base64 ? `data:image/jpeg;base64,${item.foto_base64}` : 'https://via.placeholder.com/400x200?text=Sem+Foto';
                    
                    carrossel.innerHTML += `
                        <div class="item ${activeClass}">
                            <img src="${srcFoto}" alt="Foto Produto" style="margin: 0 auto; max-height: 200px;">
                            <div class="carousel-caption" style="color: #333; background: rgba(255,255,255,0.8); border-radius: 4px; padding: 2px 10px;">
                                ${item.produto_nome}
                            </div>
                        </div>
                    `;
                });
            } else {
                lista.innerHTML = '<li class="list-group-item">Nenhum item encontrado.</li>';
                carrossel.innerHTML = '<div class="item active"><img src="https://via.placeholder.com/400x200?text=Sem+Itens" style="margin: 0 auto;"></div>';
            }
        })
        .catch(error => {
            console.error("Erro no AJAX:", error);
            lista.innerHTML = '<li class="list-group-item text-danger">Erro ao carregar os itens.</li>';
        });
}

function abrirAlterarStatus(index) {
    const pedido = pedidosRecentes[index];
    const idDoPedido = pedido.pedido_numero || pedido.id;

    document.getElementById('statusPedidoNumero').innerText = '#' + idDoPedido;
    document.getElementById('statusIdPedido').value = idDoPedido;
    document.getElementById('selectStatus').value = pedido.situacao;

    $('#modalStatus').modal('show');
}

function salvarStatusPedido() {
    const idPedido = document.getElementById('statusIdPedido').value;
    const novoStatus = document.getElementById('selectStatus').value;

    fetch('/Service/Orders/api_alterar_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `pedido_id=${idPedido}&situacao=${encodeURIComponent(novoStatus)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            $('#modalStatus').modal('hide');
            buscarPedidos(); 
        } else {
            alert(data.erro || "Erro ao atualizar o status do pedido.");
        }
    })
    .catch(error => {
        console.error("Erro no processamento:", error);
        alert("Erro de comunicação ao salvar o novo status.");
    });
}

function formatarDataBR(dataISO) {
    if (!dataISO) return '';
    const [data, hora] = dataISO.split(' ');
    const [ano, mes, dia] = data.split('-');
    return hora ? `${dia}/${mes}/${ano} ${hora}` : `${dia}/${mes}/${ano}`;
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
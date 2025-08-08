const API_BASE = '/backend/api/itens.php'; // ajuste se necessário

const form = document.getElementById('item-form');
const tableBody = document.querySelector('#itens-table tbody');
const inputId = document.getElementById('item-id');
const inputNome = document.getElementById('nome');
const inputTipo = document.getElementById('tipo');
const inputQuantidade = document.getElementById('quantidade');

const filterQ = document.getElementById('filter-q');
const filterTipo = document.getElementById('filter-tipo');

async function fetchItems(filters = {}){
  const params = new URLSearchParams(filters).toString();
  const res = await fetch(API_BASE + (params ? ('?' + params) : ''));
  const json = await res.json();
  if(json.success) return json.data;
  throw new Error(json.error || 'Erro ao obter itens');
}

function renderItems(items){
  tableBody.innerHTML = '';
  items.forEach(it => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${it.id}</td>
      <td>${escapeHtml(it.nome)}</td>
      <td>${escapeHtml(it.tipo)}</td>
      <td>${it.quantidade}</td>
      <td>
        <button class="btn-small" onclick="editItem(${it.id})">Editar</button>
        <button class="btn-small" onclick="removeItem(${it.id})">Excluir</button>
      </td>
    `;
    tableBody.appendChild(tr);
  });
}

function escapeHtml(str){
  if(!str) return '';
  return str.replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[m]; });
}

async function load(){
  try{
    const item

document.addEventListener('DOMContentLoaded', function(){
  // Find product grid(s)
  const grids = document.querySelectorAll('.product-grid');
  if (!grids.length)
      return;

  function ensureCollectionContainer(afterNode){
    let container = document.getElementById('collectionList');
    if (container)
        return container;
    container = document.createElement('div');
    container.id = 'collectionList';
    container.className = 'collection-list container';
    container.innerHTML = `
      <h3>Your collection list</h3>
      <div class="collection-items">
        <table class="collection-table"><thead><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Total</th><th></th></tr></thead><tbody></tbody></table>
        <div class="collection-empty">No items in the collection.</div>
      </div>
    `;
    // Insert after the first grid by default
    afterNode.parentNode.insertBefore(container, afterNode.nextSibling);
    return container;
  }

  // Data store (in-memory)
  const store = {};

  function formatPrice(p){
    return '$' + parseFloat(p).toFixed(2);
  }

  function renderCollection(container){
    const tbody = container.querySelector('.collection-table tbody');
    const empty = container.querySelector('.collection-empty');
    tbody.innerHTML = '';
    const keys = Object.keys(store);
    if (!keys.length){
      empty.style.display = 'block';
      container.querySelector('.collection-table').style.display = 'none';
      return;
    }
    empty.style.display = 'none';
    container.querySelector('.collection-table').style.display = 'table';
    keys.forEach(key => {
      const item = store[key];
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${item.title}</td>
        <td><input class="col-qty" type="number" min="1" value="${item.qty}" data-key="${key}"></td>
        <td>${formatPrice(item.price)}</td>
        <td>${formatPrice(item.price * item.qty)}</td>
        <td><button class="remove-col" data-key="${key}">Remove</button></td>
      `;
      tbody.appendChild(tr);
    });
  }

  function addToStore(key, title, price, qty){
    qty = parseInt(qty,10) || 1;
    if (store[key]) store[key].qty += qty;
    else store[key] = { title, price: parseFloat(price), qty };
  }

  grids.forEach(grid => {

    const collectionContainer = ensureCollectionContainer(grid);

    grid.querySelectorAll('.collection-controls').forEach(el => el.remove());
    grid.querySelectorAll('.collection-qty').forEach(el => el.remove());

    const cards = grid.querySelectorAll('.product-card');
    cards.forEach((card, idx) => {
      const addBtn = card.querySelector('.add-btn');
      if (!addBtn) return; // nothing to attach to

      if (addBtn.dataset.collectionAttached) return;
      addBtn.dataset.collectionAttached = '1';

      addBtn.addEventListener('click', function(e){
        try{ e.preventDefault(); }catch(err){}
        const titleEl = card.querySelector('.product-info h4');
        const priceEl = card.querySelector('.product-info .price');
        const title = titleEl ? titleEl.textContent.trim() : ('Item ' + (idx+1));
        const priceText = priceEl ? priceEl.textContent.replace(/[^0-9.]/g,'') : '0';
        const price = parseFloat(priceText) || 0;
        const qty = 1;
        const key = title + '|' + price;
        addToStore(key, title, price, qty);
        renderCollection(collectionContainer);
      });

    });

    collectionContainer.addEventListener('click', function(e){
      if (e.target.classList.contains('remove-col')){
        const key = e.target.dataset.key;
        delete store[key];
        renderCollection(collectionContainer);
      }
    });
    collectionContainer.addEventListener('input', function(e){
      if (e.target.classList.contains('col-qty')){
        const key = e.target.dataset.key;
        let val = parseInt(e.target.value,10);
        if (!val || val < 1) val = 1;
        if (store[key]){
          store[key].qty = val;
          renderCollection(collectionContainer);
        }
      }
    });

  });

});

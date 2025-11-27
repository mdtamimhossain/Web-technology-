// priceTax.js
// Exports a function getTotalPrice(priceWOTax) that returns price including 19% tax
(function(){
  function getTotalPrice(priceWOTax){
    // ensure numeric
    const p = parseFloat(priceWOTax) || 0;
    return +(p * 1.19).toFixed(2);
  }

  // expose on window for convenience
  window.getTotalPrice = getTotalPrice;

  // wire UI if present
  document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('priceWOTax');
    const outW = document.getElementById('showPriceWOTax');
    const outWith = document.getElementById('showPriceWithTax');
    if (!input || !outW || !outWith) return;

    function update(){
      const v = input.value;
      const p = parseFloat(v);
      if (isNaN(p)){
        outW.textContent = '-';
        outWith.textContent = '-';
        return;
      }
      outW.textContent = '$' + p.toFixed(2);
      outWith.textContent = '$' + getTotalPrice(p).toFixed(2);
    }

    input.addEventListener('input', update);
    // init
    update();
  });
})();

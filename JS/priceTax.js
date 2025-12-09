const getTotalPrice = priceWOTax => {
  const p = Number(priceWOTax) || 0;
  return Number((p * 1.19).toFixed(2));
};

window.getTotalPrice = getTotalPrice;

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('priceWOTax');
  const outW = document.getElementById('showPriceWOTax');
  const outWith = document.getElementById('showPriceWithTax');
  if (!input || !outW || !outWith) return;

  const update = () => {
    const v = input.value.trim();
    const p = parseFloat(v);
    if (Number.isNaN(p)) {
      outW.textContent = '-';
      outWith.textContent = '-';
      return;
    }
    outW.textContent = `$${p.toFixed(2)}`;
    outWith.textContent = `$${getTotalPrice(p).toFixed(2)}`;
  };

  input.addEventListener('input', update);
  update();
});

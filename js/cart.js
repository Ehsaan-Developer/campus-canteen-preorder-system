function getCart() {
  return JSON.parse(localStorage.getItem("cart")) || [];
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart));
}

// Add item with quantity
function addToCart(name, price) {
  let cart = getCart();

  // check if already exists
  const existing = cart.find(i => i.name === name);

  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ name, price: Number(price), qty: 1 });
  }

  saveCart(cart);

  // optional: no alert
  // alert(name + " added to cart!");
}

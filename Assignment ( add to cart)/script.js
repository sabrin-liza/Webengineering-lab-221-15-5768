const items = [
  {name: "Water bottle", img: "111.jpg", price: 200, discount: 5, applyDiscount: true, },
  {name: "Tiffin box", img: "222.jpg", price: 155, discount: 5, applyDiscount: false, },
  {name: "Frying Pan", img: "333.jpg", price: 300, discount: 5, applyDiscount: true, },
];

let totalCartQuantity = 0;

// the cart quantity display
let cartQtySpan = document.getElementById("cart-qty");
if (!cartQtySpan) {
  const cart = document.querySelector(".cart");
  cartQtySpan = document.createElement("span");
  cartQtySpan.id = "cart-qty";
  cartQtySpan.style.marginLeft = "8px";
  cartQtySpan.style.background = "#f3ef04";
  cartQtySpan.style.color = "#222";
  cartQtySpan.style.padding = "2px 8px";
  cartQtySpan.style.borderRadius = "10px";
  cartQtySpan.textContent = "0";
  if (cart) cart.appendChild(cartQtySpan);
}

const container = document.getElementById("product-container");

items.forEach((item) => {  
  const card = document.createElement("div");
  card.className = "productcard";
  card.style.position = "relative"; // Ensure positioning for offer badge

  // Offer badge
  if (item.applyDiscount && item.discount > 0) {
    const offerBadge = document.createElement("div");
    offerBadge.textContent = `${item.discount}% OFF`;
    offerBadge.style.position = "absolute";
    offerBadge.style.top = "10px";
    offerBadge.style.left = "10px";
    offerBadge.style.background = "#f3ef04";
    offerBadge.style.color = "#222";
    offerBadge.style.padding = "3px 10px";
    offerBadge.style.borderRadius = "6px";
    offerBadge.style.fontWeight = "bold";
    offerBadge.style.fontSize = "13px";
    card.appendChild(offerBadge);
  }

  const img = document.createElement("img");
  img.src = item.img;
  img.alt = item.name;

  const name = document.createElement("h3");
  name.textContent = item.name;

  const ing = document.createElement("p");
  ing.textContent = item.ingredients;

  const priceDiv = document.createElement("div");

  const oldPrice = document.createElement("p");
  oldPrice.style.textDecoration = "line-through";
  oldPrice.style.color = "gray";
  oldPrice.style.fontSize = "14px";

  const newPrice = document.createElement("p");
  newPrice.style.fontWeight = "bold";
  newPrice.style.fontSize = "16px";

  if (item.applyDiscount && item.discount > 0) {
    oldPrice.textContent = `${item.price}tk`;
    const discounted = item.price * (1 - item.discount / 100);
    newPrice.textContent = `${discounted.toFixed(0)}tk (${item.discount}% OFF)`;
    priceDiv.appendChild(oldPrice);
  } else {
    newPrice.textContent = `${item.price}tk`;
  }

  priceDiv.appendChild(newPrice);

  // Quantity Controls
  const qtyContainer = document.createElement("div");
  qtyContainer.style.marginTop = "10px";

  const minus = document.createElement("button");
  minus.textContent = "-";
  const qty = document.createElement("span");
  qty.textContent = "1";
  qty.style.margin = "0 10px";
  const plus = document.createElement("button");
  plus.textContent = "+";

  let quantity = 1;

  minus.onclick = () => {
    if (quantity > 1) {
      quantity--;
      qty.textContent = quantity;
    }
  };

  plus.onclick = () => {
    quantity++;
    qty.textContent = quantity;
  };

  minus.style.padding = plus.style.padding = "5px 10px";
  minus.style.marginRight = plus.style.marginLeft = "5px";

  qtyContainer.appendChild(minus);
  qtyContainer.appendChild(qty);
  qtyContainer.appendChild(plus);

  // Add to Cart Button
  const addToCartBtn = document.createElement("button");
  addToCartBtn.textContent = "Add to Cart";
  addToCartBtn.style.marginTop = "12px";
  addToCartBtn.style.padding = "7px 18px";
  addToCartBtn.style.background = "#f3ef04";
  addToCartBtn.style.border = "none";
  addToCartBtn.style.borderRadius = "4px";
  addToCartBtn.style.cursor = "pointer";
  addToCartBtn.style.fontWeight = "bold";

  addToCartBtn.onclick = () => {
    totalCartQuantity += quantity;
    cartQtySpan.textContent = totalCartQuantity;
  };

  card.appendChild(img);
  card.appendChild(name);
  card.appendChild(ing);
  card.appendChild(priceDiv);
  card.appendChild(qtyContainer);
  card.appendChild(addToCartBtn);

  container.appendChild(card);
});

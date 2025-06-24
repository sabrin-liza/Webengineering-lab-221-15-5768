const n = parseInt(prompt("Enter a number:"));

function fibonacci(n) {
  let arr = [0, 1];
  for (let i = 2; i < n; i++) {
    arr[i] = arr[i-1] + arr[i-2];
  }
  return arr;
}

function display(){

  const fibSeries = fibonacci(n);
  const divv = document.createElement("div");
  divv.style.fontSize = "24px";
  divv.style.margin = "20px";
  divv.style.display = "flex";
  divv.style.flexDirection = "column";
  divv.style.alignItems = "flex-start";
  divv.style.gap = "10px";


  
  fibSeries.forEach((n,index) => {
    const span = document.createElement("span");
    span.textContent = n;
    span.style.color = "#ffffff";
    span.style.padding = "10px 15px";
    span.style.borderRadius = "20px";
    span.style.border = "2px solid " + (index % 2 === 0 ? "blue" : "green");
    span.style.backgroundColor = index % 2 === 0 ? "blue" : "green";
    span.style.divv = "inline-block";
    divv.appendChild(span);
  });

  document.body.appendChild(divv);
}

display();
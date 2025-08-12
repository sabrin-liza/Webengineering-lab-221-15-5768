function openFeedbackForm() {
  document.getElementById("feedbackForm").style.display = "flex";
}

function closeFeedbackForm() {
  document.getElementById("feedbackForm").style.display = "none";
  clearStars();
}

function submitFeedback() {
  alert("Thank you for your feedback!");
  closeFeedbackForm();
}

function setRating(event) {
  const stars = document.querySelectorAll(".stars i");
  let selected = parseInt(event.target.getAttribute("data-value"));

  stars.forEach((star, index) => {
    if (index < selected) {
      star.classList.add("active");
    } else {
      star.classList.remove("active");
    }
  });
}

function clearStars() {
  const stars = document.querySelectorAll(".stars i");
  stars.forEach(star => star.classList.remove("active"));
}

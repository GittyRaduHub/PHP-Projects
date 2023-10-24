document.querySelector(".form-style").addEventListener("submit", function () {
  const formData = new FormData(this);
  fetch("index.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
});
//submit ul formului cu Js

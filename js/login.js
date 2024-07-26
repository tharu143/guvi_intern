$(document).ready(function () {
  $("#loginForm").submit(function (event) {
    event.preventDefault();
    var formData = {
      email: $("#email").val(),
      password: $("#password").val(),
    };
    $.ajax({
      type: "POST",
      url: "../php/login.php",
      data: formData,
      dataType: "json",
      encode: true,
    }).done(function (data) {
      if (data.success) {
        localStorage.setItem("authToken", data.token);
        window.location.href = "../html/profile.html";
      } else {
        alert("Login failed: " + data.message);
      }
    });
  });
});

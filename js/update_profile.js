$(document).ready(function () {
  var authToken = localStorage.getItem("authToken");
  if (!authToken) {
    window.location.href = "../html/login.html";
  }

  $.ajax({
    type: "GET",
    url: "../php/get_profile.php",
    data: { token: authToken },
    dataType: "json",
    encode: true,
  }).done(function (data) {
    if (data.success) {
      $("#dob").val(data.profile.dob);
      $("#emergency_contact").val(data.profile.emergency_contact);
    } else {
      alert("Failed to load profile: " + data.message);
    }
  });

  $("#updateProfileForm").submit(function (event) {
    event.preventDefault();

    var formData = new FormData();
    formData.append("token", authToken);
    formData.append("dob", $("#dob").val());
    formData.append("emergency_contact", $("#emergency_contact").val());
    formData.append("photo", $("#photo")[0].files[0]);

    $.ajax({
      type: "POST",
      url: "../php/update_profile.php",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
    }).done(function (data) {
      if (data.success) {
        alert("Profile updated successfully");
        window.location.href = "../html/profile.html";
      } else {
        alert("Failed to update profile: " + data.message);
      }
    });
  });
});

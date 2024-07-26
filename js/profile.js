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
      $("#profileFname").text(data.profile.fname);
      $("#profileLname").text(data.profile.lname);
      $("#profileEmail").text(data.profile.email);
      $("#profileDob").text(data.profile.dob);
      $("#profileEmergencyContact").text(data.profile.emergency_contact);
      if (data.profile.photo) {
        $("#profilePhoto").attr("src", data.profile.photo);
      }
      // Calculate age
      var dob = new Date(data.profile.dob);
      var ageDifMs = Date.now() - dob.getTime();
      var ageDate = new Date(ageDifMs);
      var age = Math.abs(ageDate.getUTCFullYear() - 1970);
      $("#profileAge").text(age);
    } else {
      alert("Failed to load profile: " + data.message);
    }
  });

  $("#editProfileBtn").click(function () {
    window.location.href = "../html/update_profile.html";
  });
});

$(document).ready(function() {
    var token = localStorage.getItem('authToken');
    if (!token) {
        window.location.href = 'login.html';
    }
    
    $.ajax({
        url: '../php/get_profile.php',
        type: 'GET',
        data: { token: token },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#age').val(response.profile.age);
                $('#dob').val(response.profile.dob);
                $('#contact').val(response.profile.contact);
            } else {
                alert(response.message);
            }
        },
        error: function () {
            alert('Failed to fetch profile data.');
        }
    });
    $('#profileForm').submit(function(event) {
        event.preventDefault();
        var formData = {
            age: $('#age').val(),
            dob: $('#dob').val(),
            contact: $('#contact').val(),
            token: token
        };
        $.ajax({
            type: 'POST',
            url: '../php/update_profile.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            if (data.success) {
                alert('Profile updated successfully!');
            } else {
                alert('Profile update failed: ' + data.message);
            }
        });
    });
});

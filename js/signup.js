$(document).ready(function() {
    $('#signupForm').submit(function(event) {
        event.preventDefault();
        var formData = {
            fname: $('#fname').val(),
            lname: $('#lname').val(),
            email: $('#email').val(),
            password: $('#password').val()
        };
        $.ajax({
            type: 'POST',
            url: '../php/register.php',
            data: formData,
            dataType: 'json',
            encode: true
        })
        .done(function(data) {
            if (data.success) {
                alert('Signup successful!');
                window.location.href = '../html/login.html';
            } else {
                alert('Signup failed: ' + data.message);
            }
        });
    });
});

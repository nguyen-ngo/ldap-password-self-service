<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy;Jan 2017</p>
    </div>
</footer>
<!-- /Footer -->

<script type="text/javascript" src="vendors/jquery/jquery-3.1.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js"
        integrity="sha384-XTs3FgkjiBgo8qjEjBk0tGmf3wPrWtA6coPfQDfFEY8AnYJwjalXCiosYRBIBZX8"
        crossorigin="anonymous"></script>

<script type="text/javascript" src="vendors/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript">

    $("#authenticateButton").click(function () {
        if ($("#g-recaptcha-response").length > 0) {
            if($("#g-recaptcha-response").val()) {
                $.ajax({
                    type: "POST",
                    url: "models/actions.php?action=authenticate",
                    data: "username=" + $("#username").val() + "&password=" + $("#password").val() + "&loginActive=" + $("#formAction").val() + "&gcaptcha=" + $("#g-recaptcha-response").val(),
                    success: function (result) {
                        if (result == "OK") {
                            window.location.assign("<?php echo $homepage ?>/?type=login");
                        } else {
                            var errorMessage = "<div class='alert alert-danger'>" + result + "</div>";
                            $("#errorMessage").html(errorMessage).show();
                        }
                    }
                });
            } else {
                $("#errorMessage").html("<div class='alert alert-danger'>Please check the Captcha</div>").show();
            }
        } else {
            $.ajax({
                type: "POST",
                url: "models/actions.php?action=authenticate",
                data: "username=" + $("#username").val() + "&password=" + $("#password").val() + "&loginActive=" + $("#formAction").val(),
                success: function (result) {
                    if (result == "OK") {
                        window.location.assign("<?php echo $homepage ?>/?type=login");
                    } else {
                        var errorMessage = "<div class='alert alert-danger'>" + result + "</div>";
                        $("#errorMessage").html(errorMessage).show();
                    }
                }
            });
        }
    });


    $("#sendPasswordButton").click(function () {
        /*
        if($("#g-recaptcha-response").val()) {
            $.ajax({
                type: "POST",
                url: "models/actions.php?action=reset",
                data: "username=" + $("#username").val() + "&email=" + $("#email").val() + "&loginActive=" + $("#formAction").val() + "&gcaptcha=" + $("#g-recaptcha-response").val(),
                success: function (result) {
                    if (result == "OK") {
                        var errorMessage = "<div class='alert alert-success'>&#10003; A mail with information is sent to you.<br>Click the link inside to complete the password reset</div>";
                        $("#errorMessage").html(errorMessage).show();
                        //$("#sendPasswordButton").addClass("disabled");
                        $("#sendPasswordButton").css("display", "none");
                    } else {
                        var errorMessage = "<div class='alert alert-danger'>" + result +"</div>";
                        $("#errorMessage").html(errorMessage).show();
                    }
                }
            });
        } else {
            $("#errorMessage").html("<div class='alert alert-danger'>&diams; Please check the Captcha</div>").show();
        }*/

        if ($("#g-recaptcha-response").length > 0) {
            if($("#g-recaptcha-response").val()) {
                $.ajax({
                    type: "POST",
                    url: "models/actions.php?action=reset",
                    data: "username=" + $("#username").val() + "&email=" + $("#email").val() + "&loginActive=" + $("#formAction").val() + "&gcaptcha=" + $("#g-recaptcha-response").val(),
                    success: function (result) {
                        if (result == "OK") {
                            var errorMessage = "<div class='alert alert-success'>&#10003; A mail with information is sent to you.<br>Click the link inside to complete the password reset</div>";
                            $("#errorMessage").html(errorMessage).show();
                            $("#sendPasswordButton").css("display", "none");
                        } else {
                            var errorMessage = "<div class='alert alert-danger'>" + result +"</div>";
                            $("#errorMessage").html(errorMessage).show();
                        }
                    }
                });
            } else {
                $("#errorMessage").html("<div class='alert alert-danger'>Please check the Captcha</div>").show();
            }
        } else {
            $.ajax({
                type: "POST",
                url: "models/actions.php?action=reset",
                data: "username=" + $("#username").val() + "&email=" + $("#email").val() + "&loginActive=" + $("#formAction").val(),
                success: function (result) {
                    if (result == "OK") {
                        var errorMessage = "<div class='alert alert-success'>&#10003; A mail with information is sent to you.<br>Click the link inside to complete the password reset</div>";
                        $("#errorMessage").html(errorMessage).show();
                        $("#sendPasswordButton").css("display", "none");
                    } else {
                        var errorMessage = "<div class='alert alert-danger'>" + result +"</div>";
                        $("#errorMessage").html(errorMessage).show();
                    }
                }
            });
        }
    });


    $("#changePassButton").click(function () {
        $.ajax({
            type: "POST",
            url: "models/actions.php?action=changepass",
            data: "oldpassword=" + $("#oldpassword").val() + "&newpassword=" + $("#newpassword").val() + "&confirmpassword=" + $("#confirmpassword").val() + "&loginActive=" + $("#formAction").val(),
            success: function (result) {
                var errorMessage = "";
                if (result == "OK") {
                    errorMessage = "<div class='alert alert-success'>&#10003; Your password was changed</div>";
                    $("#errorMessage").html(errorMessage).show();
                } else {
                    errorMessage = "<div class='alert alert-danger'>" + result +"</div>";
                    $("#errorMessage").html(errorMessage).show();
                }
            }
        });
    });



</script>

</body>
</html>
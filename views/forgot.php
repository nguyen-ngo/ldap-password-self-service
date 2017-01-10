<div class="home-body">
    <div class="container">
        <div id="body-header">
            <p>Welcome to LDAP Password Management !</p>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-6" id="left-panel">
                <img style="width: 500px" src="images/computer-security.jpg">
            </div>
            <div class="col-md-6" id="right-panel">
                <div class="alert alert-warning" role="alert">
                    <h5>Please provide your informations</h5>
                </div>
                <div id="errorMessage"></div>
                <form>
                    <input type="hidden" id="formAction" name="formAction" value="reset">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-address-card-o" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="LDAP Username" aria-describedby="basic-addon1" required>
                    </div><br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-envelope-open-o" aria-hidden="true"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" aria-describedby="basic-addon1" required>
                    </div><br>
                    <div class="g-recaptcha" data-sitekey="<?php echo $gcaptcha_secret ?>"></div><br>
                    <button type="button" class="btn btn-primary" id="sendPasswordButton">Send new password</button>
                </form>
            </div>
        </div>
    </div>
</div>

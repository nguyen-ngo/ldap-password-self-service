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
                    <h5>Your log in is required</h5>
                </div>
                <div id="errorMessage"></div>
                <form>
                    <input type="hidden" id="formAction" name="formAction" value="login">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-address-card-o" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="LDAP Username" aria-describedby="basic-addon1" required>
                    </div><br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-key" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" aria-describedby="basic-addon1" required>
                    </div><br>
                    <?php enableGCaptcha($gcaptcha_enable); ?>
                    <button type="button" class="btn btn-primary" id="authenticateButton">Authenticate</button>
                </form>
                <br>
                <p><a href="?type=forgot">Forgot your password?</a></p>
            </div>
        </div>
    </div>
</div>
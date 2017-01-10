<div class="home-body">
    <div class="container">
        <div id="body-header"><?php showGreeting(); ?></div>
        <div class="row">
            <div class="col-md-6" id="left-panel">
                <h5 class="alert alert-danger">Your password should respect the following constraints:</h5>
                <div id="passwordPolicy">
                    <p><span>&#9745;</span> Minimal length: 8</p>
                    <p><span>&#9745;</span> Maximal length: 24</p>
                    <p><span>&#9745;</span> Minimal lower character: 1</p>
                    <p><span>&#9745;</span> Minimal upper character: 1</p>
                    <p><span>&#9745;</span> Minimal digit: 1</p>
                    <!-- <p><span>&#9745;</span> Forbidden character: @, %</p> -->
                    <p><span>&#9745;</span> Your new password may not be the same as your old password</p>
                </div>
            </div>
            <div class="col-md-6" id="right-panel">
                <div class="alert alert-warning" role="alert">
                    <h6>Enter your password and choose a new one</h6>
                </div>
                <div id="errorMessage"></div>
                <form>
                    <input type="hidden" id="formAction" name="formAction" value="changepass">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-key" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Your current Password" aria-describedby="basic-addon1" required>
                    </div><br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-key" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="New Password" aria-describedby="basic-addon1" required>
                    </div><br>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-key" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="Confirm new Password" aria-describedby="basic-addon1" required>
                    </div><br>
                    <button type="button" class="btn btn-primary" id="changePassButton">Change</button>
                </form>
            </div>
        </div>
    </div>
</div>
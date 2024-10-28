$(document).ready(function(){

    $("#btn_login").click(function(){
        $("#modalLogin").modal("hide");
        DoLogin();
    });
});

function DoLogin(){
    var formData = $("#form_login").serialize();
    $("#form_login")[0].reset();

    var promise = login.doLogin(formData);
    promise.done(function(json){
        if( json.success ) {
            window.location.replace("user.php");
        } else {
            ShowMessage("Login error",json.message);
        }
    });
}

function ShowMessage( title, body ) {
    $("#modalMessage .modal-title").html(title);
    body = "<p>"+body+"</p>";
    $("#modalMessage .modal-body").html(body);
    $("#modalMessage").modal("show");
}
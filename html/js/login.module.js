var login = (function(ajaxModule){
    "use strict";
    
    var _submit = ajaxModule.ajaxSubmit;
    var _xhrObjects = [];
    var baseUrl = "";
    
    function doLogin(data){
        var deferred = new $.Deferred();
        
        /*
        var data = { 
            "org_id":iOrgId
        };
        */
        
        _submit(
            "POST",
            _xhrObjects["doLogin"],
            baseUrl+"api/login.php",
            data,
            function(responseData){
                deferred.resolve(responseData);
            },
            function(error){
                deferred.reject(error);
            }
        );
        
        return deferred.promise();
    }

    return {
        doLogin:        doLogin
    };

})(ajaxModule);
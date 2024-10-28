var organization = (function(ajaxModule){
    "use strict";

    var _submit = ajaxModule.ajaxSubmit;
    var _xhrObjects = [];
    var baseUrl = "";

    function getOrganizationsForUser(iOrgIds,cPerms){
        var deferred = new $.Deferred();
        var data = { 
            "org_ids":iOrgIds,
            "perms":cPerms
        };

        _submit(
            "GET",
            _xhrObjects["getOrganizationsForUser"],
            baseUrl+"api/organizations.php",
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
        getOrganizationsForUser:        getOrganizationsForUser
    };

})(ajaxModule);
var campaign = (function(ajaxModule){
    "use strict";
    
    var _submit = ajaxModule.ajaxSubmit;
    var _xhrObjects = [];
    var baseUrl = "";
    
    function getCampaignsForUser(iOrgId){
        var deferred = new $.Deferred();
        var data = { 
            "org_id":iOrgId
        };
        
        _submit(
            "GET",
            _xhrObjects["getCampaignsForUser"],
            baseUrl+"api/campaigns.php",
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
        getCampaignsForUser:        getCampaignsForUser
    };

})(ajaxModule);
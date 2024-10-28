var ajaxModule = (function(){
    "use strict";

    //private vars

    //private functions
    function _log( log ){
        if( typeof console != "object" )
            return;

        console.log( log );
    }

    function _error(status, error){
        if( typeof console != "object" )
            return;

        console.error( error );
        console.error( status );
    }

    function _getIndex(arr,val){
        console.log(val);
        for(var ii=0;ii<arr.length;ii++)
        {
            if(arr[ii]===val)
                return ii;
        }

        return null;
    }

    //public vars


    //public functions
    function ajaxSubmit(type,xhr,url,data,onDone,onFail){
        if( xhr )
        {
            _error( "AjaxModule aborting previous request:" );
            _log(xhr);
            xhr.abort();
        }

        xhr = $.ajax({
            url: url,
            type: type,
            data: data
        });
        
        if( xhr.onDone != null ){
            _error( "AjaxModule stepping on previous success callback:" );
            _log( xhr.onDone );
        }
        
        xhr.onDone = function( json ){
            onDone( json );
        };

        xhr.onFail = function(){
            onFail();
        };

        xhr.done(function(response){
            var json_response = null;
            try{
                json_response = $.parseJSON( response );
            }
            catch( error ){
                _error( "AjaxModule Error", error );
                _log( response );
            }
            
            if( json_response != null )
            {
                if( typeof xhr.onDone === "function" )
                {
                    xhr.onDone( json_response );
                }
            }
        }).fail(function(jqXHR, status, error)
        {
            if( typeof xhr.onFail === "function" )
            {
                xhr.onFail();
            }
        }).always(function(){
            xhr = null;
        });
    }

    return {
        ajaxSubmit: ajaxSubmit
    }

})();
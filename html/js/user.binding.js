$(document).ready(function(){

    $("#btn_campaigns").click(function(){
        ShowCampaigns(this);
    });
    $("#btn_organizations").click(function(){
        ShowOrganizations(this);
    });
    $("#btn_accountInfo").click(function(){
        ShowAccountInfo(this);
    });

    $("#btn_campaigns").click();
});

function ShowCampaigns(button){
    UpdateActiveTab( $(button).closest("li") );
    HideOldContent();
    ShowDownloadSpinner("#page-content");

    var promise = campaign.getCampaignsForUser();
    promise.done(function(json){
        if( json.success ) {
            ShowResults( json, "#campaignTemplate" );
        } else {
            console.log("nay");
        }
    });
    promise.fail(function(error){});
    promise.always(function(json){});
}

function ShowOrganizations(button){
    UpdateActiveTab( $(button).closest("li") );
    HideOldContent();
    ShowDownloadSpinner("#page-content");

    var promise = organization.getOrganizationsForUser();
    promise.done(function(json){
        if( json.success ) {
            ShowResults( json, "#organizationTemplate" );
        } else {
            console.log("nay");
        }
    });
    promise.fail(function(error){});
    promise.always(function(json){});
}

function ShowResults( result_json, templateId ) {
    var template = $("#tableTemplate").clone();
    $(template).attr("id","resultsTable");
    $(template).find("tbody").attr("id","resultsTableBody");
    $("#page-content").html(template);
    
    ParseResultsRow(result_json.results, templateId, "#resultsTableBody");
}

function ParseResultsRow( results, templateId, target ) {
    var template = $(templateId).html();
    var compiled = Handlebars.compile(template);

    for(var ii = 0; ii < results.length; ii++) {
        var html = compiled( results[ii] );
        $(target).append(html);
    }
}



function ShowAccountInfo(button){
    UpdateActiveTab( $(button).closest("li") );
    HideOldContent();
    ShowDownloadSpinner("#page-content");

    //some kind of crazy form for updating user data goes here
    $("#page-content").html("<h2>This page intentionally left blank</h2>");
}
function HideOldContent(){
    $("#page-content").html("");
}
function UpdateActiveTab(pCurrentTab){
    $(".active").toggleClass("active");
    $(pCurrentTab).toggleClass("active");
}
function ShowDownloadSpinner(containerTag){
    $(containerTag).html('<div><p class="loadingZone"><i class="fa fa-spinner fa-spin"></i> Loading</p></div>');
}
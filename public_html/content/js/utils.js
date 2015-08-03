var utilsModule;

function UtilsModule() {
    this.host = window.location.host;
}

UtilsModule.prototype = {
    getApiUrl: function(apiUrl, queryString) {
        if (queryString) {
            return  apiUrl + '?' + queryString;
        }
        return apiUrl;
    },

    createAjaxRequest : function(self, url, query, data, ok_callback, fail_callback) {
        queryString = $.param(query);
        url = this.getApiUrl(url, queryString);
        var request = $.ajax({
            method:    'GET',
            url:       url,
            dataType:  'json',
            data:      data
        });

        request.success(function(response) {
                ok_callback(self, response);
            }
        );

        request.fail(function(response) {
            fail_callback(self, response);
        });

    }
}

$(function(){
    utilsModule = new UtilsModule();
});
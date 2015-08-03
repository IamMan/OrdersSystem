var utilsModule;

function UtilsModule() {
    this.host = window.location.host;
}

UtilsModule.prototype = {
    getApiUrl: function(apiUrl) {
        if (apiUrl[0] == '/') {
            return this.host +  apiUrl;
        } else {
            return this.host + '/' + apiUrl;
        }
    },

    createAjaxRequest : function() {
        var request = $.ajax({
            method:    this.opt.createApiMethod,
            url:       this.opt.createApiUri,
            dataType:  this.opt.createApiDataType,
            data:      formData
        });
    }
}

$(function(){
    utilsModule = new UtilsModule();
});
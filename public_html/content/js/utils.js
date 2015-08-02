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
    }
}

$(function(){
    utilsModule = new UtilsModule();
});
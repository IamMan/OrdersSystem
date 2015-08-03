var lifeTimeListModule;

function LifeTimeList() {
    var self = this;

    self.opt = {
        getNewApiUrl : "/api/orders/new",
        getListApiUrl : "/api/orders/list",
        getDeletedListApiUrl :  "/api/orders/deleted",
        resolveOrderApiUrl :  "/api/orders/resolver"
    }
}

function createAjaxRequest() {

}

LifeTimeList.prototype = {


    getNew: function() {

    },

    getNewSuccess : function() {

    },

    getNewFail: function() {

    },

};

$(function() {
   lifeTimeListModule = new LifeTimeList();
});
var lifeTimeListModule;

function LifeTimeList() {
    var self = this;

    self.opt = {
        getNewApiUrl : "/api/v1/orders/new",
        getListApiUrl : "/public_html/api/v1/orders/list.php",
        getDeletedListApiUrl :  "/api/v1/orders/deleted",
        resolveOrderApiUrl :  "/api/v1/orders/resolver"

    };

    self.orders = $(".collection.orders");
    $("#load-new-button").click(function() {
        self.getList({});
    });
}

function order_to_li(self, order) {
    var order_li = "<li ";
    var button = '<button class="btn waves-effect waves-light o' + order.id + '" type="submit" name="action">Submit <i class="material-icons">send</i> </button>'
    $(document).on("click", ".o" + order.id, function(event) {
       console.log('resolve order with ' + order.id)
    });
    order_li += 'class="collection-item" ';
    order_li += '>' + order.title + button + '</li>';
    return order_li;
}


LifeTimeList.prototype = {


    getNew: function() {

    },

    getNewSuccess : function() {

    },

    getNewFail: function() {

    },

    getList: function(query) {
        utilsModule.createAjaxRequest(this, this.opt.getListApiUrl, query, {}, this.getListRequestSuccess, this.getListRequestFail);
    },

    getListRequestSuccess: function(self, response) {
        if (response.result == 'ok') {
            $.each(response.info.reverse(), function(index, order) {
                var order_li = order_to_li(self, order);
                self.orders.prepend(order_li);
            })
        } else {

        }
    },

    getListRequestFail: function(response) {

    }


};

$(function() {
   lifeTimeListModule = new LifeTimeList();
});
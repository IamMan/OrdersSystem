var lifeTimeListModule;

function LifeTimeList() {
    var self = this;

    self.opt = {
        getNewApiUrl : "/api/v1/orders/new",
        getListApiUrl : "/public_html/api/v1/orders/list.php",
        getDeletedListApiUrl :  "/api/v1/orders/deleted",
        resolveOrderApiUrl :  "/public_html/api/v1/order/resolver.php",

        firstButchSize : 100
    };

    self.orders = $(".collection.orders");
    $("#load-new-button").click(function() {
        self.getNewOrdersList({});
    });
}

function gen_button(order) {
    var button = '<a class="waves-effect waves-light btn secondary-content" >Resolve </a>';

    $(document).on("click", ".o" + order.id + "> a", function(event) {
        lifeTimeListModule.resolveOrder(order.id);
    });

    return button;
}

function order_to_li(self, order) {
    var order_li = "<div ";
    button = gen_button(order);
    order_li += 'class="collection-item o' + order.id + '"';
    order_li += '>' + order.title + ' for ' + order.price + '$' + button + '</div>';
    return order_li;
}

function remove_order_by_id_ok(order_id, price) {
    var order_div = $('.o' + order_id);
    var button = order_div.find('a');
    button.remove();
    order_div.append('<i class="small material-icons left">thumb_up</i>');
    order_div.append('You get ' + price);
    //order_div.hide("slide", { direction: "left" }, 1000);
    order_div.animate({width:'toggle'},350, function() {
        order_div.remove();
    });

}

function remove_order_by_id_error(order_id, errors) {
    var order_div = $('.o' + order_id);
    var button = order_div.find('a');
    button.remove();
    order_div.append('<i class="small material-icons left">thumb_down</i>');
    order_div.append();
    //order_div.hide("slide", { direction: "left" }, 1000);
    order_div.append(errors);
    order_div.animate({width:'toggle'},350, function() {
        order_div.remove();
    });
}

var orders_ids = [];

LifeTimeList.prototype = {

    getNew: function() {

    },

    getNewSuccess : function() {

    },

    getNewFail: function() {

    },

    getNewOrdersList: function(query) {
        utilsModule.createAjaxRequest(this, this.opt.getListApiUrl, query, {}, this.getListRequestSuccess, this.getListRequestFail);
    },

    getListRequestSuccess: function(self, response) {
        if (response.result == 'ok') {
            $.each(response.info.reverse(), function(index, order) {
                if ($.inArray(order.id, orders_ids) == -1) {
                    var order_li = order_to_li(self, order);
                    self.orders.prepend(order_li);
                    orders_ids.push(order.id);
                }
            })
        } else {

        }
    },

    getListRequestFail: function(response) {

    },

    resolveOrder: function(order_id) {
        utilsModule.createAjaxRequest(this, this.opt.resolveOrderApiUrl, {id : order_id}, {}, this.resolveOrderSuccess, this.resolveOrderFail)
    },

    resolveOrderSuccess: function(self, response, query) {
        if (response.result == 'ok') {
            remove_order_by_id_ok(response.info.id, response.info.price);
            orders_ids.slice(orders_ids.indexOf(response.info.id),1);
        } else {
            remove_order_by_id_error(query.id, response.errors.error);
            orders_ids.slice(query.id.indexOf(query.id),1);
        }
    },

    resolveOrderFail: function(self, response) {    },

    getOldOrdersList: function(query) {
        utilsModule.createAjaxRequest(this, this.opt.getListApiUrl, query, {}, this.getOldListRequestSuccess, this.getOldListRequestFail);
    },

    getOldListRequestSuccess: function(self, response) {
        if (response.result == 'ok') {
            $.each(response.info, function(index, order) {
                if ($.inArray(order.id, orders_ids) == -1) {
                    var order_li = order_to_li(self, order);
                    self.orders.append(order_li);
                    orders_ids.unshift(order.id);
                }
            })
        } else {

        }
    },

    getOldListRequestFail: function(response) {

    },

    //TODO: add check for deleted

};

$(function() {
   lifeTimeListModule = new LifeTimeList();
});

$(document).ready(function() {
    lifeTimeListModule.getNewOrdersList({size : lifeTimeListModule.opt.firstButchSize});

    $(window).scroll(function(){
        //console.log($(window).scrollTop());
        //console.log($(document).height());
        //console.log($(window).height());
        if ($(window).scrollTop() == $(document).height()-$(window).height()){
            lifeTimeListModule.getOldOrdersList({to : orders_ids[0]});
        }
    });
});
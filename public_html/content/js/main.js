var lifeTimeListModule;

function LifeTimeList() {
    var self = this;

    self.opt = {
        getNewApiUrl: "api/v1/orders/last.php",
        getListApiUrl: "api/v1/orders/list.php",
        getDeletedListApiUrl: "api/v1/orders/deleted.php",
        resolveOrderApiUrl: "api/v1/order/resolver.php",

        firstButchSize: 100
    };

    self.orders = $(".collection.orders");
    self.last_update_span = $("#last_update");
    self.new_orders_span = $("#neworders");

    $("#load-new-button").click(function () {
        self.getNewOrdersList({from:orders_ids[orders_ids.length - 1]});
    });
}

function gen_button(order) {
    var button = '<a class="waves-effect waves-light btn secondary-content" >Resolve </a>';

    $(document).on("click", ".o" + order.id + "> row > div > a", function () {
        lifeTimeListModule.resolveOrder(order.id);
    });

    return button;
}

function order_to_li(order) {
    var order_li = "<div ";
    button = gen_button(order);
    order_li += 'class="collection-item o' + order.id + '">';
    order_li += "<row>";
    order_li += '<div class="col s6">';
    order_li += '<h5>' + order.title + '</h5>';
    order_li += "<p class='truncate'>" + order.description +'</p> ';
    order_li += '</h5> FOR: ' + order.price + ' $ </h5>';
    order_li += "</div>";
    order_li += '<div class="col s6 results">'+ button + '</div>';

    order_li += "</row>";
    return order_li;
}

function remove_order_by_id_ok(order_id, price) {
    var order_div = $('.o' + order_id);
    var results = order_div.find('.results');
    order_div.find('.active').remove();
    results.append('<h4 class="teal-text right-align"> SUCCESS </h4>');
    results.append('<h5 class="teal-text right-align">You cash: ' + price + ' $</h5>');
    //order_div.hide("slide", { direction: "left" }, 1000);
    window.setTimeout(function() {
        order_div.slideUp(700, function () {
            order_div.remove();
        });
    }, 2500);

}

function remove_order_by_id_error(order_id, errors) {
    var order_div = $('.o' + order_id);
    var results = order_div.find('.results');
    order_div.find('.active').remove();
    results.append('<h4 class="red-text right-align"> FAIL </h4>');
    results.append('<h5 class="red-text right-align">' + errors +'</h5>');
    //order_div.hide("slide", { direction: "left" }, 1000);
    window.setTimeout(function() {
        order_div.slideUp(700, function () {
            order_div.remove();
        });
    }, 2500);
}

var orders_ids = [];
var is_first_time_load = true;
LifeTimeList.prototype = {

    getLast: function () {
        var query = {};
        utilsModule.createAjaxRequest(this, this.opt.getNewApiUrl, query, {}, this.getLastSuccess, this.getLastFail);
    },

    getLastSuccess: function (self, response) {
        var neworders = $("#neworders");
        if (response.result == 'ok')
        {
            //self.last_orders_span(response.info);
            var newCount = response.info - orders_ids[orders_ids.length - 1];
            if (newCount > 100) {
                neworders.text('100+ new orders ready for you');
            } else if (newCount > 0){
                neworders.text(newCount + ' new orders ready for you');
            } else {
                neworders.text('');
            }

        }
         else {
            neworders.text('Error');
        }

    },

    getLastFail: function () {
        var neworders = $("#neworders");
        self.text('Error');
    },

    getNewOrdersList: function (query) {
        utilsModule.createAjaxRequest(this, this.opt.getListApiUrl, query, {}, this.getListRequestSuccess, this.getListRequestFail);
    },

    getListRequestSuccess: function (self, response) {
        if (response.result == 'ok')
        {
            self.last_update_span =
            response.info.sort(function (o1, o2){ return o1.id - o2.id});
            $.each(response.info, function (index, order) {
                if ($.inArray(order.id, orders_ids) == -1) {
                    var order_li = order_to_li(order);
                    self.orders.prepend(order_li);
                    orders_ids.push(order.id);
                }
            });

            var nowTime = new Date();
            $("#last_update").text(nowTime.toLocaleString());

            if (is_first_time_load) {
                is_first_time_load = false;
            }
        } else {

        }
    },

    getListRequestFail: function (response) {

    },

    resolveOrder: function (order_id) {
        var self = this;
        var order_div = $(".o" + order_id);
        order_div.find("a").remove();
        order_div.append('<div class="preloader-wrapper active right"><div class="spinner-layer spinner-green-only"><div class="circle-clipper right"><div class="circle"></div></div></div></div>  ');
        window.setTimeout(function() {
            utilsModule.createAjaxRequest(self, self.opt.resolveOrderApiUrl, {id: order_id}, {}, self.resolveOrderSuccess, self.resolveOrderFail);
        }, 700);


    },

    resolveOrderSuccess: function (self, response, query) {
        if (response.result == 'ok') {
            remove_order_by_id_ok(response.info.id, response.info.price);
            orders_ids.slice(orders_ids.indexOf(response.info.id), 1);
        } else {
            remove_order_by_id_error(query.id, response.errors);
            orders_ids.slice(query.id.indexOf(query.id), 1);
        }
    },

    resolveOrderFail: function (self, response) {
    },

    getOldOrdersList: function (query) {
        utilsModule.createAjaxRequest(this, this.opt.getListApiUrl, query, {}, this.getOldListRequestSuccess, this.getOldListRequestFail);
    },

    getOldListRequestSuccess: function (self, response) {
        if (response.result == 'ok') {
            response.info.sort(function (o1, o2){ return parseInt(o2.id) - parseInt(o1.id)});
            $.each(response.info, function (index, order) {
                if ($.inArray(order.id, orders_ids) == -1) {
                    var order_li = order_to_li(order);
                    self.orders.append(order_li);
                    orders_ids.unshift(order.id);
                }
            })
        } else {

        }
    },

    getOldListRequestFail: function (response) {

    },

    //TODO: add check for deleted
    getDeletedOrdersList: function (query) {
        utilsModule.createAjaxRequest(this, this.opt.getDeletedListApiUrl, query, {}, this.getDeletedListRequestSuccess, this.getDeletedListRequestFail);
    },

    getDeletedListRequestSuccess: function (self, response) {
        if (response.result == 'ok') {
            $.each(response.info, function (index, order_id) {
                var index = orders_ids.indexOf(order_id);
                if (index != -1) {
                    remove_order_by_id_error(query.id, response.errors.error);
                    orders_ids.slice(query.id.indexOf(query.id), 1);
                }
            });
        }
        else {

        }
    },

    getDeletedListRequestFail: function (response) {

    }

};

$(function () {
    lifeTimeListModule = new LifeTimeList();
});

$(document).ready(function () {
    lifeTimeListModule.getNewOrdersList({size: lifeTimeListModule.opt.firstButchSize});



    $(window).scroll(function () {
        if ($(window).scrollTop() + 5 > $(document).height() - $(window).height()) {
            lifeTimeListModule.getOldOrdersList({to: orders_ids[0]});
        }
    });

    (function(){
        lifeTimeListModule.getLast();
        setTimeout(arguments.callee, 10000);
    })();


});

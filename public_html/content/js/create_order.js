var orderCreatorModule;

$(document).ready(function(){
    $('.parallax').parallax();
});

function OrderCreator() {
    var self = this;
    
    self.opt = {
        formSelector: '#order_form',
        formSubmit: ".submit",
        formClear: ".clear",

        createApiMethod: "POST",
        createApiDataType: "json",
        createApiUri: "api/v1/order/create"
    };

    self.init();
    self.form = $(self.opt.formSelector);
    self.form.submit(function(event) {
        self.sendOrder();
        event.preventDefault();
    });



}

OrderCreator.prototype = {
    init: function(){

    },

    clearForm: function() {
        var title = $('input[name=title]')
        title.val("");
        title.removeClass("valid");
        title.removeClass("invalid");
        var textarea = $('textarea[name=description]');
        textarea.val("");
        textarea.removeClass("valid");
        textarea.removeClass("invalid");
        var price = $('input[name=price]');
        price.val("");
        price.removeClass("valid");
        price.removeClass("invalid");
    },

    createOrderObject: function() {
        var orderObject = {
            'title' : $('input[name=title]').val(),
            'description' : $('textarea[name=description]').val(),
            'price' : $('input[name=price]').val()
        };
        return orderObject;
    },

    validateResponse: function(response) {
        if (response.result == "ok") {
            return true;
        } else {
            return false;
        }
    },

    responseSuccess: function(response) {
        $(".createorder").addClass("hide");
        $(".created").removeClass("hide");
    },

    responseFail: function(self, response) {
        $.each(response.errors, function(field, error) {
            var input = self.form.find("*[name="+field+"]");
            if (error == null) {
                input.addClass('valid');
            } else{
                var label = self.form.find("label[for="+field+"]");
                input.addClass('invalid');
                //label.attr('data-error', error);
            }
        });
    },

    sendOrder: function() {
        var self = this;
        var formData = {
            'order' : this.createOrderObject()
        };

        var request = $.ajax({
            method:    this.opt.createApiMethod,
            url:       this.opt.createApiUri,
            dataType:  this.opt.createApiDataType,
            data:      formData
        });

        request.success(function(response) {
            if (self.validateResponse(response) == true) {
                self.responseSuccess(self, response);
            } else {
                self.responseFail(self, response);
            }
        });
        request.fail(function(response) {

        });
    }
};


$(function(){
    orderCreatorModule = new OrderCreator();
    $(".addorder").on("click", function() {
        $(".createorder").removeClass("hide");
        $(".created").addClass("hide");
        orderCreatorModule.clearForm();
    });

    $(".clear").on("click", function() {
        orderCreatorModule.clearForm();
    });

});

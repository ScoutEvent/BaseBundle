jQuery(function ($) {
    function validateEmail(email) { 
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    } 

    var email_processed = false;
    
    function reset_form() {
        email_processed = false;

        $("#login_form input.email").show().animate({
                opacity: 1,
                left: 100
            }, function () {
                $(this).css("left", 0).focus();
            });

        $("#login_form input.password").animate({
                opacity: 0,
                left: 100
            }, function () {
                $(this).hide().prop("disabled", false);
            });
        
        $("#login_form input[type='submit']").prop("disabled", false);
    }
    
    function do_login() {
        $(this).prop("disabled", true);
        $("#login_form input[type='submit']").prop("disabled", true);
        $.ajax({
            url: $("#login_form").attr("action"),
            data: {
                _username: $("#login_form input.email").val(),
                _password: $("#login_form input.password").val()
            },
            type: 'post'
        }).done(function (r) {
            location.href = r;
        }).fail(function () {
            reset_form();
        });
    }
    
    function process_email() {
        var val = $(this).val();
        if (!validateEmail(val)) {
            if ('errorTimeout' in this) {
                clearTimeout(this.errorTimeout);
            }
            var that = $(this);
            that.addClass("error");
            this.errorTimeout = setTimeout(function () {
                that.removeClass("error");
            }, 500);
            return;
        }
        
        email_processed = true;
        
        $(this).css({
                position: "relative",
                left: 100
            }).animate({
                opacity: 0,
                left: 0
            }, function () {
                $(this).hide();
            });

        $("#login_form input.password").css({
                position: "relative",
                left: 100,
                opacity: 0
            }).show().animate({
                opacity: 1,
                left: 0
            }, function () {
                $(this).focus();
            });
    }

    $("#login_form").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (email_processed == false) {
            process_email.call($("#login_form input.email").get(0));
        } else {
            do_login.call($("#login_form input.password").get(0));
        }
        
        return false;
    });
    
    $("#login_form input.email").keydown(function (event) {
        if (event.which != 13) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        process_email.call(this);
    }).focus();
    
    $("#login_form input.password").hide().keydown(function (event) {
        if (event.which != 13) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        do_login.call(this);
    });
});

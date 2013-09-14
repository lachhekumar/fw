// game will hold the gamification struture and othere details
game = {
    
    
    _callback: "{callback}",
    _url: "{url}",
    _token: "{token}",
    
    load: function(options) {

            if((typeof options.url) == "undefined") { console.log("Please provide url"); return false; }
            if((typeof options.type) == "undefined") { options.type = "GET"; }
            if((typeof options.data) == "undefined") { options.data = {}; }
            if((typeof options.callback) == "undefined") { options.callback = function() {}; }

            // adding secret to the request
            options.data.__secret = __secret;
            options.data.__token = this._token;
           
            $.ajax({
                type: options.type,
                url: this._url + options.url + this._callback,
                dataType: "json",
                data: options.data
            }).done($.proxy(function(callback,response) {
                callback(response)
            },this,options.callback));        
    }
    
};


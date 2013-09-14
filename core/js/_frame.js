// game will hold the gamification struture and othere details
$__currentdate = "{now}";
_callbackm      = [];
game = {
    
    
    _callback: "{callback}",
    _url: "{url}",
    _token: "{token}",
    
    // query specific models
    _fields: new Array(),           // Stores the field details
    _limit: 100,                    // Maximum limit of the script to fetch records
    _page: 0,                       // First page that need to be loaded
    _where: new Array(),            // condition for filteration
    _parameter : '',
    _agg : Array(),
    _order : Array(),
    _join : Array(),
    _operation : Array(),
    _total: 0,    
    _callbackm: [],
    
    
    refresh: function($tablename,callback) {
        _callbackm[$tablename] = callback;
    },
    
    reload: function() {
        this.load({
            url: "_track/index.json",
            data: {date: $__currentdate},
            callback: $.proxy(function(response) {
                if(response.result.length > 0) {
                    $__currentdate = response.result[0].currentdate;
                    
                    for($count = 0;$count < response.result.length;$count++) {
                        _callbackm[response.result[$count].tablename]();
                    }
                }
                
                setTimeout(function() {
                    game.reload();
                },10000);
            },this)
        });        
    },
    
    clear: function() {
        this._fields= new Array();           // Stores the field details
        this._limit= 100;                    // Maximum limit of the script to fetch records
        this._where= new Array();            // condition for filteration
        this._parameter = '';
        this._agg = Array();
        this._order = Array();
        this._join = Array();
        this._operation = Array();
        this._page = 0;

    },

    
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
    },
    
    

    // get details from the serevra nnd fill in the collection
    select: function($tablename,$callback) {
        
        this.load({
            url: '_model/' + $tablename + ".json",
            data: {
                where: this._where,
                fields: this._fields,
                orderby: this._order,
                join: this._join,
                groupby: this._agg,
                parameter: this._parameter,
                limit: (parseInt(this._page) * this._limit) + ", " +this._limit,
            },
            callback: $callback            
        });


    },
    
    
    // get details from the serevra nnd fill in the collection
    insert: function($tablename,$data,$callback) {
        
        this.load({
            url: '_model/' + $tablename + ".json",
            data: $data,
            type: 'PUT',
            callback: $callback
        });


    },


    
    // get details from the serevra nnd fill in the collection
    remove: function($tablename,$data,$callback) {
        
        this.load({
            url: '_model/' + $tablename + ".json?condition=" + this._where.join(", "),
            data: {
                where: this._where,
                parameter: this._parameter,
            },
            type: 'DELETE',
            callback: $callback
        });


    },

    // get details from the serevra nnd fill in the collection
    update: function($tablename,$data,$callback) {
        
        this.load({
            url: '_model/' + $tablename + ".json",
            data: {
                where: this._where,
                parameter: $data
            },
            type: 'PATCH',
            callback: $callback
        });


    },
    
    
    page: function($count) {
        this._page = $count;
    },

    // let us write method to fetch records from database with various 
    // different parameter
    join: function($table,$condition,$type) {

        count = this._join.length;
        mytable = "join[" + count+ "]['table']=" +$table;
        mytable += "join[" + count+ "]['condition']=" +$condition;
        mytable += "join[" + count+ "]['type']=" +$type;

        this._join[this._join.length] = mytable;
    },

    // If you want to get the specific fields from the result set
    fields: function($name) {
        this._fields[this._fields.length] = $name;
    },

    // send condition parameter to server
    condition: function($field,$value) {
        if((typeof $value) == "undefined") {
            // user has provided direct where clause
            this._where[this._where.length] = $field;
        } else {
            $value = $value + "";
            // let us create the new condition for processing
            this._where[this._where.length] = $field + "=:" + $field;
            this._parameter =  this._parameter + "&parameter[" + $field+ "]=" + $value;
        }
    },

    // we need to order the result set
    orderby: function($field,$type) {
        if((typeof $type) == "undefined") {
            $type = "ASC";
        }

        this._order[this._order.length] = $field + " " +$type;
    },

    // Somtetimwe requrie to use the SUM,AVG, MIN, MAX functionalty of sql
    groupby: function($field) {
        // maintain arrigate function
        this._agg[this._agg.length] = $field;
    },

    // pass speicifc value to the query function this will prevent
    // major sql injection issue
    value: function($field,$value) {
        this._parameter =  this._parameter + "&parameter[" + $field+ "]=" + $value;
        this._operation[$field] = $value;
    },

    // set the number of records the script should fetch at atime
    limit: function($limit) {
        this._limit = $limit;
    }
    
    
    
};


$(function () {
    game.reload();
})



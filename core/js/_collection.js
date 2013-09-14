// create collection for login
// select       -> fire query
// fields       -> select fields that need to select
// condition    -> implements where condition
// orderby      -> create order by clause
// cal          -> use aggrigate function
// value        -> value for the quer parameter
// limit        -> limit for sql query
// page         -> current number that need to be loaded
// join         -> is also completed

define(["underscore","backbone","backbone/models/{model}"],function(_, Backbone, Models) {

    var Collection = Backbone.Collection.extend({
        model:          Models,
        _url: "models/{model}.json",
        urldata: "",

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
        
        // URL to fetch data from server
        url:            $.proxy(function () { return this.urldata; },this),
        
        // init function
        initialize:     function() {
            // Any initlization code
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
            
        },
        
        search: function() {
            
        },


        // Custom records parsing logic
        parse: function(response) {
            // Pushing individual rows into the database
            for(index in response.result){
                this.push(response.result[index]);
            }
            this._total = response.count;
            return  this.models;
        },
        

        // get details from the serevra nnd fill in the collection
        select: function() {
            this.url = this._url + "?__type=select&limit=" + this._limit +"&page="+this._page;

            // we have custom field to be displayed 
            if(this._fields.length > 0) {
                this.url = this.url + "&fields=" + this._fields.join(", ");
            }

            if(this._agg.length > 0) {
                this.url = this.url + "&aggrigate=" + this._agg.join(", ");
            }


            // add where condition to system
            if(this._where.length > 0) {
                this.url = this.url + "&where=" + this._where.join(" AND ");
            }

            if(this._order.length > 0) {
                this.url = this.url + "&order=" + this._order.join(", ");
            }

            if(this._join.length > 0) {
                this.url = this.url + "&" + this._join.join("&");
            }

            // Adding parameter to the actual request
            if(this._parameter != '') {
                this.url = this.url + this._parameter;
            }
            this.fetch();
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
        cal: function($field) {
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
        },

        // set the current working page
        page: function($page) {
            this._page = $page;
        },

        // Insert new records into database
        insert: function($parameter) {
            // lets check for the passed parameter
            if((typeof $parameter) != "undefined") {
                this._operation = $parameter;
            }

            // creating the object to insert records into database
            $insert = new this.model;
            $insert.url = this._url + "?__type=insert";
            $insert.save(this._operation,{
                success: $.proxy(function(model, response, options) {
                     this.push(response.result);
                },this)
            });

            
        },

        // Insert new records into database
        update: function($id,$parameter) {
            // lets check for the passed parameter
            if((typeof $parameter) != "undefined") {
                this._operation = $parameter;
            }

            // creating the object to insert records into database
            this.at($id).set($parameter);
            this.at($id).save();
        },

        // Delete user from database
        del: function($id) {
            // getting point of view

            this.at($id).destroy({
                success: $.proxy(function(model, response) {
                },this)
            });
        }

    });


    return new Collection;

});
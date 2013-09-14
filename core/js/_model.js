define(['jquery','underscore','backbone'],function ($, _,Backbone) {

    // Manage backbone model for the given tables
    // primary key of the table will be populated to the id for making update
    var Model = Backbone.Model.extend({
        idAttribute:"{id}"
    });

    
    return Model;
});
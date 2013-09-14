$(function() {
    //game.load({url: "_model/users.json"});
    
    
    game.clear();
    game.condition("username='lachhekumar'");
    game.select("users",function(response) {});
    

    game.clear();
    game.condition("username='lachhekumar'");
    game.update("users",{
      firstname:'Lachhekumar123',
      lastname:'Nadar',
      status:'1'
    },function(response) {});
    
    
    game.clear();
    game.condition("users_id='6'");
    game.remove("users",function(response) {});

    
})


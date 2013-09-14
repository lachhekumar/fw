$__last_id = 0;

$(function() {
    //game.load({url: "_model/users.json"});
    
    
    game.clear();
    game.orderby("users_id", "asc");
    game.select("users",function(response) {
        for($count = 0;$count < response.result.length ;$count++) {
            $list = "<ul>";
            $list += "   <li>" + response.result[$count].firstname +"</li>";
            $list += "   <li>" + response.result[$count].lastname +"</li>";
            $list += "</ul>";
            $(".userslist").append($list);
            
            $__last_id = response.result[$count].users_id;
        }
    });
    
    game.clear();
    game.condition("filename='index'")
    game.select("pages",function(response) {
        $(".displaycontent").html(response.result[0].pagecontent);
    });    


    $(".userinsert .submit").click(function () {
        
        validateusername({username: $(".userinsert .username").val()},function(response) {
            if(response.result == 0) {
                
                game.insert("users",{
                    firstname: $(".userinsert .firstname").val(),
                    lastname: $(".userinsert .lastname").val(),
                    username: $(".userinsert .username").val(),
                    password: $(".userinsert .password").val()
                },function(response) {
                });  
                
            } else {
                alert("Username already avaliable");
            }
        });

        
        
        
    });
    
    
    
    game.refresh("users",function() {
        
        game.clear();
        game.condition("users_id > " + $__last_id);
        game.select("users",function(response) {
            for($count = 0;$count < response.result.length ;$count++) {
                $list = "<ul>";
                $list += "   <li>" + response.result[$count].firstname +"</li>";
                $list += "   <li>" + response.result[$count].lastname +"</li>";
                $list += "</ul>";
                $(".userslist").append($list);
            }
        });        
    });
    
})


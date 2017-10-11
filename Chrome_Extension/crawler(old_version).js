function passVal(passingData){
    var curl = passingData;
    var request = $.ajax({
        type: "POST",
        url: "http://localhost/db1/script.php",
        data: {"currentUrl":curl},
        dataType: "html"
    })
    request.done(function(msg) {
            if (msg=="invalid"){
                //alert ("The URL is not in database!");
                document.getElementById("message").innerHTML="This URL is not in database!";
            }
            else{
                //alert(msg);
                var data = JSON.parse(msg);
                var ul = document.createElement('ul');
                ul.setAttribute('class','accordion');
                document.getElementById("message").appendChild(ul);
                //alert(data.Input);
                for (var wtype in data){
                    var g = document.createElement('li');
                    ul.appendChild(g);
                    var h3 = document.createElement('h3');
                    g.appendChild(h3);
                    h3.innerHTML=wtype;
                    for (var j in data[wtype]){
                        var t = document.createElement('li');//p
                        g.appendChild(t);
                        t.innerHTML=data[wtype][j];
                    }
                }
                $(document).ready(function() {
                    $('h3').click(function(){
                        $(this).toggleClass('active');
                        $(this).siblings().not(':animated').slideToggle();
                    });
                });
            }

    });
    request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
    });

}
chrome.tabs.query({currentWindow: true, active: true}, function(tabs){
    var tab_url = tabs[0].url;
    passVal(tab_url);

});


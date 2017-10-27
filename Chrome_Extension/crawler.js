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
            document.getElementById("message").innerHTML="No Warning sentences for this page!";
        }
        else{
            //document.getElementById("message").innerHTML=msg;

            var data = JSON.parse(msg);
            //var length = Object.keys(data).length;
            var ul = document.createElement('ul');
            ul.setAttribute('class','collapsibleList');
            document.getElementById('message').appendChild(ul);
            for (var EnName in data){
                //var EnNameLen = data.length;
                var li1 = document.createElement('li');
                ul.appendChild(li1);
                var a1 = document.createElement('a');
                a1.setAttribute('class','entity-name');
                //a1.setAttribute('href','#');
                a1.innerHTML=EnName;
                li1.appendChild(a1);
                var ul1 = document.createElement('ul');
                li1.appendChild(ul1);
                for (var wType in data[EnName]){
                        var li2 = document.createElement('li');
                        ul1.appendChild(li2);
                        var a2 = document.createElement('a');
                        li2.appendChild(a2);
                        //a2.setAttribute('href','#');
                        a2.setAttribute('class','warning-type');
                        a2.innerHTML=wType;
                        var ul2 = document.createElement('ul');
                        li2.appendChild(ul2);
                        for (var windex in data[EnName][wType]){
                            var wtext_url = data[EnName][wType][windex];
                            for (var wtext in wtext_url){
                                var url = wtext_url[wtext];
                                var li3 = document.createElement('li');
                                ul2.appendChild(li3);
                                var a3 = document.createElement('a');
                                a3.setAttribute('class','warning-text');
                                a3.setAttribute('href',url);
                                a3.setAttribute('target',"_blank");
                                a3.innerHTML = wtext;
                                li3.appendChild(a3);
                            }
                        }
                }
            }
            $(function(){
                $(".entity-name").click(function () {
                    $(this).parent().find('ul').slideToggle();
                })
                $(".warning-type").click(function () {
                    $(this).parent().find('ul').slideToggle();
                })
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
 var errors = false;
 var xmlHttp = false;
 
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
try
{
    xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e)
{
    try
    {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch (e2)
    {
        xmlHttp = false;
    }
}
@end @*/

if (!xmlHttp && typeof XMLHttpRequest != 'undefined')
{
    xmlHttp = new XMLHttpRequest();
}

window.onkeydown=function(e)
{
    if(e.keyCode==32)
    {
        return false;
    }
};

function login_chk()
{
    var login = document.getElementById('user_login').value;

    var url = "../user/loginchk?login="+login;
    xmlHttp.open("GET", url, true);

    xmlHttp.onreadystatechange = update_login;
    xmlHttp.send(null);
}

function update_login()
{
    if (xmlHttp.readyState == 4)
    {
        var el_id = 'user_login';
        var response = xmlHttp.responseText;

        if (response == 1)
        {
            document.getElementById(el_id).style.backgroundColor = "#ffcccc";
            document.getElementById(el_id+'_error').innerHTML = "<div class='error'> Toks varotojo vardas jau užregistrotas</div>";
            errors = true;
        }
        else
        {
            document.getElementById(el_id).style.backgroundColor = "#ffffff";
            document.getElementById(el_id+'_error').innerHTML = "";
            if (errors == true)
                errors = true;
            else errors = false;
        }
    }
}


function register_user()
{
    //alert ("nr 1");
    errors = false;
    login_chk();
    lenght_check('first_name', 3);
    lenght_check('last_name', 3);
    email_check('user_mail');
    
    lenght_check('password', 6);
    pass_confirm('password', 'user_pass_confirm');
    
    if (errors == false)
    {
        var url = "../user/process";
        var data = "user_login="     +document.getElementById('user_login').value+
                   "&first_name="     +document.getElementById('first_name').value+
                   "&last_name="      +document.getElementById('last_name').value+
                   "&user_place="     +document.getElementById('user_place').value+
                   "&user_mail="      +document.getElementById('user_mail').value+
                   "&user_tel="       +document.getElementById('user_tel').value+
                   "&user_pass="      +document.getElementById('password').value+
                   "&user_position="  +document.getElementById('user_position').value;
        xmlHttp.open("POST", url, true);

        //Send the proper header information along with the request
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp.setRequestHeader("Content-length", data.length);
        xmlHttp.setRequestHeader("Connection", "close");

        xmlHttp.onreadystatechange = registration_ok;
        xmlHttp.send(data);
       // alert(url+data);
    }
    
}

function registration_ok()
{
   // alert("response");
    if (xmlHttp.readyState == 4)
    {
        var response = xmlHttp.responseText;
        document.getElementById('main').innerHTML = response;
    }
}



function lenght_check(el_id, min_ln)
{
    var name = document.getElementById(el_id).value;
    var text;
    var pass_h = 1;
    switch (el_id)
    {
        case "first_name":
            text = "Minimalus vardo ilgis - 3 raidės";
        break;
        case "last_name":
            text = "Minimalus pavardės ilgis - 3 raidės";
        break;
        case "password":
        {
            text = "Minimalus slaptažodžio ilgis - 6 simboliai";
            if (name == "123456" || name == "asdfgh")
            {
                text = "Tokio slaptažodžio naudoti negalima.";
                pass_h = 0;
            }
        }
        break;
    }
    if (name.length < min_ln || pass_h == 0)
    {
        document.getElementById(el_id).style.backgroundColor = "#ffcccc";
        document.getElementById(el_id+'_error').innerHTML = "<div class='error'>"+ text +"</div>";
        errors = true;
    }
    else
    {
        document.getElementById(el_id).style.backgroundColor = "#ffffff";
        document.getElementById(el_id+'_error').innerHTML = "";
        if (errors == true)
                errors = true;
        else errors = false;
    }
}


function email_check(el_id)
{
    //validate email
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    var address = document.getElementById(el_id).value;
    if(reg.test(address) == false)
    {
        document.getElementById(el_id).style.backgroundColor = "#ffcccc";
        errors = true;
    }
    else
    {
        document.getElementById(el_id).style.backgroundColor = "#ffffff";
        if (errors == true)
                errors = true;
        else errors = false;
    }
}

function pass_confirm(pass_id, confirm_id)
{
    var pass = document.getElementById(pass_id).value;
    var confirm = document.getElementById(confirm_id).value;
    if (pass != confirm)
    {
        document.getElementById(confirm_id).style.backgroundColor = "#ffcccc";
        document.getElementById(confirm_id+'_error').innerHTML = "<div class='error'>Neteisingai pakartotas slaptažodis</div>";
        errors = true;
    }
    else
    {
        document.getElementById(confirm_id).style.backgroundColor = "#ffffff";
        document.getElementById(confirm_id+'_error').innerHTML = "";
        if (errors == true)
                errors = true;
        else errors = false;
    }
}




   








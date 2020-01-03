var xmlHttp = false;
var xmlHttp1 = false;
var xmlHttp2 = false;

//var ready = 0;
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

function find_user()
{
    document.getElementById('user_list').innerHTML = '<br /><img src="../images/ajax-loader.gif" alt="kraunasi..."/><br /> Kraunasi....';
    var url = "../admin/finduser";
    var data =  "name="     +document.getElementById('src_name').value+
                "&surname="  +document.getElementById('src_surname').value+
                "&group="    +document.getElementById('src_group').value+
                "&place="    +document.getElementById('src_place').value;

    xmlHttp.open("POST", url, true);
   
    //Send the proper header information along with the request
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", data.length);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.onreadystatechange = users_list;
    
    xmlHttp.send(data);
}

function users_list()
{
    if (xmlHttp.readyState == 4)
    {
        var response = xmlHttp.responseXML;
        var data = '';
        var id, name, surname, tel, mail, position, group, place, foto;
        var x = response.getElementsByTagName('user');
        for (var i=0;i<x.length;i++)
        {
            id =        x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
            name =      x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
            surname =   x[i].getElementsByTagName("surname")[0].childNodes[0].nodeValue;
            tel =       x[i].getElementsByTagName("tel")[0].childNodes[0].nodeValue;
            mail =      x[i].getElementsByTagName("mail")[0].childNodes[0].nodeValue
            position =  x[i].getElementsByTagName("position")[0].childNodes[0].nodeValue;
            group =     x[i].getElementsByTagName("group")[0].childNodes[0].nodeValue;
            place =     x[i].getElementsByTagName("place")[0].childNodes[0].nodeValue;
            foto =      x[i].getElementsByTagName("foto")[0].childNodes[0].nodeValue;

            data += '<table width=500 height=80 cellspasing="2" class="lists" id="lists_'+id+'">';
            data += '<tr >';
                data += '<td width=70 rowspan="4" > <img src="'+foto+'" alt="foto" width="65"/> </td>';
                data += '<td width=60 class="text">Vardas</td>'
                data += '<td width=155 id= "name'+id+'">'+ name + '</td>';
                data += '<td width=60 class="text"> Kont. tel. </td>';
                data += '<td width=155 id= "tel'+id+'">'+ tel + '</td>';
                data += '<td rowspan="2" width=10 id= "edit'+id+'"> <img src="../images/edit.png" alt="keisti" onmousedown="user_edit('+id+',\''+name+'\',\''+surname+'\',\''+tel+'\',\''+mail+'\',\''+position+'\',\''+group+'\',\''+place+'\');" /> </td>'; //,
            data += '</tr>';
            data += '<tr class="lists">';
                data += '<td class="text"> Pavardė </td>';
                data += '<td width=155 id= "surname'+id+'">'+ surname + '</td>';
                data += '<td class="text"> El. paštas </td>';
                data += '<td width=155 id= "mail'+id+'">'+ mail + '</td>';
            data += '</tr>';
            data += '<tr class="lists">';
                data += '<td class="text"> Pareigos </td>';
                data += '<td width=155 id= "position'+id+'">'+ position + '</td>';
                data += '<td class="text"> Grupė </td>';
                data += '<td width=155 id= "group'+id+'">'+ group + '</td>';
                data += '<td rowspan="2" width=10 id= "delete'+id+'"> <img src="../images/del.png" alt="trinti" onmousedown="delete_user_conf('+id+');"/></td>';  // TRINTI
            data += '</tr>';
            data += '<tr>';
                data += '<td class="text"> Skyrius </td>';
                data += '<td width=155 id= "place'+id+'">'+ place + '</td>';
                data += '<td class="text"> ID </td>';
                data += '<td width=155 >'+ id + '</td>';
            data += '</tr>';
            data += '</table>';
        }
        document.getElementById('user_list').innerHTML = data;
    }
}


function user_edit(id, name, surname, tel, mail, position, group, place)
{
    document.getElementById('name'+id).innerHTML = '<input type="text" name="edit_name" class="inputs" maxlength="30" id="edit_name'+id+'" value="'+name+'">';
    document.getElementById('surname'+id).innerHTML = '<input type="text" name="edit_surname" class="inputs" maxlength="30" id="edit_surname'+id+'" value="'+surname+'">';
    document.getElementById('tel'+id).innerHTML = '<input type="text" name="edit_tel" class="inputs" maxlength="20" id="edit_tel'+id+'" value="'+tel+'">';
    document.getElementById('mail'+id).innerHTML = '<input type="text" name="edit_mail" class="inputs" maxlength="70" id="edit_mail'+id+'" value="'+mail+'">';
    document.getElementById('position'+id).innerHTML = '....';
    document.getElementById('group'+id).innerHTML = '....';
    document.getElementById('place'+id).innerHTML = '....';
    get_lists(id, 'position', position);
    get_lists1(id, 'group', group);
    get_lists2(id, 'place', place);
    document.getElementById('edit'+id).innerHTML = '<img src="../images/apply.png" alt="Keisti" onmousedown="save_data('+id+',\''+name+'\',\''+surname+'\',\''+tel+'\',\''+mail+'\',\''+position+'\',\''+group+'\',\''+place+'\');" />';
    document.getElementById('delete'+id).innerHTML = '<img src="../images/cancel.png" alt="Atšaukti" onmousedown="cancel('+id+');"/>';
}

function save_data(id)
{
    var url = "../admin/save";

    var name = document.getElementById('edit_name'+id).value;
    var surname = document.getElementById('edit_surname'+id).value;
    var group = document.getElementById('edit_group'+id).value;
    var place = document.getElementById('edit_place'+id).value;
    var tel = document.getElementById('edit_tel'+id).value;
    var mail = document.getElementById('edit_mail'+id).value;
    var position = document.getElementById('edit_position'+id).value;

    var data =  "id="      +id+
                "&name="     +name+
                "&surname="  +surname+
                "&group="    +group+
                "&place="    +place+
                "&tel="      +tel+
                "&mail="     +mail+
                "&position=" +position;
 
    xmlHttp.open("POST", url, true);

    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", data.length);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.onreadystatechange = function()
    {
        
        if (xmlHttp.readyState == 4)
        {
            if (xmlHttp.status == 200)
            {
                var response = xmlHttp.responseText;
                if (response == "done\n\n\n")
                {
                    document.getElementById('name'+id).innerHTML = name;
                    document.getElementById('surname'+id).innerHTML = surname;
                    var ind = group - 1;
                    document.getElementById('group'+id).innerHTML = document.getElementById('edit_group'+id).options[ind].text; 
                    ind = place - 1;
                    document.getElementById('place'+id).innerHTML = document.getElementById('edit_place'+id).options[ind].text; 
                    document.getElementById('tel'+id).innerHTML = tel;
                    document.getElementById('mail'+id).innerHTML = mail;
                    ind = position - 1;
                    document.getElementById('position'+id).innerHTML = document.getElementById('edit_position'+id).options[ind].text;                 
                    document.getElementById('edit'+id).innerHTML = '<img src="../images/edit.png" alt="keisti" onmousedown="user_edit('+id+',\''+name+'\',\''+surname+'\',\''+tel+'\',\''+mail+'\',\''+position+'\',\''+group+'\',\''+place+'\');" />';
                    document.getElementById('delete'+id).innerHTML = '<img src="../images/del.png" alt="trinti" onmousedown="delete_user_conf('+id+');"/>';  // TRINTI
                }
            }
        }
    }
    xmlHttp.send(data);
}

function cancel(id)
{
    var name = document.getElementById('edit_name'+id).value;
    var surname = document.getElementById('edit_surname'+id).value;
    var group = document.getElementById('edit_group'+id).value;
    var place = document.getElementById('edit_place'+id).value;
    var tel = document.getElementById('edit_tel'+id).value;
    var mail = document.getElementById('edit_mail'+id).value;
    var position = document.getElementById('edit_position'+id).value;

    document.getElementById('name'+id).innerHTML = name;
    document.getElementById('surname'+id).innerHTML = surname;
    var ind = group - 1;
    document.getElementById('group'+id).innerHTML = document.getElementById('edit_group'+id).options[ind].text;
    ind = place - 1;
    document.getElementById('place'+id).innerHTML = document.getElementById('edit_place'+id).options[ind].text;
    document.getElementById('tel'+id).innerHTML = tel;
    document.getElementById('mail'+id).innerHTML = mail;
    ind = position - 1;
    document.getElementById('position'+id).innerHTML = document.getElementById('edit_position'+id).options[ind].text;
    document.getElementById('edit'+id).innerHTML = '<img src="../images/edit.png" alt="keisti" onmousedown="user_edit('+id+',\''+name+'\',\''+surname+'\',\''+tel+'\',\''+mail+'\',\''+position+'\',\''+group+'\',\''+place+'\');" />';
    document.getElementById('delete'+id).innerHTML = '<img src="../images/del.png" alt="trinti" onmousedown="delete_user_conf('+id+');" />';  // TRINTI
}

function delete_user_conf(id)
{
    var data='';
    data+= 'Ar tikrai norite pašalinti vartotoja ('+document.getElementById('name'+id).innerHTML+' '+document.getElementById('surname'+id).innerHTML+') iš sistemos?';
    data+= '<br /> <br />';
    data+= '<table align="center"> <tr>';
    data+= '<td align="center"><img src="../images/apply.png" alt="Pašalinti" onmousedown="delete_user('+id+');" /></td>';
    data+= '<td align="center"><img src="../images/cancel.png" alt="Atšaukti" onmousedown="hide_confirm();" /></td>';
    data+= '</tr></table>';
    var winH = $(window).height();
    var winW = $(window).width();
    document.getElementById('window').innerHTML = data;
    $("#window").fadeIn("slow");
    $("#confirm").fadeIn("slow");
    $("#confirm").css({display: 'block', width: winW+'px', height: winH+'px'});
    $("#window").css({display: 'block', left: winW/2-260+'px', top: winH/2-140+'px'})
}

function delete_user(id)
{
    $.post("../admin/deluser", {id: id},
        function()
        {
            hide_confirm();
            $("#lists_"+id).slideUp("slow");
        });
}

function hide_confirm()
{
    $("#window").fadeOut("slow", function(){$("#confirm").css({display: 'none'});});
    $("#confirm").fadeOut("slow", function(){$("#window").css({display: 'none'});});
}


function get_lists(id, list_name, value)
{
    var url = "../admin/lists";
    var data ="info="+list_name;

    xmlHttp.open("POST", url, true);

    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", data.length);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.onreadystatechange = function()
    {
        if (xmlHttp.readyState == 4)
            {
                if (xmlHttp.status == 200)
                {
                    var response = xmlHttp.responseXML;
                    var data = '<select size="1" name="edit_'+list_name+'" class="inputs" id="edit_'+list_name+id+'">';
                    var x = response.getElementsByTagName('list');
                    var pos;
                    var selected = '';
                    for (var i=0;i<x.length;i++)
                    {
                        pos = x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                        if (pos == value)
                            selected = ' selected="selected"';
                        else
                            selected = '';
                        data += '<option value="'+x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue+'"'+selected+'">';
                        data += pos+'</option>';
                    }
                    data += '</select>';
                    document.getElementById(list_name+id).innerHTML = data;
                }
            }
    }
    xmlHttp.send(data);
}

function get_lists1(id, list_name, value)
{
    var url = "../admin/lists";
    var data ="info="+list_name;

    if (window.XMLHttpRequest)
    {
    // for IE7+, Firefox, Chrope, Opera...
        xmlHttp1 = new XMLHttpRequest();
        if (xmlHttp1.overrideMimeType)
        {
            xmlHttp1.overrideMimeType('text/xml');
        }
    }
    if (window.ActiveXObject)
    {
    // for IE6, IE5
        xmlHttp1 = new ActiveXObject("Microsoft.XMLHTTP");
        if (xmlHttp1.overrideMimeType)
        {
            xmlHttp1.overrideMimeType('text/xml');
        }
    }

    xmlHttp1.open("POST", url, true);

    xmlHttp1.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp1.setRequestHeader("Content-length", data.length);
    xmlHttp1.setRequestHeader("Connection", "close");
    xmlHttp1.onreadystatechange = function()
    {

        if (xmlHttp1.readyState == 4)
            {
                if (xmlHttp1.status == 200)
                {
                    var response = xmlHttp1.responseXML;
                    var data = '<select size="1" name="edit_'+list_name+'" class="inputs" id="edit_'+list_name+id+'">';
                    var x = response.getElementsByTagName('list');
                    var pos;
                    var selected = '';
                    for (var i=0;i<x.length;i++)
                    {
                        pos = x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                        if (pos == value)
                            selected = ' selected="selected"';
                        else
                            selected = '';
                        data += '<option value="'+x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue+'"'+selected+'">';
                        data += pos+'</option>';
                    }
                    data += '</select>';
                    document.getElementById(list_name+id).innerHTML = data;
                }
            }
    }
    xmlHttp1.send(data);
}

function get_lists2(id, list_name, value)
{
    var url = "../admin/lists";
    var data ="info="+list_name;

    if (window.XMLHttpRequest)
    {
    // for IE7+, Firefox, Chrope, Opera...
        xmlHttp2 = new XMLHttpRequest();
        if (xmlHttp2.overrideMimeType)
        {
            xmlHttp2.overrideMimeType('text/xml');
        }
    }
    if (window.ActiveXObject)
    {
    // for IE6, IE5
        xmlHttp2 = new ActiveXObject("Microsoft.XMLHTTP");
        if (xmlHttp2.overrideMimeType)
        {
            xmlHttp2.overrideMimeType('text/xml');
        }
    }

    xmlHttp2.open("POST", url, true);

    xmlHttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp2.setRequestHeader("Content-length", data.length);
    xmlHttp2.setRequestHeader("Connection", "close");
    xmlHttp2.onreadystatechange = function()
    {

        if (xmlHttp2.readyState == 4)
            {
                if (xmlHttp2.status == 200)
                {
                    var response = xmlHttp2.responseXML;
                    var data = '<select size="1" name="edit_'+list_name+'" class="inputs" id="edit_'+list_name+id+'">';
                    var x = response.getElementsByTagName('list');
                    var pos;
                    var selected = '';
                    for (var i=0;i<x.length;i++)
                    {
                        pos = x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                        if (pos == value)
                            selected = ' selected="selected"';
                        else
                            selected = '';
                        data += '<option value="'+x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue+'"'+selected+'">';
                        data += pos+'</option>';
                    }
                    data += '</select>';
                    document.getElementById(list_name+id).innerHTML = data;
                }
            }
    }
    xmlHttp2.send(data);
}
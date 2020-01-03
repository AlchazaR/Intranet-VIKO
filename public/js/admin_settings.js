function find_place()
{
    var name = document.getElementById('src_place_name').value;
    var net = document.getElementById('src_net').value;
    var city = document.getElementById('src_city').value;
 
    document.getElementById('settings_list').innerHTML = '<br /><img src="../images/ajax-loader.gif" alt="kraunasi..."/><br /> Kraunasi....';
       
    $.post("../admin/findplaces", {name: name, net: net,
                                   city: city},
           function(data)
           {
               var data_ = data;
               list_places(data_);
           }, "xml");
}

function list_places(xmldata)
{
    var data ='<br /><b>Skyrių sąrašas</b><br />';
    var id, net, city, name, adr, tel, mail;
    var x = xmldata.getElementsByTagName('place');
    
    for (var i=0;i<x.length;i++)
    {
        id = x[i].getElementsByTagName('id')[0].childNodes[0].nodeValue;
        net = x[i].getElementsByTagName('net')[0].childNodes[0].nodeValue;
        city = x[i].getElementsByTagName('city')[0].childNodes[0].nodeValue;
        name = x[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
        adr = x[i].getElementsByTagName('adr')[0].childNodes[0].nodeValue;
        tel = x[i].getElementsByTagName('tel')[0].childNodes[0].nodeValue;
        mail = x[i].getElementsByTagName('mail')[0].childNodes[0].nodeValue;

        data += '<table width=500 height=40 cellspasing="2" class="lists" id="place_'+id+'">';
        data += '<tr>';
        data += '<td class="text" width=95> Pavadinimas </td>';
        data += '<td id="name'+id+'" width=150>'+name+'</td>';
        data += '<td class="text" width=75> Tinklas </td>';
        data += '<td id="net'+id+'" width=170>'+net+'</td>';
        data += '<td rowspan="2" width=10 id= "edit'+id+'"> <img src="../images/edit.png" alt="keisti" onmousedown="place_edit('+id+',\''+name+'\',\''+net+'\',\''+tel+'\',\''+mail+'\',\''+city+'\',\''+adr+'\');" /> </td>';
        data += '</tr><tr>';
        data += '<td class="text"> Miestas </td>';
        data += '<td id="city'+id+'">'+city+'</td>';
        data += '<td class="text"> Adresas </td>';
        data += '<td id="adr'+id+'">'+adr+'</td>';
        data += '</tr><tr>';
        data += '<td class="text"> Telefono nr.</td>';
        data += '<td id="tel'+id+'">'+tel+'</td>';
        data += '<td class="text"> El. paštas </td>';
        data += '<td id="mail'+id+'">'+mail+'</td>';
        data += '<td width=10 id= "delete'+id+'"> <img src="../images/del.png" alt="trinti" onmousedown="delete_place_conf('+id+');"/></td>';  // TRINTI

        data += '</tr>';
    }
    data += '</table>';
    //settings_list
    document.getElementById('settings_list').innerHTML = data;
}

function place_edit(id, name, net, tel, mail, city, adr)
{
    document.getElementById('name'+id).innerHTML = '<input type="text" name="edit_name" class="inputs" maxlength="10" id="edit_name'+id+'" value="'+name+'">';
    document.getElementById('adr'+id).innerHTML = '<input type="text" name="edit_adr" class="inputs" maxlength="50" id="edit_adr'+id+'" value="'+adr+'">';
    document.getElementById('mail'+id).innerHTML = '<input type="text" name="edit_mail" class="inputs" maxlength="50" id="edit_mail'+id+'" value="'+mail+'">';
    document.getElementById('tel'+id).innerHTML = '<input type="text" name="edit_tel" class="inputs" maxlength="12" id="edit_tel'+id+'" value="'+tel+'">';
    document.getElementById('city'+id).innerHTML = '....';
    document.getElementById('net'+id).innerHTML = '....';
    get_lists(id, 'city', city);
    get_lists(id, 'net', net);
    document.getElementById('edit'+id).innerHTML = '<img src="../images/apply.png" alt="Keisti" onmousedown="save_ed_place('+id+');" />';
    document.getElementById('delete'+id).innerHTML = '<img src="../images/cancel.png" alt="Atšaukti" onmousedown="cancel_ed_place('+id+');"/>';

}

function get_lists(id, list_name, value)
{
    $.post("../admin/lists", {info: list_name},
           function(data)
           {
               var data_ = data;
               var text = '<select size="1" name="edit_'+list_name+'" class="inputs" id="edit_'+list_name+id+'">';
                    var x = data_.getElementsByTagName('list');
                    var pos;
                    var selected = '';
                    for (var i=0;i<x.length;i++)
                    {
                        pos = x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                        if (pos == value)
                            selected = ' selected="selected"';
                        else
                            selected = '';
                        text += '<option value="'+x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue+'"'+selected+'">';
                        text += pos+'</option>';
                    }
                    text += '</select>';
                    document.getElementById(list_name+id).innerHTML = text;
           }, "xml");
}

function save_ed_place(id)
{
    var name = document.getElementById('edit_name'+id).value;
    var adr = document.getElementById('edit_adr'+id).value;
    var mail = document.getElementById('edit_mail'+id).value;
    var tel = document.getElementById('edit_tel'+id).value;
    var net = document.getElementById('edit_net'+id).value;
    var city = document.getElementById('edit_city'+id).value;

    $.post("../admin/saveedit",  {name: name,
                                    adr: adr,
                                    mail: mail,
                                    tel: tel,
                                    net: net,
                                    city: city,
                                    id: id},
           function()
           {
                document.getElementById('name'+id).innerHTML = name;
                var ind = net - 1;
                document.getElementById('net'+id).innerHTML = document.getElementById('edit_net'+id).options[ind].text;
                ind = city - 1;
                document.getElementById('city'+id).innerHTML = document.getElementById('edit_city'+id).options[ind].text;
                document.getElementById('tel'+id).innerHTML = tel;
                document.getElementById('mail'+id).innerHTML = mail;
                document.getElementById('adr'+id).innerHTML = adr;
                document.getElementById('edit'+id).innerHTML = '<img src="../images/edit.png" alt="keisti" onmousedown="place_edit('+id+',\''+name+'\',\''+net+'\',\''+tel+'\',\''+mail+'\',\''+city+'\',\''+adr+'\');" />';
                document.getElementById('delete'+id).innerHTML = '<img src="../images/del.png" alt="trinti" onmousedown="delete_place_conf('+id+');"/>';  // TRINTI
           });

}

function delete_place_conf(id)
{
    var data='';
    data+= 'Ar tikrai norite pašalinti skyriu ('+document.getElementById('name'+id).innerHTML+') iš sistemos?';
    data+= '<br /> <br />';
    data+= '<table align="center"> <tr>';
    data+= '<td align="center"><img src="../images/apply.png" alt="Pašalinti" onmousedown="delete_place('+id+');" /></td>';
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

function delete_place(id)
{
    hide_confirm();
    $.post("../admin/delplace", {id: id},
        function()
        {
            $("#place_"+id).slideUp("slow");
        });
}

function hide_confirm()
{
    $("#window").fadeOut("slow", function(){$("#confirm").css({display: 'none'});});
    $("#confirm").fadeOut("slow", function(){$("#window").css({display: 'none'});});
}


function new_place()
{
    var name = document.getElementById('new_place_name').value;
    var adr = document.getElementById('new_place_adr').value;
    var mail = document.getElementById('new_place_mail').value;
    var tel = document.getElementById('new_place_tel').value;
    var net = document.getElementById('new_net').value;
    var city = document.getElementById('new_city').value;

    if (name.length > 1 && adr..length > 3 && mail.length > 4 && tel.length > 6)
    {
        $.post("../admin/newplace", {name: name,
                                 adr: adr,
                                 mail: mail,
                                 tel: tel,
                                 net: net,
                                 city: city},
                         function()
                         {
                            document.getElementById('new_place_name').value = '';
                            document.getElementById('new_place_adr').value = '';
                            document.getElementById('new_place_mail').value = '';
                            document.getElementById('new_place_tel').value = '';
                            document.getElementById('new_net').value = '';
                            document.getElementById('new_city').value = '';
                         });
    }
    else
    {
        var data='';
        data+= 'Ne visi laukai užpildyti.';
        data+= '<br /> <br />';
        data+= '<table align="center"> <tr>';
        data+= '<td align="center"><img src="../images/apply.png" alt="Pašalinti" onmousedown="hide_confirm();" /></td>';
        data+= '</tr></table>';
        var winH = $(window).height();
        var winW = $(window).width();
        document.getElementById('window').innerHTML = data;
        $("#window").fadeIn("slow");
        $("#confirm").fadeIn("slow");
        $("#confirm").css({display: 'block', width: winW+'px', height: winH+'px'});
        $("#window").css({display: 'block', left: winW/2-260+'px', top: winH/2-140+'px'})
    }
}

function net_delete_conf(id)
{
    var data='';
    data+= 'Ar tikrai norite pašalinti tinklą ('+document.getElementById('net_edit'+id).innerHTML+') iš sistemos?';
    data+= '<br /> <br />';
    data+= '<table align="center"> <tr>';
    data+= '<td align="center"><img src="../images/apply.png" alt="Pašalinti" onmousedown="delete_net('+id+');" /></td>';
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

function delete_net(id)
{
     hide_confirm();
     $.post("../admin/delnet", {id: id},
        function()
        {
            $("#net_"+id).slideUp("slow");
        });
}

function new_net()
{
    var name = document.getElementById('new_net_name').value;
    $.post("../admin/newnet", {name: name},
        function()
        {
            document.getElementById('new_net_name').value = '';
        });
}

function net_edit(id)
{
    var name = document.getElementById('net_edit'+id).innerHTML;
    name = name.replace(/^\s+/,'').replace(/\s+$/,'');
    document.getElementById('net_edit'+id).innerHTML = '<input type="text" name="net_edit" class="inputs" maxlength="40" id="net_edited'+id+'" value="'+name+'">';
    document.getElementById('net_edit_btn'+id).innerHTML = '<img src="../images/apply.png" alt="Keisti" onmousedown="save_ed_net('+id+');" />';
    document.getElementById('net_delete_btn'+id).innerHTML = '<img src="../images/cancel.png" alt="Atšaukti" onmousedown="cancel_ed_net('+id+', \''+name+'\');"/>';
}

function save_ed_net(id)
{
    var name = document.getElementById('net_edited'+id).value;
    $.post("../admin/saveEdNet", {name: name, id: id},
        function()
        {
            document.getElementById('net_edit'+id).innerHTML = name;
            document.getElementById('net_edit_btn'+id).innerHTML = '<img src="../images/edit.png" alt="Keisti" onmousedown="net_edit('+id+');" />';
            document.getElementById('net_delete_btn'+id).innerHTML = '<img src="../images/del.png" alt="Pašalinti" onmousedown="net_delete_conf('+id+');"/>';
        });
}

function cancel_ed_net(id, name)
{
    document.getElementById('net_edit'+id).innerHTML = name;
    document.getElementById('net_edit_btn'+id).innerHTML = '<img src="../images/edit.png" alt="Keisti" onmousedown="net_edit('+id+');" />';
    document.getElementById('net_delete_btn'+id).innerHTML = '<img src="../images/del.png" alt="Pašalinti" onmousedown="net_delete_conf('+id+');"/>';
}


function pos_edit(id)
{
    var name = document.getElementById('pos_edit'+id).innerHTML;
    name = name.replace(/^\s+/,'').replace(/\s+$/,'');
    document.getElementById('pos_edit'+id).innerHTML = '<input type="text" name="pos_edit" class="inputs" maxlength="40" id="pos_edited'+id+'" value="'+name+'">';
    document.getElementById('pos_edit_btn'+id).innerHTML = '<img src="../images/apply.png" alt="Keisti" onmousedown="save_ed_pos('+id+');" />';
    document.getElementById('pos_delete_btn'+id).innerHTML = '<img src="../images/cancel.png" alt="Atšaukti" onmousedown="cancel_ed_pos('+id+', \''+name+'\');"/>';
}

function save_ed_pos(id)
{
    var name = document.getElementById('pos_edited'+id).value;
    $.post("../admin/saveEdPos", {name: name, id: id},
        function()
        {
            document.getElementById('pos_edit'+id).innerHTML = name;
            document.getElementById('pos_edit_btn'+id).innerHTML = '<img src="../images/edit.png" alt="Keisti" onmousedown="pos_edit('+id+');" />';
            document.getElementById('pos_delete_btn'+id).innerHTML = '<img src="../images/del.png" alt="Pašalinti" onmousedown="pos_delete_conf('+id+');"/>';
        });
}

function cancel_ed_pos(id, name)
{
    document.getElementById('pos_edit'+id).innerHTML = name;
    document.getElementById('pos_edit_btn'+id).innerHTML = '<img src="../images/edit.png" alt="Keisti" onmousedown="pos_edit('+id+');" />';
    document.getElementById('pos_delete_btn'+id).innerHTML = '<img src="../images/del.png" alt="Pašalinti" onmousedown="pos_delete_conf('+id+');"/>';
}


function new_pos()
{
    var name = document.getElementById('new_pos_name').value;
    $.post("../admin/newpos", {name: name},
        function()
        {
            //alert (data);
            document.getElementById('new_pos_name').value = '';
        });
}

function pos_delete_conf(id)
{
    var data='';
    data+= 'Ar tikrai norite pašalinti pareigas ('+document.getElementById('pos_edit'+id).innerHTML+') iš sistemos?';
    data+= '<br /> <br />';
    data+= '<table align="center"> <tr>';
    data+= '<td align="center"><img src="../images/apply.png" alt="Pašalinti" onmousedown="delete_pos('+id+');" /></td>';
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

function delete_pos(id)
{
     hide_confirm();
     $.post("../admin/delpos", {id: id},
        function()
        {
            $("#pos_"+id).slideUp("slow");
        });
}


function save_others()
{
    
}

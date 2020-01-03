/**
 * Patikrinam ar sitas vartotojas dar neturi grupes su tokiu pat pavadinimu
 */
function ngr_check_group_name()
{
    var name = $("#newgroup_name").val();
    $.post("../mygroups/checkgrname", {name: name}, 
        function(_data)
        {
            if (_data >= 1)
            $("#ngr_errors").html('<span class="error">Grupė su tokių pavadinimų jau yra.</span>');
            else
            $("#ngr_errors").html('');
        }
    , "text");
}

/**
 * rasti vartotojus
 */
function ngr_find_user(slash)
{
    var name = $("#newgroup_fnd_name").val();
    var surname = $("#newgroup_fnd_surname").val();
    var place = $("#newgroup_user_place").val();
    var pos = $("#newgroup_user_pos").val();
    var patch = "../mygroups/finduser";
    if (slash != 0) // slash = 0 - sukuriama nauja grupe,
                    // slash !=0 - itraukiamas naujas vartotojas i jau sukurta grupe (psl. mano grupes-index)
    {
        name = $("#newgroup_fnd_name_"+slash).val();
        surname = $("#newgroup_fnd_surname_"+slash).val();
        place = $("#newgroup_user_place_"+slash).val();
        pos = $("#newgroup_user_pos_"+slash).val();
        patch = "mygroups/finduser";
    }
    $.post(patch, {name: name, surname: surname, place: place, pos: pos},
           function(data){ngr_show_users(data, slash)}, "xml" );
}

/**
 * parodyti rastu vartotoju sarasa
 */
function ngr_show_users(xmlData, slash)
{
    var data ='';
    var id, pos, name, surname, place, foto;
    var patch = "images/";
    if (slash == 0) patch = "../images/";
    var x = xmlData.getElementsByTagName('user');
    for (var i=0;i<x.length;i++)
        {
            id =      x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
            name =    x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
            surname = x[i].getElementsByTagName("surname")[0].childNodes[0].nodeValue;
            pos =     x[i].getElementsByTagName("position")[0].childNodes[0].nodeValue;
            place =   x[i].getElementsByTagName("place")[0].childNodes[0].nodeValue;
            foto =    x[i].getElementsByTagName("foto")[0].childNodes[0].nodeValue;

            data += '<table width=500 height=50 cellspasing="2" class="lists" id="user_lists_'+id+'" borders="1">';
            data += '<tr>';
                data += '<td rowspan = "2" width="60"><img src="'+foto+'" alt="foto" width="45"/></td>';
                data += '<td class="text" width="60">Vardas</td>';
                data += '<td width="155">'+name+'</td>';
                data += '<td class="text" width="60">Pavardė</td>';
                data += '<td width="155">'+surname+'</td>';
                data += '<td rowspan = "2" width="25"><img src="'+patch+'add-icon.png" alt="plius" onClick="ngr_chk_user(\''+pos+'\',\''+place+'\', \''+name+'\',\''+surname+'\',\''+foto+'\', '+id+', '+slash+');"></td>';
            data += '</tr>';
            data += '<tr>';
                data += '<td class="text">Pareigos</td><td>'+pos+'</td>';
                data += '<td class="text">Skyrius</td><td>'+place+'</td>';
            data += '</tr></table>';
        }
    if (slash == 0)
        $("#found_users").html(data);
    else $("#found_users_"+slash).html(data);
}

/**
 * patikrinti ar sis vart. dar neitrauktas i sita grupe
 */
function ngr_chk_user(pos, place, name, surname, foto, id, slash)
{
    var gr_name;
    if (slash == 0) // slash = 0 - sukuriama nauja grupe,
    {                // slash !=0 - itraukiamas naujas vartotojas i jau sukurta grupe (psl. mano grupes-index)
        gr_name = $("#newgroup_name").val();
        $("#ngr_errors").html('');
    }
    else
    {
        gr_name = "none";
        $("#ngr_errors_"+slash).html('');
    }

    if (gr_name.length > 0 && slash == 0)
    {
        var gr_id = $("#newgroup_id").val();

        // jeigu grupe dar nesukurta ir neturi savo ID, tada
        // jos id = new
        if (gr_id.length < 1)
        {
            gr_id = 'new';
            ngr_add_user(pos, place, name, surname, foto, id, gr_name, gr_id, slash);
            $("#newgroup_name").attr("disabled", true);
            $("#newgroup_info").attr("disabled", true); 
        }
        //patikrinam ar vartotojas dar neitrauktas i grupe
        else
        {
            $.post("../mygroups/chkuser", {gr_id: gr_id, user_id: id},
                function(_data)
                {
                    if (_data == "ok\n\n\n")
                        ngr_add_user(pos, place, name, surname, foto, id, gr_name, gr_id, slash);
                    else
                        $("#ngr_errors").html('<span class="error">Vartotojas jau įtrauktas į šita grupė.</span>');
                }, "text");
        }

    }
    if (slash != 0)
    {
        gr_id = slash;
        $.post("mygroups/chkuser", {gr_id: gr_id, user_id: id},
                function(_data)
                {
                    if (_data == "ok\n\n\n")
                        ngr_add_user(pos, place, name, surname, foto, id, gr_name, gr_id, slash);
                    else
                        $("#ngr_errors_"+slash).html('<span class="error">Vartotojas jau įtrauktas į šita grupė.</span>');
                }, "text");
    }

    if (gr_name.length < 1 && slash == 0)
    {
        $("#ngr_errors").html('<span class="error">Neparasytas grupės pavadinimas.</span>');
    }
}

/**
 *  itraukti vartotoja i grupe
 */
function ngr_add_user(pos, place, name, surname, foto, id, gr_name, gr_id, slash)
{
    var gr_info = "none";
    var patch, patch_im;
    if (slash == 0)
    {
        patch_im = "../images/";
        patch = "../mygroups/";
        gr_info = $("#newgroup_info").val();
    }
    else
    {
       patch_im = "images/";
       patch = "mygroups/";
    }
    $.post(patch+"adduser", {id: id, gr_name: gr_name, gr_id: gr_id, gr_info: gr_info},
            function(_data)
            {
                // naujas vartotojas irasytas i db
                if (_data != 'ok\n\n\n')
                {
                    $("#newgroup_id").val(_data);
                }
                var data='';
                data += '<table cellspasing="2" class="lists" id="added_lists_'+id+'" borders="1">';
                data += '<tr>';
                data += '<td width="20"></td>';
                data += '<td width="120">'+name+'</td>';
                data += '<td width="120">'+surname+'</td>';
                data += '<td width="140">'+pos+'</td>';
                data += '<td width="100">('+place+')</td>';
                data += '<td width="20"><img src="'+patch_im+'remove-icon.png" alt="minus" onClick="ngr_remove_user('+id+')"></td>';
                data += '</tr></table>';
                if (slash != 0)
                    $("#mgr_users_list_"+slash).prepend(data);
                else
                    $("#added_users").prepend(data);
            }, "text");
}

/**
 * pasalinti vartotoja is grupes (id - vartotojo, kuri norima pasalinti ID)
 * kai grupe tik sukurta
 */
function ngr_remove_user(del_id)
{
    var gr_id = $("#newgroup_id").val();
    $.post("../mygroups/removeuser", {gr_id: gr_id, del_id: del_id},
        function(_data)
        {
            alert(_data);
            if (_data == "ok\n\n\n")
            {
                $("#added_lists_"+del_id).slideUp("slow");
            }
            else alert("Dėmesio, įvyko klaida: \n"+_data);
        },"text");
}

/**
 *  parodyti grupes vartotojus
 */
function mgr_show_users_list(group_id)
{
    if (document.getElementById('mgr_users_list_'+group_id).style.display == "none")
    {
        $.post("mygroups/findgroupusers", {gr_id: group_id},
            function(_data)
            {
                var data='';
                var user_id, name, pos, surname, place, foto;
                var x = _data.getElementsByTagName('user');
                for (var i=0;i<x.length;i++)
                {
                    user_id = x[i].getElementsByTagName("user_id")[0].childNodes[0].nodeValue;
                    name =    x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                    surname = x[i].getElementsByTagName("surname")[0].childNodes[0].nodeValue;
                    place =   x[i].getElementsByTagName("place")[0].childNodes[0].nodeValue;
                    pos =     x[i].getElementsByTagName("position")[0].childNodes[0].nodeValue;
                    foto =    x[i].getElementsByTagName("foto")[0].childNodes[0].nodeValue;
                    data +='<table cellspasing="2" class="lists" id="tbl_group_user_'+user_id+'_'+group_id+'">';
                    data += '<tr><td width="20"></td><td width="120">'+name+'</td><td width="120">'+surname+'</td>';
                    data += '<td width="140">'+pos+'</td><td width="100">('+place+')</td>';
                    data += '<td width="20"><img src="images/remove-icon.png" alt="minus" onClick="ngr_remove_old_user('+user_id+', '+group_id+')"></td></tr>'
                    data += '</table>';
                }
                $("#mgr_users_list_"+group_id).html(data);
                $("#mgr_users_list_"+group_id).slideDown("slow");
            }, "xml")
    }
    else
    {
        $("#mgr_users_list_"+group_id).slideUp("slow");
    }
}

/**
 * ar istrinti grupe?
 */
function mgr_delete_group_conf(group_id, group_name)
{
    var data='';
    data+= 'Ar tikrai norite pašalinti grupė \"'+group_name+'\"?';
    data+= '<br /> <br />';
    data+= '<table align="center"> <tr>';
    data+= '<td align="center"><img src="images/apply.png" alt="Pašalinti" onmousedown="mgr_delete_group('+group_id+');" /></td>';
    data+= '<td align="center"><img src="images/cancel.png" alt="Atšaukti" onmousedown="hide_confirm();" /></td>';
    data+= '</tr></table>';
    var winH = $(window).height();
    var winW = $(window).width();
    document.getElementById('window').innerHTML = data;
    $("#window").fadeIn("slow");
    $("#confirm").fadeIn("slow");
    $("#confirm").css({display: 'block', width: winW+'px', height: winH+'px'});
    $("#window").css({display: 'block', left: winW/2-260+'px', top: winH/2-140+'px'})
}

/**
 * istrinti grupe
 */
function mgr_delete_group(group_id)
{
    $.post("mygroups/delgroup", {group_id: group_id},
        function(_data)
        {
            if (_data == "ok\n\n\n")
            {
                $("#groups_tbl_"+group_id).slideUp("slow");
                $("#mgr_users_list_"+group_id).slideUp("slow");
            }
            else alert("Klaida!\n\n"+_data);
            hide_confirm();
        }, "text");
}

/**
 * pasalinti vartotoja is grupes
 */
function ngr_remove_old_user(user_id, group_id)
{
    $.post("mygroups/removeuser", {gr_id: group_id, del_id: user_id},
        function(_data)
        {
           // alert (_data);
            if (_data == "ok\n\n\n")
            {
                $("#tbl_group_user_"+user_id+"_"+group_id).slideUp("slow");
            }
            else alert("Dėmesio, įvyko klaida: \n"+_data);
        },"text");
}


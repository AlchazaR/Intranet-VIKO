/**
 * itraukti grupes vartotojus i galinciu matyti sarasa
 */
function add_group_to_viewers(group_id)
{
    $.post("../foto/addgroup", {group_id: group_id},
        function(xmlData)
        {
            var data ='';
            var id, pos, name, surname, place, user_is;
            var x = xmlData.getElementsByTagName('user');
            for (var i=0;i<x.length;i++)
            {
                id =      x[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
                name =    x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
                surname = x[i].getElementsByTagName("surname")[0].childNodes[0].nodeValue;
                pos =     x[i].getElementsByTagName("position")[0].childNodes[0].nodeValue;
                place =   x[i].getElementsByTagName("place")[0].childNodes[0].nodeValue;
                user_is = $("#added_lists_"+id).length;
                if (user_is < 1)
                {
                    data += '<table cellspasing="2" class="lists" id="added_lists_'+id+'" borders="1">';
                        data += '<tr>';
                        data += '<td width="20"></td>';
                        data += '<td width="120">'+name+'</td>';
                        data += '<td width="120">'+surname+'</td>';
                        data += '<td width="140">'+pos+'</td>';
                        data += '<td width="100">('+place+')</td>';
                        data += '<td width="20"><img src="../images/remove-icon.png" alt="minus" onClick="album_remove_user('+id+')"></td>';
                    data += '</tr></table>';
                }
            }
            $("#users_list_can_see_album").prepend(data);
        }, "xml");
}

/**
 * rasti vartotoju pajieska
 */
function album_find_user()
{
    var name = $("#album_fnd_name").val();
    var surname = $("#album_fnd_surname").val();
    var place = $("#album_user_place").val();
    var pos = $("#album_user_pos").val();

    $.post("../mygroups/finduser", {name: name, surname: surname, place: place, pos: pos},
            function(xmlData)
            {
                var data ='';
                var id, pos, name, surname, place, foto;
                var patch = "../images/";
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
                        data += '<td rowspan = "2" width="25"><img src="'+patch+'add-icon.png" alt="plius" onClick="add_user_to_viewers(\''+pos+'\',\''+place+'\', \''+name+'\',\''+surname+'\','+id+');"></td>';
                        data += '</tr>';
                        data += '<tr>';
                        data += '<td class="text">Pareigos</td><td>'+pos+'</td>';
                        data += '<td class="text">Skyrius</td><td>'+place+'</td>';
                    data += '</tr></table>';
                }
                $("#found_users").html(data);
            }, "xml" );
}

/**
 * itraukti vartotoja i galinciu matyti sarasa
 */
function add_user_to_viewers(pos, place, name, surname, id)
{
    var user_is = $("#added_lists_"+id).length;
    var data = '';
    if (user_is < 1)
    {
        data += '<table cellspasing="2" class="lists" id="added_lists_'+id+'" borders="1">';
            data += '<tr>';
            data += '<td width="20"></td>';
            data += '<td width="120">'+name+'</td>';
            data += '<td width="120">'+surname+'</td>';
            data += '<td width="140">'+pos+'</td>';
            data += '<td width="100">('+place+')</td>';
            data += '<td width="20"><img src="../images/remove-icon.png" alt="minus" onClick="album_remove_user('+id+')"></td>';
        data += '</tr></table>';
    }
    $("#users_list_can_see_album").prepend(data);
}

/**
 * nuotrauku ikelimas i albuma  
 */
$(function()
{
    var btnUpload=$('#album_foto_upload');
    var status=$('#status');
    new AjaxUpload(btnUpload,
    {
        action: '../foto/fotoupload',
        //Name of the file input box
        name: 'album_foto_upload',
        onSubmit: function(file, ext)
        {
            if (! (ext && /^(jpg|png|jpeg|JPG|PNG|JPEG)$/.test(ext)))
            {
                // check for valid file extension
                status.text('<span class="error">Tik JPG, JPEG ir PNG tipo nuotraukas galima įkelti</span>');
                return false;
            }
            status.html('<img src="../images/ajax-loader.gif" alt="kraunasi"> &nbsp  Kraunasi... ');
        },
        onComplete: function(file, response)
        {
            //On completion clear the status
            status.text('');
            //Add uploaded file to list
            if(response.substring(0,7)=="success")
            {
                var data='';
                var t_id = response.substring(22, response.length-3);
                var img_link = response.substring(7, response.length);
                data += '<table height="110" cellspasing="2" class="lists" id="album_foto_'+t_id+'">';
                data += '<tr ><td rowspan="2" width="125" >';
                    data += '<center><img src="'+img_link+'" alt="foto"></center></td>';
                    data += '<td class="text" width="120" height="30">Pavadinimas</td>';
                    data += '<td width="315"><input type="text" class="inputs_mid" maxlength="50" id="album_new_foto_name_'+t_id+'"></td>';
                    data += '<td><img src="../images/remove-icon.png" alt="minus" onClick="album_remove_foto('+t_id+')"></td>';
                data += '</tr><tr >';
                    data += '<td class="text">Nuotraukos aprašymas</td>';
                    data += '<td width="315" ><textarea class="inputs_mid" rows="3" cols="50" id="album_new_foto_info_'+t_id+'"></textarea></td>';
                    data += '<td></td>';
               
                data += '</table>';
                $("#uploaded_album_fotos").prepend(data);
            }
            else
            {
                status.text('<span class="error">Nepavyko įkelti nuotraukos.</span>');
            }
        }
    });
});

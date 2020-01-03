function find_events()
{
    var u_name = document.getElementById('src_name').value;
    var u_surname = document.getElementById('src_surname').value;
    var u_group = document.getElementById('src_group').value;
    var u_place = document.getElementById('src_place').value;
    var d_from = document.getElementById('src_date1').value;
    var d_to = document.getElementById('src_date2').value;
    var e_type = document.getElementById('src_event').value;

    document.getElementById('events_list').innerHTML = '<br /><img src="../images/ajax-loader.gif" alt="kraunasi..."/><br /> Kraunasi....';
    $.post("../admin/findevents", {name: u_name, surname: u_surname,
                                   group: u_group, place: u_place,
                                   from: d_from, to: d_to,
                                   type: e_type},
           function(data)
           {
               var data_ = data;
               list_events(data_);
           }, "xml");
}

function list_events(xmldata)
{
    var data ='<br /><b>Įvykiai</b><br />';
    var u_id, u_name, u_surname, u_place, u_position, u_group, e_type, e_date;
    var x = xmldata.getElementsByTagName('event');
    for (var i=0;i<x.length;i++)
    {
        u_id = x[i].getElementsByTagName('id')[0].childNodes[0].nodeValue;
        u_name = x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
        u_surname = x[i].getElementsByTagName("surname")[0].childNodes[0].nodeValue;
        u_place = x[i].getElementsByTagName("place")[0].childNodes[0].nodeValue;
        u_position = x[i].getElementsByTagName("position")[0].childNodes[0].nodeValue;
        u_group = x[i].getElementsByTagName("group")[0].childNodes[0].nodeValue;
        e_type = x[i].getElementsByTagName("e_type")[0].childNodes[0].nodeValue;
        e_date = x[i].getElementsByTagName("date")[0].childNodes[0].nodeValue;

        data += '<table width=500 height=40 cellspasing="2" class="lists" id="lists_'+u_id+'">';
        data += '<tr>';
        data += '<td width=100>'+e_date+'</td>';
        data += '<td width=150>'+u_name+' '+u_surname+'</td>';
        data += '<td >'+e_type+'</td>';
        data += '</tr>';
        data += '</table>';

    }
    document.getElementById('events_list').innerHTML = data;
}


function new_event()
{
    var e_name = document.getElementById('text_new_event').value;
    $.post("../admin/newevent", {e_name: e_name});
    document.getElementById('text_new_event').value = '';
}
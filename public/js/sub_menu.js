function sub_menu()
{
    if (document.getElementById('sub_menu').style.display == "none")
        $("#sub_menu").slideDown("slow");
    else
        $("#sub_menu").slideUp("slow");
}

function show(name)
{
    if (document.getElementById(name).style.display == "none")
    {
        $("#"+name).slideDown("slow");
    }
    else
        $("#"+name).slideUp("slow");
}

function mouseover(name)
{
    $("#"+name).css("background","url(../images/table-open-h.png) bottom repeat-x");
}

function mouse_out(name)
{
    $("#"+name).css("background","url(../images/table-open.png) bottom repeat-x");
}

function tbl_out(name)
{
    $("#"+name).css("background-color","#f9f7f7");
}

function tbl_over(name)
{
    $("#"+name).css("background-color","#e2e1e1");
}

function hide_confirm()
{
    $("#window").fadeOut("slow", function(){$("#confirm").css({display: 'none'});});
    $("#confirm").fadeOut("slow", function(){$("#window").css({display: 'none'});});
}
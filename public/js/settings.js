function change_pass()
{
    var old_pass = $("#old_pass").val();
    var new_pass = $("#new_pass").val();
    var conf_pass = $("#new_pass_confirm").val();

    if (new_pass.length > 5)
    {
        if (new_pass == conf_pass)
        {
            $.post("../settings/changepass", {old_pass: old_pass, new_pass: new_pass},
                    function(data)
                    {
                        if (data == "ok\n\n\n")
                        {
                            $("#old_pass").val('');
                            $("#new_pass").val('');
                            $("#new_pass_confirm").val('');
                            $("#pass_error").html('<span class="done">Slaptažodis pakeistas.</span>');
                        }
                        else
                        {
                            $("#pass_error").html('<span class="error">Neteisingai parašytas senas slaptažodis.</span>');
                        }
                    });
        }
        else
        {
            $("#pass_error").html('<span class="error">Neteisingai pakartotas slaptažodis.</span>');
        }
    }
    else
    {
         $("#pass_error").html('<span class="error">Minimalus slaptažodžio ilgis - 6 simboliai.</span>');
    }
}


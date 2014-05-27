function toogleDiv(id, title) {
    $('.main').addClass('hide');
    $('#' + id).removeClass('hide');

    $('h2').html(title);

    $('.nav li').removeClass('active');
    $('.nav a[href="#' + id + '"]').parent().addClass('active');
}

function showBoolean(value) {
    return (value ? 'Oui' : 'Non');
}

function showNull(value) {
    if (value === null) {
        return '';
    }

    if (value === false) {
        return 'Erreur';
    }

    if (typeof(value) === 'array') {
        var return_value = '';
        $.each(value, function(i, item) {
            if (i !== 0) {
                return_value += ', '
            }

            return_value += item;
        });

        return return_value;
    }
    return value;
}

function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}


function buildSearch(id) {
    $('#' + id + ' table thead tr:last th select').each(function(i, item) {
        var options = '<option value="#--#">Tous</option>';
        var values = new Array();

        $('#' + id + ' table tbody tr').each(function(i_tr, item_tr) {
            values.push($.trim($(item_tr).find('td:eq(' + i + ')').html()));
        });

        values = unique(values);
        $.each(values, function(i_array, item_array) {
            options += '<option value="' + item_array + '">' + item_array + '</option>';
        });

        $(item).html(options);

        $(item).chosen();
    });
}

function viewServers(datas) {
    toogleDiv('servers', 'Serveurs');

    var tbody = '';
    $.each(datas, function (i, item) {
        tbody += '<tr>' +
                '<td>' + item.ip + '</td>' +
                '<td>' + showNull(item.hostname) + '</td>' +
                '<td>' + showBoolean(item.ping) + '</td>' +
                '<td>' + showBoolean(item.apache) + '</td>' +
                '<td>' + showBoolean(item.openerp) + '</td>' +
                '<td>' + showBoolean(item.php) + '</td>' +
                '<td>' + showNull(item.fis) + '</td>' +
                '<td>' + '' + '</td>' +
                '<td><a style="cursor:pointer;" class="edit" data-target="' + route_servers + '/' + item.ip + '">Modifier</a></td>' +
            '</tr>';
    });

    $('#servers table tbody').html(tbody);

    buildSearch('servers');
}

function viewDomains(datas) {
    toogleDiv('domains', 'Noms de domaine');

    var tbody = '';
    $.each(datas, function (i, item) {
        tbody += '<tr>' +
                '<td>' + item.domain + '</td>' +
                '<td>' + item.primary + '</td>' +
                '<td>' + showNull(item.subname) + '</td>' +
                '<td>' + showBoolean(item.is_primary) + '</td>' +
                '<td>' + showNull(item.dns_a) + '</td>' +
                '<td>' + showNull(item.dns_mx) + '</td>' +
                '<td><a style="cursor:pointer;" class="edit" data-target="' + route_domains + '/' + item.domain + '">Modifier</a></td>' +
            '</tr>';
    });
    $('#domains table tbody').html(tbody);

    buildSearch('domains');
}


function buildForm(datas) {
    var form = '';

    if (!datas.domain) {
        form += '<input type="hidden" name="_type" value="servers">';
        form += '<input type="hidden" name="_id" value="' + datas.ip + '">';
    } else {
        form += '<input type="hidden" name="_type" value="domains">';
        form += '<input type="hidden" name="_id" value="' + datas.domain + '">';
    }
    
    /* Domain/Server */
    if (datas.domain) {
        $.each(datas, function (key, value) {
            console.log(key, value);
            form += buildFormGroup(key, value, ($.inArray(key, additionnal_fields_domain) == -1));
        });
        $.each(additionnal_fields_domain, function (key, value) {
            console.log('add dom: ' + value);
            if (datas[value] === undefined) {
                form += buildFormGroup(value, '', false);
            }
        });
    } else {
        $.each(datas, function (key, value) {
            console.log(key, value);
            form += buildFormGroup(key, value, ($.inArray(key, additionnal_fields_server) == -1));
        });

        $.each(additionnal_fields_server, function (key, value) {
            console.log('add serv: ' + value);
            if (datas[value] === undefined) {
                form += buildFormGroup(value, '', false);
            }
        });
    }

    $('#form .form').html(form);
    toogleDiv('form', 'Modification de ' + (!datas.domain ? datas.ip : datas.domain));
}


function buildFormGroup(key, value, readonly) {
    return '<div class="form-group">' +
                '<label for="' + key + '" class="control-label col-md-2">' + key + '</label>' +
                buildInput(key, value, readonly) +
           '</div>';
}


function buildInput(key, value, readonly) {
    var input = '<div class="col-md-10"><input id="' + key + '" name="' + key + '" ';
    if (readonly) {
        input += 'readonly="readonly" disabled="disabled" ';
    }

    if (typeof value === 'boolean') {
        if (value) {
            input += 'checked="checked" ';
        }
        input += 'value="1" type="checkbox">'
    } else {
        input += 'value="' + showNull(value) + '" type="text" class="form-control">';
    }

    input += '</div>';

    return input;
}

function viewImport() {
    toogleDiv('import', 'Importer un Excel');
}

$(document).ready(function () {
    // Manage links
    $('.link').on('click', function () {
        var link = $(this).attr('data-target');
        callback = $(this).attr('data-callback') || null;
        method = $(this).attr('data-method') || 'GET';

        $.ajax({
            url: link,
            type: method,
            dataType: 'json',
            success: function (data) {
                if (callback) {
                    window[callback](data);
                }
            },
            error: function () {

            }
        });

        return false;
    });

    //cancel import page
    $('a#canceled').on('click', function () {
        $('#import-message').addClass('hide ');
        toogleDiv('homepage', 'Accueil');
    });

    /* Import message */
    $(document).ready(function () {
        var hash = window.location.hash;
        if (hash === '#import') {
            toogleDiv('import', 'Importer un Excel');
        }
        var message = $('#import-message');
        if (message.text() !== '') {
            message.removeClass("hide");
            message.delay(1200).slideToggle(600);
        }
    });

    // Manage selector
    $('select').on('change', function () {
        var name = $(this).attr('name');
        value = $(this).val();
        position = $(this).parent().index();
        table = $(this).parent().parent().parent().parent();

        if (value === '#--#') {
            $(table).find('tbody tr.hide-' + name).removeClass('hide-' + name);
            return;
        }

        $.each(table.find('tbody tr'), function (i, item) {
            if ($(item).find('td:eq(' + position + ')').html() !== value) {
                $(item).addClass('hide-' + name);
            } else {
                $(item).removeClass('hide-' + name);
            }
        });
    });

    // Manage edit
    $(document).on('click', '.edit', function () {
        var link = $(this).attr('data-target');

        $.ajax({
            url: link,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                buildForm(data);
            },
            error: function () {
            }
        });

        return false;
    });

    // Manage form
    $(document).on('submit', '#form form', function () {
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            dataType: 'json',
            data: $(this).serialize(),
            success: function (data) {
                var type = $('input[name="_type"]').val();
                $('a[href="#' + type + '"]').eq(0).click();
                $('#form .form').html('');
            },
            error: function () {
            }
        });

        return false;
    });

    $(document).on('click', '#cancel', function () {
        var type = $('input[name="_type"]').val();
        $('a[href="#' + type + '"]').eq(0).click();
        $('#form .form').html('');
        return false;
    });
});
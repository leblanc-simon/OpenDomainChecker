function toogleDiv(id, title)
{
    $('.main').addClass('hide');
    $('#' + id).removeClass('hide');

    $('h2').html(title);

    $('.nav li').removeClass('active');
    $('.nav a[href="#' + id +'"]').parent().addClass('active');
}

function showBoolean(value)
{
    return (value ? 'Oui' : 'Non');
}

function showNull(value)
{
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


function buildSearch(id)
{
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

function viewServers(datas)
{
    toogleDiv('servers', 'Serveurs');

    var tbody = '';
    $.each(datas, function(i, item){
        tbody += '<tr>' +
                '<td>' + item.ip + '</td>' +
                '<td>' + showNull(item.hostname) + '</td>' +
                '<td>' + showBoolean(item.ping) + '</td>' +
                '<td>' + showBoolean(item.apache) + '</td>' +
                '<td>' + showBoolean(item.openerp) + '</td>' +
                '<td>' + showBoolean(item.php) + '</td>' +
                '<td>' + showNull(item.fis) + '</td>' +
                '<td>' + '' + '</td>' +
            '</tr>';
    });

    $('#servers table tbody').html(tbody);

    buildSearch('servers');
}

function viewDomains(datas)
{
    toogleDiv('domains', 'Noms de domaine');

    var tbody = '';
    $.each(datas, function(i, item){
        tbody += '<tr>' +
            '<td>' + item.domain + '</td>' +
            '<td>' + item.primary + '</td>' +
            '<td>' + showNull(item.subname) + '</td>' +
            '<td>' + showNull(item.dns_a) + '</td>' +
            '<td>' + showNull(item.dns_mx) + '</td>'
            '</tr>';
    });

    $('#domains table tbody').html(tbody);

    buildSearch('domains');
}


$(document).ready(function() {
    // Manage links
    $('.link').on('click', function(){
        var link = $(this).attr('data-target');
        var callback = $(this).attr('data-callback') || null;
        var method = $(this).attr('data-method') || 'GET';

        $.ajax({
            url: link,
            type: method,
            dataType: 'json',
            success: function(data) {
                if (callback) {
                    window[callback](data);
                }
            },
            error: function(){

            }
        });
    });

    $('select').on('change', function(){
        var name = $(this).attr('name');
        var value = $(this).val();
        var position = $(this).parent().index();
        var table = $(this).parent().parent().parent().parent();

        if (value === '#--#') {
            $(table).find('tbody tr.hide-' + name).removeClass('hide-' + name);
            return;
        }

        $.each(table.find('tbody tr'), function(i, item) {
            if ($(item).find('td:eq(' + position + ')').html() != value) {
                $(item).addClass('hide-' + name);
            } else {
                $(item).removeClass('hide-' + name);
            }
        });
    });
});
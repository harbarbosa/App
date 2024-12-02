
// General helper function for $.get ajax requests
function requestGet(uri, params) {
    params = typeof (params) == 'undefined' ? {} : params;
    var options = {
        type: 'GET',
        url: uri
    };
    return $.ajax($.extend({}, options, params));
}

// General helper function for $.get ajax requests with dataType JSON
function requestGetJSON(uri, params) {
    params = typeof (params) == 'undefined' ? {} : params;
    params.dataType = 'json';
    return requestGet(uri, params);
}
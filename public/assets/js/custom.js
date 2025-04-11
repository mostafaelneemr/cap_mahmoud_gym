function __(key) {
    if ($global_lang == 'ar' && globalTranslations[key] != 'undefined') {
        return globalTranslations[key];
    }
    return key;
}

// RUN TIME JAVASCRIPT CODES
var ajaxRequestUrl = $('meta[name="ajax-post"]').attr('content');

// Disable button after submit
$('form').submit(function () {
    // $(this).find("button[type='submit']").not('.without-loading').val('Loading...').prop('disabled',true);
    // $(this).find("input[type='submit']").not('.without-loading').val('Loading...').prop('disabled',true);
});
let selectedIds = [];
// Disable button after submit

function pageAlert($selector, $type, $title, $message) {

    if ($type == 'hide') {
        $($selector).hide();
    } else if ($type == 'error') {
        $alret_div = $('.alert-error-disable').clone();
        $alret_div.find('#div_title').html($title);
        $alret_div.find('#div_message').html($message)

        $($selector).html($alret_div.html()).show();

        $("html, body").animate({ scrollTop: 0 }, "fast");
    } else if ($type == 'success') {
        $alret_div = $('.alert-success-disable').clone();
        $alret_div.find('#div_title').html($title);
        $alret_div.find('#div_message').html($message);

        $($selector).html().show();

    }
}

function add_loading($form_id) {

    var submit_button = document.querySelector('#' + $form_id + ' button[type="submit"]:last-child') ||  document.querySelector('#' + $form_id + ' button[type="submit"]');
    var second_submit = document.querySelector('#'+$form_id+' .second-submit');
    var submit_modal = document.querySelector('.submit_modal');
    var add_to_loading = document.querySelector('.add-to-loading');

    if(submit_button){
        submit_button.setAttribute('disabled', 'disabled');
        submit_button.setAttribute("data-kt-indicator", "on");
    }

    if(submit_modal){
        submit_modal.setAttribute('disabled', 'disabled');
        submit_modal.setAttribute("data-kt-indicator", "on");
    }
    if(second_submit){
        second_submit.setAttribute('disabled', 'disabled');
        second_submit.setAttribute("data-kt-indicator", "on");
    }

    if(add_to_loading){
        add_to_loading.setAttribute('disabled', 'disabled');
        add_to_loading.setAttribute("data-kt-indicator", "on");
    }
}

function remove_loading($form_id) {

    var submit_button1 = document.querySelector('#' + $form_id + ' button[type="submit"]');
    var submit_button = document.querySelector('#' + $form_id + ' button[type="submit"]:last-child,button[type="submit"]');
    var second_submit = document.querySelector('#'+$form_id+' .second-submit');
    var submit_modal = document.querySelector('.submit_modal');

    if(submit_button1){
        submit_button1.removeAttribute('disabled', 'disabled');
        submit_button1.removeAttribute("data-kt-indicator", "on");
    }

    if(submit_button){
        submit_button.removeAttribute("data-kt-indicator");
        submit_button.removeAttribute('disabled');
    }
    if(submit_modal){
        submit_modal.removeAttribute("data-kt-indicator");
        submit_modal.removeAttribute('disabled');
    }

    if(second_submit){
        second_submit.removeAttribute('disabled', 'disabled');
        second_submit.removeAttribute("data-kt-indicator", "on");
    }
}


function FormSubmit($url, $form_id = 'main-form', $success = null, $error = null, $method = 'POST') {
    var $data = new FormData(document.getElementById($form_id));
    add_loading($form_id);
    if(document.querySelector('#' + $form_id + ' #form-alert-message')){
        pageAlert('#' + $form_id + ' #form-alert-message', 'hide');
    }else if(document.querySelector('#form-alert-message')){
        pageAlert('#form-alert-message', 'hide');
    }
    $("[id$='-form-input']").removeClass('is-invalid');
    $("[id$='-form-error']").html('');
    var errorMesg = __('Some fields are invalid please fix them');
    $options = {
        url: $url,
        type: $method,
        data: $data,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function ($response) {

            if ($response.status == false) {

                if(document.querySelector('#' + $form_id + ' #form-alert-message')){
                    pageAlert('#' + $form_id + ' #form-alert-message', 'error', errorMesg, $response.responseJSON.message);
                }else if(document.querySelector('#form-alert-message')){
                    pageAlert('#form-alert-message', 'error', errorMesg, $response.responseJSON.message);
                }
                var errors = $response.data;
                console.log(errors);
                $.each(errors, function (key, value) {
                    $('#' + key + '-form-input').addClass('is-invalid');
                    $('#' + key + '-form-error').html(value + '<br />');
                });
                $('.invalid-feedback').css('display', 'block')
                $("html, body").animate({ scrollTop: 0 }, "fast");
            } else {
                if ($success) {
                    $success($response);

                }
                if ($response.data && Object.size($response.data)) {
                    console.log($response);
                    if ($response.data.url) {
                        console.log($response.data)
                        window.location = $response.data.url;
                    }

                    if ($response.data.new_url) {
                        window.open($response.data.new_url, '_blank').focus();
                    }

                    if ($response.data.reload) {
                        remove_loading($form_id)
                        $(datatableID).DataTable().ajax.reload();
                        hideModal();
                        notify($response.message, 'success');
                        $('#'+$form_id)[0].reset();
                    }
                    if ($response.data.error) {
                        remove_loading($form_id)
                        $(datatableID).DataTable().ajax.reload();
                        hideModal();
                        notify($response.message, 'error');
                        $('#'+$form_id)[0].reset();
                    }

                    if($response.data.redirect_url){
                        window.location = $response.data.redirect_url;
                        notify($response.message, 'success');
                    }

                } else {
                    remove_loading($form_id);
                    hideModal();
                    notify($response.message, 'success')
                }
            }

        },
        error: function ($response) {

             notify($response.responseJSON.message, 'error');
            remove_loading($form_id);
            if ($error) {
                $error($response);
            }

            if(document.querySelector('#' + $form_id + ' #form-alert-message')){
                pageAlert('#' + $form_id + ' #form-alert-message', 'error', errorMesg, $response.responseJSON.message);
            }else if(document.querySelector('#form-alert-message')){
                pageAlert('#form-alert-message', 'error', errorMesg, $response.responseJSON.message);
            }

            var errors = $.parseJSON($response.responseText);

            $.each(errors.errors, function (key, value) {
                var search = key.includes('.');
                if (search) {

                    key = key.replace(".", '[')
                    key = key.replaceAll(".", '][')
                    key = key + ']';
                }

                key = key.replace(/(:|\.|\[|\])/g, '\\$1')
                $('#'+$form_id).find('#' + key + '-form-input').addClass('is-invalid');
                if(value[0]){
                    value = value[0];
                }

                $('#'+$form_id).find('#' + key + '-form-error').html(value);
            });
            $('.invalid-feedback').css('display', 'block')
            $("html, body").animate({ scrollTop: 0 }, "fast");
        }
        ,
        fail: function ($response) {

            notify($response.responseJSON.message, 'error');
            remove_loading($form_id)
            if ($error) {
                $error($response);
            }

            if(document.querySelector('#' + $form_id + ' #form-alert-message')){
                pageAlert('#' + $form_id + ' #form-alert-message', 'error', errorMesg, $response.responseJSON.message);
            }else if(document.querySelector('#form-alert-message')){
                pageAlert('#form-alert-message', 'error', errorMesg, $response.responseJSON.message);
            }


            var errors = $.parseJSON($response.responseText);

            $.each(errors.errors, function (key, value) {

                if (key.search(".")) {
                    key = key.replace(".", '][')
                    key = '[' + key + ']';
                }

                key = key.replace(/(:|\.|\[|\])/g, '\\$1')
                $('#' + key + '-form-input').addClass('is-invalid');
                $('#' + key + '-form-error').html(value);
            });
            $('.invalid-feedback').css('display', 'block')
            $("html, body").animate({ scrollTop: 0 }, "fast");
        }
    };
    $.ajax($options)
}

// LARAVEL CSRF
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    error: function (jqXHR, textStatus, errorThrown) {

    }
});
function hideModal() {
    $(".modal").modal("hide");
}


function urlIframe($url, $headerTitle, without_navbar = 1) {
    // var  without_navbar = without_navbar;

    if (without_navbar == 1) {
        $url = $url + "?without_navbar=true";
    }


    $url = $url;
    $('#modal-iframe-url').height(($(window).height() - 150) + "px");
    $('#modal-iframe-width').css('max-width', ($(window).width() - 100) + "px");

    $('#modal-iframe-title').text($headerTitle);


    $('#modal-iframe').modal('show');

    $('#modal-iframe-url').hide();

    $('#modal-iframe-url').attr('src', $url);
    $('#modal-iframe-image').show();


    $('#modal-iframe-url').on('load', function () {
        $('#modal-iframe-image').hide();
        $('#modal-iframe-url').show();
    });
}

function deleteRecord($button_id, $routeName, $row_id) {


    Swal.fire({
        html: __('Are you sure you want to delete ?'),
        icon: __('warning'),
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: __("Confirm"),
        cancelButtonText: __('Cancel'),
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {


            var btn = $('#' + $button_id);

            var btn_html = btn.html();
            btn.html('<i class="fas fa-sync fa-spin"></i>');


            $options = {
                url: $routeName,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },

                success: function ($response) {
                    // $response = $response.responseJSON;
                    btn.html(btn_html);

                    if ($response.status == false) {
                        notify($response.message, 'error');
                    } else {
                        notify($response.message, 'success');
                        $('#' + $row_id).remove();

                    }
                },
                error: function ($response) {
                    $response = $response.responseJSON;
                    btn.html(btn_html);
                    notify($response.message, 'error');
                },
                fail: function ($response) {
                    $response = $response.responseJSON;
                    btn.html(btn_html);
                    notify($response.message, 'error');
                }
            };
            $.ajax($options)


        }
    });


}
function updateRecord($button_id, $id, $status, $routeName) {
    let htmlLabel = 'Are you sure you want to Approve ?';
    let label = "Approve";
    let btn_class = 'btn btn-success';
    if ($status == 2) {
        htmlLabel = 'Are you sure you want to Disapprove ?';
        label = "Disapprove";
        btn_class = 'btn btn-danger';
    }

    if ($id > 0 || selectedIds.length > 0) {
        Swal.fire({
            html: __(htmlLabel),
            icon: 'warning',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: __(label),
            cancelButtonText: __('Cancel'),
            customClass: {
                confirmButton: btn_class,
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                let data = [];
                var btn;
                var btn_html;
                if ($button_id) {
                    btn = $('#' + $button_id);
                    data = $id;
                }
                else {
                    btn = $('.approve_btn');
                    data = selectedIds;
                }
                btn_html = btn.html();
                btn.html('<i class="fas fa-sync fa-spin"></i>');

                $options = {
                    url: $routeName,
                    type: 'POST',
                    data: {
                        '_method': 'POST',
                        'data[]': data,
                        'status': $status,
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function ($response) {
                        // $response = $response.responseJSON;
                        btn.html(btn_html);
                        $('#show-review-modal').modal('hide');
                        if ($response.status == false) {
                            notify($response.message, 'error');
                        } else {
                            notify($response.message, 'success');
                            if (Array.isArray(data)) {
                                data.forEach(function (item) {
                                    $('#tr_' + item).hide();
                                    selectedIds = [];
                                });
                            } else {
                                $('#tr_' + data).hide();
                                selectedIds = [];
                            }


                        }
                    },
                    error: function ($response) {
                        $response = $response.responseJSON;
                        btn.html(btn_html);
                        notify($response.message, 'error');
                    },
                    fail: function ($response) {
                        $response = $response.responseJSON;
                        btn.html(btn_html);
                        notify($response.message, 'error');
                    }
                };
                $.ajax($options)

            }
        });
    } else {
        console.log('no Id seletced');
    }
}
function isJSON(m) {
    if (typeof m == 'object') {
        try {
            m = JSON.stringify(m);
        } catch (err) {
            return false;
        }
    }

    if (typeof m == 'string') {
        try {
            m = JSON.parse(m);
        } catch (err) {
            return false;
        }
    }

    if (typeof m != 'object') {
        return false;
    }
    return true;

}

function ajaxSelect2Modal(Selector, Type, filterBtnSelector = '#filter_btn', modalSelector = '#filter-modal') {
    let SelectorData = '';

    function handleCategoryChange() {
        SelectorData = $(Selector).select2('data');
    }

    function handleFilterClick() {
        ajaxSelect2(Selector, Type, 2, modalSelector);

        if (SelectorData !== '') {
            const data = {
                id: SelectorData[0].id,
                text: SelectorData[0].value,
            };

            const textValue = SelectorData[0].text !== '' ? SelectorData[0].text : SelectorData[0].value;
            $(Selector).html(`<option value="${SelectorData[0].id}">${textValue}</option>`);
            $(Selector).val(SelectorData[0].id).trigger('change');
        }
    }

    $(Selector).change(handleCategoryChange);
    $(filterBtnSelector).click(handleFilterClick);

    return {
        destroy: function () {
            $(Selector).off('change', handleCategoryChange);
            $(filterBtnSelector).off('click', handleFilterClick);
        }
    };
}

function ajaxSelect2($formID, $controllerFunction, $chars, $parentModal = '',data=null) {
    if ($chars == undefined) {
        $chars = 1;
    }
    if ($parentModal != '')
        $parentModal = $($parentModal);
    $($formID).select2({
        dropdownParent: $parentModal,
        dir: $global_lang == 'ar' ? 'rtl' : 'ltr',
        "language": {
            "inputTooShort": function () {
                return __('Please enter ' + $chars + ' or more characters');
            },
            "noResults": function () {
                return __('No Results Found');
            }
        },
        ajax: {
            method: 'GET',
            url: ajaxRequestUrl,
            dataType: 'json',

            data: function (params) {
                if(data){
                    var values = Object.assign({},{
                        type: $controllerFunction,
                        word: params.term
                    },data);
                    return values;
                }else{
                    return {
                        type: $controllerFunction,
                        word: params.term
                    };
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data,
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: $chars,
        templateResult: function (data) {
            if (data.loading === true) { // adjust for custom placeholder values
                return data.text;
            }

            return data.value;
        },
        templateSelection: function (data, container) {
            if (data.text != '') {
                return data.text;
            }
            return data.value;
        }
    })

}


// PHP.JS Functions
function isset() {
    //  discuss at: http://locutus.io/php/isset/
    // original by: Kevin van Zonneveld (http://kvz.io)
    // improved by: FremyCompany
    // improved by: Onno Marsman (https://twitter.com/onnomarsman)
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    //   example 1: isset( undefined, true)
    //   returns 1: false
    //   example 2: isset( 'Kevin van Zonneveld' )
    //   returns 2: true
    var a = arguments
    var l = a.length
    var i = 0
    var undef
    if (l === 0) {
        throw new Error('Empty isset')
    }
    while (i !== l) {
        if (a[i] === undef || a[i] === null) {
            return false
        }
        i++
    }
    return true
}

function current(arr) {
    //  discuss at: http://locutus.io/php/current/
    // original by: Brett Zamir (http://brett-zamir.me)
    //      note 1: Uses global: locutus to store the array pointer
    //   example 1: var $transport = ['foot', 'bike', 'car', 'plane']
    //   example 1: current($transport)
    //   returns 1: 'foot'
    var $global = (typeof window !== 'undefined' ? window : global)
    $global.$locutus = $global.$locutus || {}
    var $locutus = $global.$locutus
    $locutus.php = $locutus.php || {}
    $locutus.php.pointers = $locutus.php.pointers || []
    var pointers = $locutus.php.pointers
    var indexOf = function (value) {
        for (var i = 0, length = this.length; i < length; i++) {
            if (this[i] === value) {
                return i
            }
        }
        return -1
    }
    if (!pointers.indexOf) {
        pointers.indexOf = indexOf
    }
    if (pointers.indexOf(arr) === -1) {
        pointers.push(arr, 0)
    }
    var arrpos = pointers.indexOf(arr)
    var cursor = pointers[arrpos + 1]
    if (Object.prototype.toString.call(arr) === '[object Array]') {
        return arr[cursor] || false
    }
    var ct = 0
    for (var k in arr) {
        if (ct === cursor) {
            return arr[k]
        }
        ct++
    }
    // Empty
    return false
}

function empty(mixedVar) {
    //  discuss at: http://locutus.io/php/empty/
    // original by: Philippe Baumann
    //    input by: Onno Marsman (https://twitter.com/onnomarsman)
    //    input by: LH
    //    input by: Stoyan Kyosev (http://www.svest.org/)
    // bugfixed by: Kevin van Zonneveld (http://kvz.io)
    // improved by: Onno Marsman (https://twitter.com/onnomarsman)
    // improved by: Francesco
    // improved by: Marc Jansen
    // improved by: Rafał Kukawski (http://blog.kukawski.pl)
    //   example 1: empty(null)
    //   returns 1: true
    //   example 2: empty(undefined)
    //   returns 2: true
    //   example 3: empty([])
    //   returns 3: true
    //   example 4: empty({})
    //   returns 4: true
    //   example 5: empty({'aFunc' : function () { alert('humpty'); } })
    //   returns 5: false
    var undef
    var key
    var i
    var len
    var emptyValues = [undef, null, false, 0, '', '0']
    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixedVar === emptyValues[i]) {
            return true
        }
    }
    if (typeof mixedVar === 'object') {
        for (key in mixedVar) {
            if (mixedVar.hasOwnProperty(key)) {
                return false
            }
        }
        return true
    }
    return false
}

function rand(min, max) {
    //  discuss at: http://locutus.io/php/rand/
    // original by: Leslie Hoare
    // bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
    //      note 1: See the commented out code below for a version which
    //      note 1: will work with our experimental (though probably unnecessary)
    //      note 1: srand() function)
    //   example 1: rand(1, 1)
    //   returns 1: 1
    var argc = arguments.length
    if (argc === 0) {
        min = 0
        max = 2147483647
    } else if (argc === 1) {
        throw new Error('Warning: rand() expects exactly 2 parameters, 1 given')
    }
    return Math.floor(Math.random() * (max - min + 1)) + min
}

function notify(message, type = 'info') {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toastr-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    var success_title = __('Success !');
    var error_title = __('ERROR !');
    var info_title = __('INFO !');

    if (type == 'success') {
        toastr.success(message, success_title);
    } else if (type == 'error') {
        toastr.error(message, error_title);
    } else {
        toastr.info(message, info_title);
    }

}

function sweetConfirmAlert(message, type) {
    return Swal.fire({
        html: message,
        icon: type,
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: __("Confirm"),
        cancelButtonText: __('Cancel'),
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            return true;
        } else {
            return false;
        }
    });
}

function createDatatable(datatableID, datatableURL, datatableVar, js_column, sort = 0) {


    var js_column_data = [];
    Object.entries(js_column).forEach(entry => {
        const [$key, $row] = entry;

        if ($key == 'action' || $row == '') {
            js_column_data.push({ data: $key, name: $row, orderable: false, searchable: false })
        } else {
            js_column_data.push({ data: $key, name: $row, "className": "mx-300" })
        }
    });

    if (datatableURL.includes('?'))
        var extra_url = '&isDataTable=true';
    else
        var extra_url = '?isDataTable=true';

    var full_url = datatableURL + extra_url;
    full_url = full_url.replace(/&amp;/g, '&');

    window[datatableVar] = $(datatableID).on('processing.dt', function (e, settings, processing) {
        $('#processingIndicator').css('display', 'none');
        if (processing) {
            $('.app-default-loader').show();
        } else {
            $('.app-default-loader').hide();
        }
    }).DataTable({
        processing: true,
        // responsive: true,
        serverSide: true,
        pageLength: 100,
        lengthMenu: ["All",50, 100, 200, 300],
        order: js_dataTableSort.column == '-1' ? [] : [js_dataTableSort.column, js_dataTableSort.order],
        "language": {
            "info": __('Showing') + " _START_ " + __('to') + " _END_ " + __('of') + " _TOTAL_ " + __('entries'),
            "search": __('Search'),
            "paginate": {
                "previous": '<i class="previous"></i>',
                "next": '<i class="next"></i>'
            },
            "infoEmpty": __('Showing 0 to 0 of 0 entries'),
            "emptyTable": __('No Data'),
            "processing": __('loading'),
            'zeroRecords': __('No matching records found'),
            'infoFiltered': '(' + __('filtered from') + " _MAX_ " + __('total entries') + ')'
        },
        ajax: full_url,
        columns: js_column_data,
        "fnDrawCallback": function (evn) {
            $('.pagination').addClass('pagination-outline');

            hideStatusButtons();
            $('#checkall').prop('checked', false); // Uncheck checked All if filter applied
            $('[class*="jsPop-"]').each(function () {
                $(this).magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    tLoading: 'Loading image #%curr%...',
                    mainClass: 'mfp-img-mobile',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
                    },
                    image: {
                        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
                    }
                });
            });


            $("html, body").animate({
                scrollTop: 0
            }, 600);



        }

    });
    // $(datatableID + ' .thead2 th').each(function (i) {
    //     var title = $(datatableID + ' .thead2 th').eq($(this).index()).text();
    //     $(this).html('<input type="text" placeholder="' + title + '" class="form-control form-control-solid form-control-sm" data-index="' + i + '" />');
    // });

    $(datatableID).on('click', '.dt_review', function () {
        if (this.checked) {
            selectedIds.push(parseInt(this.value));
        }
        else {
            const index = selectedIds.indexOf(parseInt(this.value));

            if (index > -1) {

                selectedIds.splice(index, 1);
            }
        }


        if (selectedIds.length > 0) {
            showStatusButtons();
        } else
            hideStatusButtons();

    });
    $(window[datatableVar].table().container()).on('keyup', '.thead2 input', function () {
        window[datatableVar]
            .column($(this).data('index'))
            .search(this.value)
            .draw();
    });
    $('#checkall').on('change', function () {
        var checked = $(this).prop('checked');
        window[datatableVar].cells(null, 0).every(function (idx, data) {
            var cell = this.node();
            $(cell).find('input[type="checkbox"][name="ids[]"]').prop('checked', checked);
        });
        var data = window[datatableVar]
            .rows(function (idx, data, node) {
                if (checked && data.review_id) {
                    selectedIds.push(data.review_id)
                }
                else {
                    selectedIds = [];
                }
                return $(node).find('input[type="checkbox"][name="ids[]"]').prop('checked');
            })
            .data()
            .pluck('review_id');
        if (selectedIds.length > 0) {
            showStatusButtons();
        } else
            hideStatusButtons();
    })
}

function hideStatusButtons() {
    $('#checkbox_review_status_action_btns').css('display', 'none');
}

function showStatusButtons() {
    $('#checkbox_review_status_action_btns').css('display', 'block');
}

function filterFunction(datatableURL, datatableVar, $this, downloadExcel = false) {

    if (downloadExcel == false) {
        $url = datatableURL + '?&isDataTable=true&' + $this.serialize();
        window[datatableVar].ajax.url($url).load();
        $('#filter-modal').modal('hide');
    } else {
        $url = datatableURL + '?isDataTable=true&' + $this.serialize() + '&downloadExcel=' + downloadExcel;
        window.location = $url;
    }

}

function check_telephone() {
    var phone = $(".valid_telephone").val();
    if (phone != '') {
        phone = phone.replace(/\D/g, '')
        if (phone.substring(0, 1) == 0) {
            $(".valid_telephone").val(phone.substring(1));
            check_telephone()
            return;
        }
        $(".valid_telephone").val(phone);

    }
}

$(function () {
    $('.valid_telephone').change(function () {
        check_telephone();
    });
    $('.valid_telephone').keyup(function () {
        check_telephone();
    });
    $('.valid_telephone').keydown(function () {
        check_telephone();
    });
})

function editModal($button_id, $routName, $mainModalId, $recordId, $prev_status, $primaryKey, $updateUrl) {
    if ($recordId) {
        var btn = $('#' + $button_id);
        var key = $primaryKey;
        var btn_html = btn.html();
        btn.html('<i class="fas fa-sync fa-spin"></i>');
        $options = {
            url: $routName,
            type: 'GET',
            data: {
                [key]: $recordId,
                '_token': $('meta[name="csrf-token"]').attr('content')
            },

            success: function ($response) {
                btn.html(btn_html);
                if ($response.status == false) {
                    notify($response.message, 'error');
                }

                var modalId = $mainModalId;
                if ($response.data) {
                    if ($response.data.station) {
                        modalId = editStation($response.data.station, $updateUrl,$mainModalId);
                    }
                }
                $(`#${modalId}`).modal("show");
            },
            error: function ($response) {
                btn.html(btn_html);
            },
            fail: function ($response) {
                btn.html(btn_html);
            }
        };
        $.ajax($options)
    }

}

function showModal($button_id, $routName, modalId, $recordId, $prev_status, $primaryKey, $updateUrl) {
    if ($recordId) {
        var btn = $('#' + $button_id);
        var key = $primaryKey;
        var btn_html = btn.html();
        btn.html('<i class="fas fa-sync fa-spin"></i>');
        $options = {
            url: $routName,
            type: 'GET',
            data: {
                [key]: $recordId,
                '_token': $('meta[name="csrf-token"]').attr('content')
            },

            success: function ($response) {
                btn.html(btn_html);
                if ($response.status == false) {
                    notify($response.message, 'error');
                }
                if ($response.data) {
                    $('.review_show_body').html($response.data.review);
                }
                $(`#${modalId}`).modal("show");
            },
            error: function ($response) {
                btn.html(btn_html);
            },
            fail: function ($response) {
                btn.html(btn_html);
            }
        };
        $.ajax($options)
    }

}

$(document).on('click', '.popdiv', function () {
    $('.popdiv').not($(this)).popover('hide');
    $(this).popover('toggle')
})
// Toggle between first and second visit

$(document).ready(function () {
    $(".second_visit").hide();
    $(".hint").show();

    $(".hint").on("click", function () {
        $(this).slideUp();
        $(".second_visit").slideDown();
    });

    // Toggle between sign in form and QR code

    // $(".modal-dialog").hide();

    $(".show-qrCode").on("click", function () {
        var $url = $('#main-form').attr('action');
        FormSubmit($url, 'main-form', function ($response) {

            if (!$response.data.first_time) {
                $('.switching_visits').hide();
            } else {

                $('.QR-code').attr('src', 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + $response.data.qrCodeUrl);
                // $('#load-qr').hide();
            }

            if(!$response.data.url){
                $('.login-form').addClass('d-none');
                $('.code-form').removeClass('d-none');
                $('.code-email').attr('placeholder', $response.data.email);
                $(".originalForm").slideUp();
                $(".modal-dialog").removeClass('d-none');
                $("#2fa_code").focus();
            }
            //  $(".modal-dialog").slideDown();

        });
    });

    $('.submit-qrCode').on('click', function () {
        var $code_url = $('#main-form-code').attr('action');
        FormSubmit($code_url, 'main-form-code');
    });
});

Object.size = function (obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function resetModal(cloned_modal,$updateUrl,$title_msg,$id,callback = null) {
     cloned_modal.attr('id',`cloned-modal-${$id}`)
    cloned_modal.find('.invalid-feedback').html('')
    cloned_modal.find('input[name="_method"]').val('PATCH');

    cloned_modal.find('.main-title').text($title_msg);
    cloned_modal.find('.submit').addClass('second-submit');
    cloned_modal.find('.save-btn-edit').text(__('Update'));
    var formSelector = cloned_modal.find("form");

    formSelector.attr("id", `edit_form-${$id}`);
    formSelector.attr("onSubmit", `FormSubmit('${$updateUrl}','edit_form-${$id}',${callback});return false;`);
    $('#update_modal').html(cloned_modal);
    $('span.select2_add').remove();
    $(datatableID).DataTable().ajax.reload();
}
$(document).on('show.bs.modal', function (event) {  // modelId is dynamic

    var modalId = $(event.target).attr('id');

    var dir = "ltr";
    if ($global_lang == 'ar') {
        dir = "rtl"
    }
    $('.select_local').select2({
        dropdownParent: $('#' + modalId),
        dir: dir,
        "language": {
            "inputTooShort": function () {
                return __('Please enter 1 or more characters');
            },
            "noResults": function () {
                return __('No Results Found');
            },
            removeAllItems: function () {
                return __('Remove all items');
            },
            removeItem: function () {
                return __('Remove item');
            },
        },
    });
});

function resetFormModal() {
    var form = $('#filterForm');
    form[0].reset();
    filterFunction(datatableURL, datatableVar, form);
}


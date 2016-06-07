/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * 
 * @param {type} $
 * @returns {undefined}
 */
(function ($) {

    /**
     * 
     * @returns {admin_L6.ErrorFix}
     */
    function ErrorFix() {

        /**
         * 
         */
        this.view = {};

        //initialize the UI
        this.initialize();
    }

    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.initialize = function () {
        var _this = this;

        //initialize main panel tab listener
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            switch ($(e.target).attr('aria-controls')) {
                case 'piegraph':
                    _this.renderPieGraph();
                    break;

                default:
                    //by default do nothing
                    break;
            }
        });

        //initialize activation button
        $('#activate-check').bind('click', function (event) {
            event.preventDefault();
            
            if (!$('#activate-check').attr('disabled')) {
                $.ajax(ajaxurl, {
                    type: 'POST',
                    data: {
                        action: 'errorfix',
                        sub_action: 'activate',
                        _ajax_nonce: errorFixLocal.nonce
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#activate-check').text('Activating...');
                        $('#activate-check').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            location.reload();
                        } else {
                            _this.errorMessage(response.message);
                        }
                    },
                    error: function () {
                        _this.errorMessage('Failed to activate');
                    },
                    complete: function () {
                        $('#activate-check').text('Activate');
                        $('#activate-check').removeAttr('disabled');
                    }
                });
            }   
        });
        
        //submit payment
        $('#submit-payment').bind('click', function() {
           $('#payment-form').submit(); 
        });

        //fetching current balance
        $.ajax(ajaxurl, {
            type: 'POST',
            data: {
                action: 'errorfix',
                sub_action: 'balance',
                _ajax_nonce: errorFixLocal.nonce
            },
            dataType: 'json',
            beforeSend: function () {
                $('.balance').html('<small>updating...</small>');
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('.balance').html(
                        '$ ' + response.balance + ' <small>USD</small>'
                    );
                    $('.balance').attr('data-balance', response.balance);
                } else {
                    _this.errorMessage('Failed to retrieve current balance');
                }
            },
            error: function () {
                $('.balance').html('<small class="error-danger">??</small>');
            }
        });

        //add table custom filter
        $.fn.dataTable.ext.search.push(
            function (settings, data) {
                var show = true;
                if (settings.sTableId === 'error-list') {
                    if (_this.view.errorList.filter) {
                        show = (data[3] === _this.view.errorList.filter);
                    }
                }

                return show;
            }
        );

        //render error list
        this.renderErrorList();

        //render patch list
        this.renderPatchList();
        
        //initialize the settings tab
        this.initSettings();
        
        //initialize the contact form
        this.initContactForm();
    };
    
    /**
     * 
     * @param {type} message
     * @returns {undefined}
     */
    ErrorFix.prototype.errorMessage = function (message) {
        $('.wrap').append($('<div/>', {
            'class' : 'error-message'
        }).html(message));
        
        setTimeout(function() {
            $('.error-message').remove();
        }, 5000);
    };
    
    /**
     * 
     * @param {type} message
     * @returns {undefined}
     */
    ErrorFix.prototype.successMessage = function (message) {
        $('.wrap').append($('<div/>', {
            'class' : 'success-message'
        }).html(message));
        
        setTimeout(function() {
            $('.success-message').remove();
        }, 5000);
    };

    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.renderErrorList = function () {
        var table = $('#error-list').DataTable({
            autoWidth: false,
            ordering: false,
            processing: true,
            dom: 'lftip',
            pagingType: 'full_numbers',
            ajax: {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'errorfix',
                    sub_action: 'getErrorList',
                    _ajax_nonce: errorFixLocal.nonce
                }
            },
            columnDefs: [
                {visible: false, targets: [2, 3, 4]},
                {className: 'text-smaller', targets: [1]}
            ],
            language: {
                emptyTable: "There are no error in your error log",
                lengthMenu: "_MENU_",
                search: "_INPUT_",
                searchPlaceholder: "Search",
                info: "_START_ to _END_ of _TOTAL_",
                infoFiltered: "<small>filtered from _MAX_</small>",
                infoEmpty: "0 errors"
            }
        });
        //mark linegraph view as loaded
        this.view.errorList = {
            loaded: true,
            table: table,
            filter: null
        };
    };

    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.renderPatchList = function () {
        var _this = this;

        $('#patch-list').DataTable({
            autoWidth: false,
            ordering: false,
            processing: true,
            dom: 't',
            paging: false,
            ajax: {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'errorfix',
                    sub_action: 'getPatchList',
                    _ajax_nonce: errorFixLocal.nonce
                }
            },
            language: {
                emptyTable: "List of fixes is empty"
            },
            columnDefs: [
                {visible: false, targets: [0, 1]},
                {className: 'text-center', targets: [4]}
            ],
            initComplete: function (settings, json) {
                if (json.data.length) {
                    $('#patch-footer').show();
                    $('#apply-fixes').show();
                } else if (_this.view.errorList.table.data().length) {
                    $('#manual-check').show();
                }
            },
            createdRow: function (row, data) {
                var errors = data[2] + ' error' + (data[2] > 1 ? 's' : '');
                var view = $('<a/>', {
                    'href': '#',
                    'class': 'view-errors'
                }).text('view').bind('click', function (event) {
                    event.preventDefault();
                    _this.view.errorList.filter = data[0];
                    $('#error-list').DataTable().ajax.reload();
                });

                $('td:eq(0)', row).html(errors);
                $('td:eq(0)', row).append(view);

                $('td:eq(1)', row).html('$ ' + data[3] + ' <small>USD</small>');

                var action = $('<i/>', {
                    'class': 'icon-ok-circled icon-action text-' + (data[1] === true ? 'success' : 'muted')
                }).bind('click', function () {
                    _this.updateTotal(data[3] * (data[1] ? -1 : 1));
                    data[1] = !data[1];
                    $(this).toggleClass('text-muted text-success');
                });

                $('td:eq(2)', row).html(action);
            }
        });
        
        $('#rejected-list').DataTable({
            autoWidth: false,
            ordering: false,
            processing: true,
            dom: 't',
            paging: false,
            ajax: {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'errorfix',
                    sub_action: 'getRejectedList',
                    _ajax_nonce: errorFixLocal.nonce
                }
            },
            columnDefs: [
                {visible: false, targets: [2]},
                {className: 'text-center', targets: [1]}
            ],
            createdRow: function (row, data) {
                var errors = data[0] + ' error' + (data[0] > 1 ? 's' : '');
                var view = $('<a/>', {
                    'href': '#',
                    'class': 'view-errors'
                }).text('view').bind('click', function (event) {
                    event.preventDefault();
                    _this.view.errorList.filter = data[1];
                    $('#error-list').DataTable().ajax.reload();
                });

                $('td:eq(0)', row).html(errors);
                $('td:eq(0)', row).append(view);

                var action = $('<i/>', {
                    'class': 'icon-info-circled icon-action text-info'
                }).bind('click', function () {
                    $('.rejection-message').text(data[1]);
                    $('#ignore-rejected').attr('data-code', data[2]);
                    $('#rejected-reason-modal').modal('show');
                });

                $('td:eq(1)', row).html(action);
            }
        });
        
        $('#ignore-rejected').bind('click', function() {
            $.ajax(ajaxurl, {
                type: 'POST',
                async: false,
                dataType: 'json',
                data: {
                    action: 'errorfix',
                    sub_action: 'ignoreErrors',
                    _ajax_nonce: errorFixLocal.nonce,
                    code: $(this).attr('data-code')
                },
                beforeSend: function () {
                    $('#ignore-rejected').text('Please wait...');
                },
                complete: function () {
                    location.reload();
                }
            });
        });

        $('#select-all-fixes').bind('click', function (event) {
            event.preventDefault();
            //select all
            $.each($('#patch-list').DataTable().data(), function (i, row) {
                if (row[1] === false) {
                    $('#patch-list tbody tr:eq(' + i + ') .icon-action').trigger('click');
                }
            });
        });

        $('#apply-fixes').bind('click', function (event) {
            event.preventDefault();
            
            if (!$(this).attr('disabled')) {
                var count = 0;
                var cost = 0;

                $.each($('#patch-list').DataTable().data(), function (i, row) {
                    if (row[1]) {
                        count++;
                        cost += parseFloat(row[3]);
                    }
                });

                $('#fix-count').text(count + ' fix' + (count > 1 ? 'es' : ''));
                $('#fix-total-cost').html('$ ' + cost.toFixed(2) + ' <small>USD</small>');
                $('.apply-step.step-one').show();
                $('.apply-step.step-two').hide();
                $('#confirm-apply').show();
                $('#complete').hide();

                $('#apply-modal').modal({backdrop: 'static', show: true});
            }
        });

        //confirm apply
        $('#confirm-apply').bind('click', function () {
            $('.apply-progress').empty();
            $('.apply-step.step-one').hide();
            $('.apply-step.step-two').show();
            $('#confirm-apply').text('Applying...').attr('disabled', 'disabled');
            
            $.each($('#patch-list').DataTable().data(), function (i, row) {
                if (row[1]) {
                    $.ajax(ajaxurl, {
                        type: 'POST',
                        async: false,
                        dataType: 'json',
                        data: {
                            action: 'errorfix',
                            sub_action: 'apply',
                            _ajax_nonce: errorFixLocal.nonce,
                            patch: row[0]
                        },
                        beforeSend: function () {
                            $('.apply-progress').append(
                                '<li>Applying Fix ID-' + row[0] + '</li>'
                            );
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                $('.apply-progress li:last')
                                    .addClass('text-success')
                                    .html('Fix applied successfully.');
                            } else {
                                $('.apply-progress li:last')
                                    .addClass('text-danger')
                                    .html(response.message);
                            }
                        },
                        error: function () {
                            $('.apply-progress li:last')
                                    .addClass('text-danger')
                                    .html('Unexpected Application Error');
                        }
                    });
                }
            });
            
            $('.apply-progress').append(
                '<li>Process completed. Backup of the original files stored to wp-content/errorfix directory</li>'
            );
            
            $('#confirm-apply').text('Apply').removeAttr('disabled').hide();
            $('#complete').show();
        });
        
        $('#complete').bind('click', function () {
            location.reload();
        });
        
        //manual check
        $('#manual-check').bind('click', function () {
            $.ajax(ajaxurl, {
                type: 'POST',
                data: {
                    action: 'errorfix',
                    sub_action: 'check',
                    _ajax_nonce: errorFixLocal.nonce
                },
                dataType: 'json',
                success: function () {
                    location.reload();
                },
                error: function () {
                    _this.errorMessage('Failed to perform the check');
                },
                complete: function () {
                    $('#check-modal').modal('hide');
                }
            });
        });
    };

    /**
     * 
     * @param {type} number
     * @returns {undefined}
     */
    ErrorFix.prototype.updateTotal = function (number) {
        var total = parseFloat($('#total-cost').attr('data-total')) + number;
        total = total.toFixed(2);

        var balance = $('.balance').attr('data-balance');

        $('#total-cost').attr('data-total', total);
        $('#total-cost').html('$ ' + total + ' <small>USD</small>');

        if (total > balance) {
            $('#total-cost').addClass('text-danger');
        } else {
            $('#total-cost').removeClass('text-danger');
        }

        if (total <= balance) {
            $('#apply-fixes').removeAttr('disabled');
        } else {
            $('#apply-fixes').attr('disabled', 'disabled');
        }
    };

    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.renderPieGraph = function () {
        var _this = this;

        if (typeof this.view['piegraph'] === 'undefined') {
            $.ajax(ajaxurl, {
                type: 'POST',
                data: {
                    action: 'errorfix',
                    sub_action: 'getPieData',
                    _ajax_nonce: errorFixLocal.nonce
                },
                dataType: 'json',
                success: function (response) {
                    $('#graph-pie-loader').remove();

                    if (response.length) {
                        $('.ahm-graph-empty').remove();
                        var graph = Morris.Donut({
                            element: 'graph-pie',
                            data: response
                        });
                    } else {
                        $('.ahm-graph-empty').removeClass('hidden');
                        $('.graph-description').remove();
                    }

                    //mark linegraph view as loaded
                    _this.view.piegraph = {
                        loaded: true,
                        graph: graph
                    };
                },
                error: function() {
                    _this.errorMessage('Failed to load Pie Graph data');
                }
            });
        }
    };
    
    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.initSettings = function () {
        var _this = this;
        
        $('input,select', '.settings-container').each(function() {
            $(this).bind('change', function() {
                var value = null;
            
                if ($(this).is(':checkbox')) {
                    value = $(this).prop('checked');
                } else {
                    value = $(this).val();
                }
                
                $.ajax(ajaxurl, {
                    type: 'POST',
                    data: {
                        action: 'errorfix',
                        sub_action: 'updateSetting',
                        _ajax_nonce: errorFixLocal.nonce,
                        setting: $(this).attr('name'),
                        value: value
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status !== 'success') {
                            _this.errorMessage('Failed to save setting');
                        }
                    },
                    error: function () {
                        _this.errorMessage('Failed to save setting');
                    }
                });
            });
        });
    };
    
    /**
     * 
     * @returns {undefined}
     */
    ErrorFix.prototype.initContactForm = function () {
        var _this = this;
        
        $('#send-message').bind('click', function () {
            var fullname = $.trim($('#contact-fullname').val());
            var email    = $.trim($('#contact-email').val());
            var message  = $.trim($('#contact-message').val());
            
            if (fullname && email && message) {
                $.ajax(ajaxurl, {
                    type: 'POST',
                    data: {
                        action: 'errorfix',
                        sub_action: 'sendMessage',
                        _ajax_nonce: errorFixLocal.nonce,
                        fullname: fullname,
                        email: email,
                        message: message
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#send-message').text('Sending...');
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            _this.successMessage('Message has been sent successfully');
                            $('#send-message-modal').modal('hide');
                            $('#contact-message').val(''); //clear message
                        } else {
                            _this.errorMessage('Failed to send message');
                        }
                    },
                    error: function () {
                        _this.errorMessage('Failed to send message');
                    },
                    complete: function () {
                        $('#send-message').text('Send Message');
                    }
                });
            } else {
                _this.errorMessage('All fields are required to contact us');
            }
        });
    };

    /**
     * 
     */
    $('document').ready(function () {
        new ErrorFix();
    });

})(jQuery);
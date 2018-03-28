/**
 * Created by alex on 8/3/16.
 */
 'use strict';

 class JobsUpdate {

    constructor() {
        JobsUpdate.time = 0;
        this.initAccounting();
        this.initCostCenters();
        this.initInvoices();
        this.initTranslations();
        this.initFileUploads();
        this.initComments();
        this.initPopovers();
        this.initLinks();
        this.initForm();
        this.initCWA();
        this.initTextarea();
        this.initProcessingUnit();
        this.initAssignments();
        this.initJobRemove();
    }

    initJobRemove() {
        $('.delete_job').click(e => {
            e.preventDefault();

            swal({
                type: 'warning',
                title: 'Are you sure you want to delete this request?',
                text: "Deleted requests cannot be recovered. If yes, please enter the request number to complete the deletion.",
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete request',
                confirmButtonColor: '#f27474',
                inputValidator: function (result) {
                    return new Promise(function (resolve, reject) {
                        if (result === $('meta[name="job-legacy-id"]').attr('content')) {
                            resolve()
                        } else {
                            reject('Please enter job number in format XXXXXX-XXX.')
                        }
                    })
                }
            }).then(function (result) {
                window.location = '/jobs/jobs/delete?id=' + $('meta[name="job-id"]').attr('content');
            })
        })
    }

    initTextarea() {
        autosize($('#updatejobform-description'));
    }

    initAccounting() {
        $('#updatejobform-internal_only input').click(function () {
            if ($('#updatejobform-internal_only input:checked').val() == '0') {
                $('.internal_only_block').show();
            } else {
                $('.internal_only_block').hide();
            }
        });
    }

    initLinks() {
        $('#updatejobform-page_update').change(function (e) {
            if ($(this).is(':checked')) {
                $('#links').show();
            } else {
                $('#links').hide();
            }
        });
        $('#add_link').click(function (e) {
            e.preventDefault();
            let template = Handlebars.compile($('#link-template').html());
            $(this).before(template({}))
        });
        $('#links').on('click', '.delete', function (e) {
            e.preventDefault();
            $(this).closest('.row').remove();
        });
        $('#links').on('click', '.edit', function (e) {
            e.preventDefault();
            $(this).closest('.text').hide();
            $(this).closest('.item').find('.edit_field').show();
        });
    }

    initCostCenters() {
        $('#add_cost_center').click(function (e) {
            e.preventDefault();
            let $cost_center_name = $('#new_cost_center_name');
            let $percent = $('#new_cost_center_percent');
            let job_id = $('meta[name="job-id"]').attr('content');

            if ($cost_center_name.val().length > 0 && $percent.val() > 0) {
                let sum = 0;
                $('.cost-centers tr').each(function () {
                    sum += parseInt($(this).data('percent'));
                });
                if (sum + parseInt($percent.val()) > 100) {
                    swal('Error', 'Total of all cost centers more that 100%', 'error');
                    return;
                }
                $.post('/jobs/jobs/add-cost-center', {
                    'JobCostCenter[job_id]': job_id,
                    'JobCostCenter[cost_center]': $cost_center_name.val(),
                    'JobCostCenter[percent]': $percent.val()
                }).done(function (data) {
                    let source = $('#cost-center-template').html();
                    let template = Handlebars.compile(source);
                    $('.cost-centers table tbody').append(template(data));
                    $('.cost-centers select').val('');
                })
            } else {
                swal('Error', 'Total percentage for cost centers can’t exceed 100%', 'error');
            }
        });
        $('#new_cost_center_name').keyup(function (e) {
            $(this).val($(this).val().replace(/[^a-zA-Z0-9]/gi, ''));

        });
        $('.cost-centers').on('click', '.delete', function (e) {
            e.preventDefault();
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $.get('/jobs/jobs/delete-cost-center?id=' + $(this).closest('tr').data('id'));
                $(this).closest('tr').remove();
            });
        });
    }

    initCWA() {
        let __this = this;
        $('#add_cwa').click(function (e) {
            e.preventDefault();
            let cwa_id = $('#new_cwa option:selected').val();
            let cwa_name = $('#new_cwa option:selected').html();
            let job_id = $('meta[name="job-id"]').attr('content');

            if(cwa_id==''){
                return;
            }

            $.post('/jobs/jobs/add-cwa?job_id=' + job_id + '&cwa_id=' + cwa_id).done((data) => {
                let source = $('#cwa-template').html();
                let template = Handlebars.compile(source);
                $('.cwa table tbody').append(template({name: cwa_name, id: cwa_id}));
                $('#new_cwa option:selected').remove();
            });
        });
        $('.cwa').on('click', '.delete', function (e) {
            e.preventDefault();
            let job_id = $('meta[name="job-id"]').attr('content');
            let cwa_id = $(this).closest('tr').data('id');
            let __this = this;

            if (parseInt($(this).closest('tr').data('invoices')) > 0) {
                swal('Error', 'Remove all invoices from CWA before removing CWA.', 'error');
                return;
            }
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                axios.get('/jobs/jobs/delete-cwa?job_id=' + job_id + '&cwa_id=' + cwa_id)
                .then(() => {
                    $('#new_cwa').append('<option value="' + cwa_id + '" amount="' + $(__this).closest('tr').data('amount') + '" used="' + $(__this).closest('tr').data('used') + '">' + $(__this).closest('tr').data('name') + '</option>');
                    $(__this).closest('tr').remove();
                });
            });
        });
        $('.cwa').on('click', '.invoices', function (e) {
            e.preventDefault();

            let source = $('#cwa-modal-template').html();
            let template = Handlebars.compile(source);

            let $modal = $('#cwa_modal');
            $('.modal-body', $modal).html(template({}))
            $('.modal-title', $modal).html('CWA ' + $(this).closest('tr').data('name'))
            $modal.modal();
            $modal.data('cwa', $(this).closest('tr').data('id'));

            __this.initAccountingPieChart();
            jQuery('#new_invoice_date').parent().datepicker();

            let source_invoice = $('#invoice-template').html();
            let template_invoice = Handlebars.compile(source_invoice);

            axios.get('/jobs/jobs/get-invoices?job_id=' + $('meta[name="job-id"]').attr('content') + '&cwa_id=' + $(this).closest('tr').data('id'))
            .then((response) => {
                Object.keys(response.data).forEach(item => {
                    item = response.data[item];
                    $('.invoices table tbody').append(template_invoice(item));
                });
                __this.updateProgress();
            })
        });
    }

    initInvoices() {
        let $this = this;
        $('#cwa_modal').on('click', '#add_invoice', function (e) {
            e.preventDefault();
            let cwa_id = $('#cwa_modal').data('cwa');
            let $date = $('#new_invoice_date');
            let $number = $('#new_invoice_number');
            let $amount = $('#new_invoice_amount');
            let job_id = $('meta[name="job-id"]').attr('content');

            if ($number.val().length > 0 && $amount.val() > 0) {
                $.post('/jobs/jobs/add-invoice', {
                    'JobInvoice[cwa_id]': cwa_id,
                    'JobInvoice[job_id]': job_id,
                    'JobInvoice[date]': moment($this.parseDate($date.val())).format('YYYY-MM-DD'),
                    'JobInvoice[number]': $number.val(),
                    'JobInvoice[amount]': $amount.val()
                }).done(function (data) {
                    data.date = $date.val();
                    let source = $('#invoice-template').html();
                    let template = Handlebars.compile(source);
                    $('.invoices table tbody').append(template(data));
                    $date.val('');
                    $number.val('');
                    $amount.val('');
                    $this.updateProgress();
                    let total = 0;
                    $('.invoices tr').each(function() {
                        total += parseInt($(this).data('amount'));
                    });
                    $('.cwa table tr[data-id="' + cwa_id + '"]').attr('data-invoices', $('.invoices tr').length).find('.invoices span').html(total);
                });
            } else {
                swal('Error', 'Fill invoice number and amount fields.', 'error');
            }
        })
        $('#cwa_modal').on('click', '.invoices .delete', function (e) {
            e.preventDefault();

            let cwa_id = $(this).closest('.modal').data('cwa');
            let invoice_id =  $(this).closest('tr').data('id');
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                axios.get('/jobs/jobs/delete-invoice?id=' + invoice_id).then(()=>{
                    $(this).closest('tr').remove();
                    $('.cwa table tr[data-id="' + cwa_id + '"]').attr('data-invoices', $('.invoices tr').length).find('.invoices span').html($('.invoices tr').length);
                    $this.updateProgress();
                });
            });
        });
    }

    updateProgress() {
        let cwa_id = $('#cwa_modal').data('cwa');

        let total_invoices = 0;
        $('.invoices tr').each(function () {
            total_invoices += parseInt($(this).data('amount'));
        });

        //Based on CWA
        let config = $('.cwa table tr[data-id="' + cwa_id + '"]');
        let cwa_limit = parseFloat(config.data('amount'));
        let cwa_used = parseFloat(config.data('used'));

        let free = cwa_limit - cwa_used - total_invoices;
        if (free < 0)
            free = 0;

        $('.balance-cwa .balance span').html(cwa_limit - cwa_used - total_invoices);

        this.accountingChart.series[0].setData([
        {
            name: 'CWA used by other jobs',
            y: cwa_used,
            color: '#31708f',
            cursor: 'pointer'
        },
        {
            name: 'CWA used by this job',
            y: total_invoices,
            color: '#4D863D'
        },
        {
            name: 'Free',
            y: free,
            color: '#CCCCCC'
        }
        ]);
    }

    initTranslations() {
        $('#updatejobform-translation_needed input').click(function () {
            if ($('#updatejobform-translation_needed input:checked').val() == '0') {
                $('.translation_needed_block').hide();
            } else {
                $('.translation_needed_block').show();
            }
        });

        $('#add_translation_submit').click(function (e) {
            e.preventDefault();
            let $due_date = $('#new_translation_due_date');
            let $rush = $('#new_translation_rush');
            let $status = $('#new_translation_status');
            let $language = $('#new_translation_language');
            let job_id = $('meta[name="job-id"]').attr('content');

            if ($due_date.val().length > 0 && $rush.find('input:checked').length > 0) {
                $.post('/jobs/jobs/add-translation', {
                    'JobTranslation[job_id]': job_id,
                    'JobTranslation[due_date]': $due_date.val(),
                    'JobTranslation[rush]': $rush.find('input:checked').val(),
                    'JobTranslation[status]': $status.val(),
                    'JobTranslation[language]': $language.val(),
                }).done(function (data) {
                    let source = $('#translation-template').html();
                    let template = Handlebars.compile(source);
                    let rendered = $(template(data));
                    rendered.find('select').val(data.status);
                    $('.translations table tbody').append(rendered);
                    $('#add_translation').modal('toggle');
                });
            } else {
                swal('Error', 'Fill all fields', 'error');
            }
        });
        $('.translations').on('click', '.delete', function (e) {
            e.preventDefault();
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $.get('/jobs/jobs/delete-translation?id=' + $(this).closest('tr').data('id'));
                $(this).closest('tr').remove();
            });
        });
        $('.translations table').on('change', 'select', function (e) {
            e.preventDefault();
            $.get('/jobs/jobs/update-translation-status?id=' + $(this).closest('tr').data('id') + '&status=' + $(this).val());
        });
    }

    initFileUploads() {
        $('#fileupload').fileupload({
            url: '/jobs/jobs/file-upload?job_id=' + $('meta[name="job-id"]').attr('content'),
            dataType: 'json',
            start: function () {
                $('.fileinput-button span').html('<i class="fa fa-refresh fa-spin"></i>');
            }
        }).on('fileuploaddrop', function (e, data) {
            $('.fileinput-button span').html('<i class="fa fa-refresh fa-spin"></i>');
        }).on('fileuploaddone', function (e, data) {
            $('.fileinput-button span').html('Upload Attachment');
            JobsUpdate.refreshComments();
        }).on('fileuploadfail', function (e, data) {
            $('.fileinput-button span').html('Upload Attachment');
            swal('Error', 'Error while uploading', 'error');
        });
    }

    initComments() {
        $('#add_comment').click((e) => {
            e.preventDefault();

            let $textarea = $('.comments textarea');
            if ($textarea.val().length === 0) {
                swal('Error', 'Can\'t send empty comment!', 'error');
                return;
            }

            if (!$textarea.val().match(/(^|[^a-z0-9_])@([a-z0-9_.]+)/i)) {
                swal('Error', 'In order to post a message in the comments, you must type @ followed by the user\'s first or last name.', 'error');
                return;
            }

            let data = {
                'body': $textarea.val(),
                public: 1
            };
            $.post('/jobs/jobs/add-comment?job_id=' + $('meta[name="job-id"]').attr('content'), data).done(function (data) {
                JobsUpdate.refreshComments();
                $textarea.val('');
            });
        });
        setInterval(function () {
            JobsUpdate.refreshComments();
        }, 5000);

        $('.comments .list').on('click', '.delete_file', function (e) {
            e.preventDefault();
            swal({
                title: 'Are you sure you want to delete this file?',
                html: "Deleted files cannot be recovered.<br>File Name: " + $(this).closest('.file').find('.title').html(),
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete file.',
                confirmButtonColor: '#f27474'
            }).then(() => {
                axios
                .get('/jobs/jobs/delete-file', {
                    params: {
                        id: $(this).closest('.file').data('id')
                    }
                })
                .then(() => {
                    $(this).closest('.file').remove();
                })
                .catch(() => {
                    swal(
                        'Error!',
                        'Error occurred during file removing!',
                        'error'
                        )
                });
            });
        });

    }

    static initMentions(users) {
        let tmp = [];
        $.each(users, function (key, item) {
            tmp.push({name: item, username: key});
        });

        $('#new_comment').atwho({
            at: "@",
            data: tmp,
            headerTpl: '<div class="atwho-header">Member List<small>↑&nbsp;↓&nbsp;</small></div>',
            insertTpl: "${atwho-at}${username}",
            displayTpl: "<li>${name} <small>${username}</small></li>",
            limit: 200
        });
    }

    initPopovers() {
        $('[data-toggle="popover"]').popover({
            trigger: 'hover'
        });
    }

    static refreshComments() {
        $.get('/jobs/jobs/get-comments?job_id=' + $('meta[name="job-id"]').attr('content') + '&time=' + JobsUpdate.time).done(function (data) {
            if (data.no_updates) {
                return;
            }
            JobsUpdate.time = data.timestamp;
            $('.comments .list').html(data.content);
            $('[rel="tooltip"],[data-toggle="tooltip"]').tooltip();
        });
    }

    initForm() {
        $('.main_form').submit((e) => {
            let status = $('#updatejobform-status').val();
            if (status == "4" || status == "6") {
                if ($('#updatejobform-approver').val().length == 0 ||
                    $('#updatejobform-completed_on').val().length == 0) {

                    $('a[href="#update-tab5"]').click();
                swal('Error', 'Fill Close Out tab first', 'error');

                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }

        let total = 0;
        $('.cost-centers tr').each(function () {
            total += parseInt($(this).data('percent'));
        });

        if (total !== 100) {
            swal('Error', 'Total percentage of all cost centers must be 100', 'error');
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        return true;
    });
    }

    initProcessingUnit() {
        $('#updatejobform-processing_unit_id').change(e => {
            axios
            .get('/jobs/request-types/get?processing_unit=' + $('#updatejobform-processing_unit_id').val() + '&rand=' + new Date().getTime())
            .then(response => {
                if (response.status === 200) {
                    let out = [];
                    $.map(response.data, function (value, index) {
                        return [value];
                    }).sort(function (a, b) {
                        if (a.order < b.order)
                            return -1;
                        if (a.order > b.order)
                            return 1;
                        return 0;
                    }).forEach(obj => {
                        out.push('<option value="' + obj.id + '"> ' + obj.name + '</option>');
                    });
                    $('#updatejobform-request_type_id').html(out.join(''));
                } else {
                    $('#updatejobform-request_type_id').html('');
                    throw 'Processing Department not found';
                }
            })
            .catch(error => {
                $('#updatejobform-request_type_id').html('');
                throw 'Processing Department not found';
            })
        });
    }

    initAccountingPieChart() {
        this.accountingChart = Highcharts.chart($('.balance-cwa .progress_holder').get(0), {
            chart: {
                type: 'pie'
            },
            title: {
                text: false
            },
            credits: {
                enabled: false
            },

            series: [{
                tooltip: {
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> <strong>{point.y}$</strong><br/>'
                },
                data: []
            }],

            plotOptions: {
                pie: {
                    size: '250px',
                    showInLegend: true,
                    point: {
                        events: {
                            mouseOver: function () {
                                if ('pointer' === this.cursor) {
                                    this.graphic.element.style.cursor = 'pointer';
                                }
                            },
                            click: function () {
                                if ('pointer' === this.cursor) {
                                    window.location.href = '/jobs/jobs/search?filter%5B1%5D%5Bfield%5D=job.cwa_id&filter%5B1%5D%5Btype%5D=match&filter%5B1%5D%5Bvalue%5D=' + $('#updatejobform-cwa_id').val();
                                }
                            }
                        }
                    },
                },
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.percentage:.1f} %',
                        distance: 15
                    }
                }
            },
        });
    }

    initAssignments() {
        $('#save_and_reset_custom_fields').click(e => {
            e.preventDefault();
            e.stopPropagation();

            swal({
                title: 'Are you sure?',
                text: "If you reset departmental fields it will remove all data from the departmental fields tab.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Save and reset!'
            }).then(() => {
                $('#save_and_reset_custom_fields').closest('form').submit();
            });
        });
    }

    parseDate(input) {
        var parts = input.split('/');
      return new Date(parts[2], parts[1]-1, parts[0]); // Note: months are 0-based
  }
}
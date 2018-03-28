/**
 * Created by alex on 9/22/16.
 */
class CreateJob {
    constructor() {
        this.form_object = $('#create_new_job').closest('form');
        this.submit = false;

        this.bindStep0();
        this.bindStep1();
        this.blockForm();

        this.initCancel();
    }

    initCancel() {
        this.form_object.on('click', '.cancel', (e) => {
            e.preventDefault();

            location.reload();
        })
    }

    blockForm() {
        this.form_object.submit(e => {
            console.log('Try to submit');
            if (this.submit)
                return;
            console.log('Nope... Just a try');
            e.preventDefault();
            e.stopPropagation();
        })
    }

    bindStep0() {
        let container = $('#create_new_job .step_0');
        this.bindRequestUnits(container);
        this.loadRequestTypes(container);

        $('.next_step', container).click((e) => {
            e.preventDefault();

            $('.next_step', container).button('loading');
            this.loadCustomForms(() => {
                $('.step_0', this.form_object).hide();
                $('.step_1', this.form_object).show();

                $('.next_step', container).button('reset');
            });
        });
    }

    bindRequestUnits(container) {
        $('#processing_unit', container).change(() => {
            this.loadRequestTypes(container);
        });
    }

    loadRequestTypes(container) {
        axios
            .get('/jobs/request-types/get?processing_unit=' + $('#processing_unit', container).val() + '&rand=' + new Date().getTime())
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
                        out.push('<label><input type="checkbox" name="request_type[]" value="' + obj.id + '"> ' + obj.name + '</label>');
                    });
                    $('.request_types .holder', container).html(out.join(''));
                    this.bindRequestTypes(container);
                    this.checkForNextStep_0_1();
                } else {
                    $('.request_types .holder', container).html('');
                    throw 'Processing Department not found';
                }
            })
            .catch(error => {
                $('.request_types .holder', container).html('');
                throw error;
            })
    }

    bindRequestTypes(container) {
        $('.request_types input', container).change(e => {
            this.checkForNextStep_0_1();
        })
    }

    checkForNextStep_0_1() {
        if ($('.request_types input:checked', this.form_object).length > 0) {
            $('.step_0 .next_step', this.form_object).show();
        } else {
            console.info('false');
            $('.step_0 .next_step', this.form_object).hide();
        }
    }

    bindStep1() {
        $('.step_1 .next_step', this.form_object).click(e => {
            e.preventDefault();

            if (!this.checkForNextStep_1()) {
                this.form_object.yiiActiveForm('validateAttribute', 'createjobform-name');
                this.form_object.yiiActiveForm('validateAttribute', 'createjobform-description');
                this.form_object.yiiActiveForm('validateAttribute', 'createjobform-due_date');
                this.form_object.yiiActiveForm('validateAttribute', 'createjobform-approver');
                return;
            }

            $('.step_1', this.form_object).hide();
            let obj = $('.additional_steps .additional_step:nth-child(1)', this.form_object);
            obj.show().find('label[data-toggle="tooltip"]').tooltip();
            this.bindAdditionalStep(obj);
            $('.additional_step:last-child .next_step', this.form_object).html('Finish');
        });
        $('.step_1 .back', this.form_object).click(e => {
            e.preventDefault();

            $('.step_1', this.form_object).hide();
            $('.step_0', this.form_object).show();
        });
    }

    checkForNextStep_1() {
        let allOk = true;
        $('.step_1 input', this.form_object).each(function () {
            if ($(this).val().length === 0)
                allOk = false;
        });
        return allOk;
    }

    loadCustomForms(callback) {
        let params = [];
        $('.request_types input:checked', this.form_object).each(function () {
            params.push('ids[]=' + $(this).val());
        });

        this.alerts = {};

        axios
            .get('/jobs/custom-forms/generate?' + params.join('&') + '&rand=' + new Date().getTime())
            .then((response) => {
                let content = [];
                Object.keys(response.data).forEach(key => {
                    let obj = response.data[key].html;
                    this.alerts[key] = response.data[key].alert;
                    content.push('<div class="additional_step" style="display: none" data-request="' + key + '"><p>If you have additional information or attachments for the <strong>' + response.data[key].name + '</strong>, please update the fields below.</p>' + obj + '</div>')
                });
                $('.additional_steps', this.form_object).html(content);
                callback();
            });
    }

    bindAdditionalStep(container) {
        let _t = this;
        let check = function (e, obj) {
            if (typeof obj === 'undefined')
                obj = $(this).closest('.additional_step');
            if (_t.customFormValidate(obj)) {
                obj.find('.next_step').removeClass('off');
                return true;
            } else {
                obj.find('.next_step').addClass('off');
                return false;
            }
        };
        $('.form-group input', container).change(check);
        $('.form-group select', container).change(check);
        $('.form-group input', container).keyup(check);
        $('#add_cost_center', container).click(() => {
            setTimeout(check, 50);
        });

        this.bindLinks(container);
        this.bindCWA(container);
        this.bindCostCenters(container);

        if (this.alerts[container.data('request')] && this.alerts[container.data('request')].length > 0) {
            swal({
                title: 'Warning!',
                text: this.alerts[container.data('request')],
                type: 'warning'
            });
        }

        $('.next_step', container).click(function (e) {
            e.preventDefault();

            console.log('On start!');

            if (!check({}, container)) {
                _t.showErrors(container);
                console.log('Opps. Error.');
                return;
            }

            console.log('And here we go!');

            if (container.next('.additional_step').length > 0) {
                console.log('Next step');

                container.hide();
                let obj = container.next('.additional_step');
                obj.show().find('label[data-toggle="tooltip"]').tooltip();
                _t.bindAdditionalStep(obj);
                $(window).scrollTop(0);
            } else {
                console.log('Submit form');

                _t.submit = true;
                _t.form_object.submit();
            }
        });
        $('.back', container).click(e => {
            if (container.prev('.additional_step').length > 0) {
                container.hide();
                let obj = container.prev('.additional_step');
                obj.show().find('label[data-toggle="tooltip"]').tooltip();
                $(window).scrollTop(0);
            } else {
                container.hide();
                $('.step_1', this.form_object).show();
            }
        });
    }

    customFormValidate(container) {
        let out = true;
        $('.form-group.required', container).each(function () {
            let type = $(this).data('type') + '[type!=\'hidden\']';
            if ($(this).hasClass('type_checkbox')) {
                if ($('input:checked', this).length === 0) {
                    out = false;
                }
            } else if ($(this).hasClass('type_checkbox_list')) {
                if ($('input:checked', this).length === 0) {
                    out = false;
                }
            } else {
                if ($(type, this).val() === null || $(type, this).val().length === 0) {
                    out = false;
                }
            }
        });
        if ($('.cost-centers', container).length > 0) {
            if (!this.checkCostCenters(container)) {
                out = false;
            }
        }
        if ($('.cms_links', container).length > 0) {
            if (!this.checkLinks(container)) {
                out = false;
            }
        }
        console.log('Form validation - ', out);
        return out;
    }

    checkCostCenters(container) {
        return $('.cost-centers tr', container).length !== 0;
    }

    checkCostCentersTotal(container) {
        let total = 0;
        $('.cost-centers tr', container).each(function () {
            total += parseInt($(this).data('percent'));
        });
        return total === 100;
    }

    checkLinks(container) {
        let links = $('#links', container);
        return !($('.show_links', container).is(':checked') && (links.find('tr').length === 0 || links.find('tr input').val() === ''));
    }

    showErrors(container) {
        let out = true;
        $('.form-group.required', container).each(function () {
            let type = $(this).data('type') + '[type!=\'hidden\']';
            if ($(this).hasClass('type_checkbox')) {
                if ($('input:checked', this).length === 0) {
                    out = false;
                }
            } else if ($(this).hasClass('type_checkbox_list')) {
                if ($('input:checked', this).length === 0) {
                    out = false;
                }
            } else {
                if ($(type, this).val() === null || $(type, this).val().length === 0) {
                    out = false;
                }
            }
            if (out === false) {
                $(this).closest('.form-group').addClass('has-error');
            } else {
                $(this).closest('.form-group').removeClass('has-error');
            }
        });
        if (!out) {
            return;
        }
        if ($('.cost-centers', container).length > 0) {
            if (!this.checkCostCenters(container)) {
                alert('Add at least one cost center');
                return;
            }
            if (!this.checkCostCentersTotal(container)) {
                alert('Total percentage of all cost centers must be 100');
                return;
            }
        }
        if ($('.cms_links', container).length > 0) {
            if (!this.checkLinks(container)) {
                alert('Add at least one CMS link');
                return;
            }
        }
    }

    bindLinks(container) {
        $('.show_links', container).change(function (e) {
            let $obj = $(this).closest('.additional_step');
            if ($(this).is(':checked')) {
                $('#links', $obj).show();
            } else {
                $('#links', $obj).hide();
            }
        });
        $('#add_link', container).click(function (e) {
            e.preventDefault();
            let template = Handlebars.compile($('#link-template').html());
            $('.cms_links table tbody', container).append(template({}));
        });
        $('.cms_links table', container).on('click', '.delete', function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    }

    bindCWA(container) {
        $('#add_cwa', container).click(function (e) {
            e.preventDefault();
            let cwa_id = $('#new_cwa option:selected', container).val();
            let cwa_name = $('#new_cwa option:selected', container).html();

            if (cwa_id == '') {
                return;
            }

            let source = $('#cwa-template').html();
            let template = Handlebars.compile(source);
            $('.cwa table tbody', container).append(template({
                name: cwa_name,
                id: cwa_id,
                request: $(this).closest('.additional_step').data('request')
            }));
            $('#new_cwa option:selected', container).remove();
        });
        $('.cwa', container).on('click', '.delete', function (e) {
            e.preventDefault();
            let cwa_id = $(this).closest('tr').data('id');
            let __this = this;

            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $('#new_cwa', container).append('<option value="' + cwa_id + '">' + $(__this).closest('tr').data('name') + '</option>');
                $(__this).closest('tr').remove();
            });
        });
    }

    bindCostCenters(container) {
        $('#add_cost_center', container).click(function (e) {
            e.preventDefault();
            let $obj = $(this).closest('.cost-centers');
            let $cost_center_name = $('#new_cost_center_name', $obj);
            let $percent = $('#new_cost_center_percent', $obj);

            if ($cost_center_name.val().length > 0 && $percent.val() > 0) {
                let sum = 0;
                $('tr', $obj).each(function () {
                    sum += parseInt($(this).data('percent'));
                });
                if (sum + parseInt($percent.val()) > 100) {
                    swal({
                        title: 'Warning!',
                        text: "Total of all cost centers more that 100%",
                        type: 'warning'
                    });
                    return;
                }
                let source = $('#cost-center-template').html();
                let template = Handlebars.compile(source);
                $('table tbody', $obj).append(template({
                    id: $('table tr', $obj).length,
                    cost_center_name: $cost_center_name.val(),
                    percent: $percent.val(),
                    request: $(this).closest('.additional_step').data('request')
                }));
            } else {
                swal({
                    title: 'Warning!',
                    text: "Total percentage for cost centers can’t exceed 100%",
                    type: 'warning'
                });
            }
        });
        $('#new_cost_center_name', container).keyup(function (e) {
            $(this).val($(this).val().replace(/[^a-zA-Z0-9]/gi, ''));
        });
        container.on('click', '.cost-centers .delete', function (e) {
            e.preventDefault();
            let __this = this;
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $(__this).closest('tr').remove();
            });
        });
    }
}

new CreateJob();
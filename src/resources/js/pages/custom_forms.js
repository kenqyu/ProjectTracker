/**
 * Created by alex on 3/31/17.
 */

class CustomForms {
    constructor() {
        this.initFields();
        this.initValidation();
        this.initOptions();
        this.initSort();
    }

    initSort() {
        $('.custom_forms .list_container').sortable({
            handle: ".dragger"
        });
        $('.option_list').sortable({
            handle: ".option_dragger"
        });
    }

    initFields() {
        $('.add_field').click((e) => {
            e.preventDefault();

            let source = $('#field-template').html();
            let template = Handlebars.compile(source);
            $('.fields .list_container').append(template({id: $('.list_container .item').length + 1}));
        });
        $('.list_container')
            .on('click', '.delete', function (e) {
                e.preventDefault();

                if (confirm('You sure?')) {
                    $(this).closest('.item').remove();
                }
            })
            .on('change', 'select', function (e) {
                if ($(this).find(":selected").attr('value') == 4 || $(this).find(":selected").attr('value') == 6) {
                    $(this).closest('.item').addClass('show_options');
                } else {
                    $(this).closest('.item').removeClass('show_options');
                }
            })
    }

    initValidation() {
        $('#custom_form_form').submit((e) => {
            let valid = true;
            $('.list_container .item .label_input').each(function () {
                if ($(this).val().length === 0) {
                    valid = false;
                }
            });
            if (!valid) {
                e.preventDefault();
                e.stopPropagation();

                alert('Fill all labels first');
                return false;
            }
        });
    }

    initOptions() {
        $('.list_container')
            .on('click', '.open_modal', function (e) {
                e.preventDefault();

                $(this).closest('.item').find('.modal').modal('show');
            })
            .on('click', '.add_option', function (e) {
                e.preventDefault();

                let source = $('#option-template').html();
                let template = Handlebars.compile(source);
                let obj = $(this).parent().find('.list');
                obj.append(template({id: $(this).closest('.item').data('id')}));
                $(this).closest('.item').find('.options .open_modal span').html(obj.find('.row').length);
            })
            .on('click', '.delete_option', function (e) {
                e.preventDefault();

                if (confirm('You sure?')) {
                    let $item = $(this).closest('.item');
                    $(this).closest('.form-group').remove();
                    $item.find('.options .open_modal span').html($item.find('.modal .row').length);
                }
            });
    }
}
$(document).ready(() => {
    new CustomForms();
});
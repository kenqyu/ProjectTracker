/**
 * Created by alex on 8/8/16.
 */

class JobsSearch {
    constructor() {
        var obj = this;
        $('#search .add a').click(function (e) {
            e.preventDefault();

            var $select = $('#search .add select option:selected');
            obj.addRow($select.val(), $select.data('type'), $select.html());
        });
        $('#search .add select').change(function (e) {
            if ($(this).val() != '') {
                obj.addRow($(this).val(), $(this).find('option:selected').data('type'), $(this).find('option:selected').html());
                $(this).val('');
            }
        });
        $('#search').on('click', '.delete', function (e) {
            e.preventDefault();

            $(this).closest('.item').remove();
        });
        this.initDatePicker($('#search'));
    }

    addRow(field, type, name) {
        var source = $('#search-type-' + type).html();
        var template = Handlebars.compile(source);
        var id = $('#search .filters .item').length + 1;

        var data = {humanName: name, name: field, id: id, type_content: template({id: id})};

        var rowTemplate = Handlebars.compile($('#search-row').html());
        var out = $(rowTemplate(data));

        $('#search .filters').append(out);
        this.initDatePicker($('#search .filters .item').last());
    }

    initDatePicker(container) {
        container.find('.datepicker').datepicker();
    }
}
new JobsSearch();
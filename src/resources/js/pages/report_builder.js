class ReportBuilder {
    constructor() {
        this.container = $('.report_builder');

        this.initAddRow();
        this.initDeleteRow();
        this.initColumnType();
        this.initFieldType();
        this.initCustomFields();
        this.initSort();
        this.initFilters();
    }

    initSort() {
        $('.report_builder .rows').sortable({
            handle: ".dragger"
        })
    }

    initAddRow() {
        $('.add_row').click((e) => {
            e.preventDefault();

            let columns = prompt('How many columns');
            if (columns <= 0) {
                alert('Enter more that 0 columns');
                return;
            }

            let source = $('#row-template').html();
            let template = Handlebars.compile(source);
            let rowId = this.guid();
            let $row = $(template({row: rowId}));

            let column_template = Handlebars.compile($('#column-template').html());

            for (let i = 1; i <= columns; i++) {
                $('.columns', $row).append(column_template({row: rowId, column: i}));
            }

            $('.rows', this.container).append($row);

        });
    }

    initDeleteRow() {
        $(this.container).on('click', '.delete_row', function () {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $(this).closest('.builder_row').remove();
            });
        });
    }

    initColumnType() {
        $(this.container).on('change', '.column_type', function () {
            let $this = $(this);
            let $column = $this.closest('.column');
            let $row = $this.closest('.builder_row');
            if ($this.find('option:selected').val() > 0) {
                let type_template = Handlebars.compile($('#type-chart-template').html());
                let config_template = Handlebars.compile($('#type-chart-native-template').html());

                $column
                    .find('.type_container')
                    .html(type_template({
                        column: $column.data('id'),
                        row: $row.data('id')
                    }))
                    .find('.field_configuration_container')
                    .html(config_template({
                        column: $column.data('id'),
                        row: $row.data('id')
                    }));
            } else {
                $column.find('.type_container').html('');
            }
        })
    }

    initFieldType() {
        $(this.container).on('change', '.field_type', function () {
            let $this = $(this);
            let $column = $this.closest('.column');
            let $row = $this.closest('.builder_row');

            let config_template;
            if ($this.find('option:selected').val() == 0) {
                config_template = Handlebars.compile($('#type-chart-native-template').html());
            } else {
                config_template = Handlebars.compile($('#type-chart-custom-template').html());
            }

            $column.find('.field_configuration_container')
                .html(config_template({
                    column: $column.data('id'),
                    row: $row.data('id')
                }));
        });
    }

    initCustomFields() {
        let t = this;
        $(this.container).on('change', '.processing_unit', function () {
            let $this = $(this);
            let $column = $this.closest('.column');
            let $request_type = $column.find('.request_type');

            axios.get('/reports/builder/get-request-types?processing_unit=' + $this.find('option:selected').val())
                .then(r => {
                    $request_type.html('');
                    Object.entries(r.data).forEach(([key, value]) => {
                        $request_type.append('<option value="' + key + '">' + value + '</option>');
                    });
                    t.refreshCustomFields($column, $request_type.find('option:selected').val())
                })
                .catch(e => {
                    alert(e);
                })
        });
        $(this.container).on('change', '.request_type', function () {
            let $this = $(this);
            let $column = $this.closest('.column');

            t.refreshCustomFields($column, $this.find('option:selected').val())

        });
    }

    refreshCustomFields($column, request_type) {
        axios.get('/reports/builder/get-custom-fields?request_type=' + request_type)
            .then(r => {
                let $customFields = $column.find('.custom_field');
                $customFields.html('');
                Object.entries(r.data).forEach(([key, value]) => {
                    $customFields.append('<option value="' + key + '">' + value + '</option>');
                });
            })
            .catch(e => {
                alert(e);
            })
    }

    guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
            s4() + '-' + s4() + s4() + s4();
    }

    initFilters() {
        let $filters_container = $('#filters_modal', this.container);
        let __this = this;
        $('#add_filter', $filters_container).click(function (e) {
            e.preventDefault();

            let $option = $('#new_filter option:selected');

            let source = $('#filter-type-' + $option.data('type')).html();
            let template = Handlebars.compile(source);
            let id = __this.guid();

            let data = {humanName: $option.html(), name: $option.val(), id: id, type_content: template({id: id})};

            let rowTemplate = Handlebars.compile($('#filter-row').html());
            let out = $(rowTemplate(data));

            $('.filters-list', $filters_container).append(out);
            $('.filters-list .item', $filters_container).last().find('.datepicker').datepicker();
        });

        $('.filters-list', $filters_container).on('click', '.delete', function (e) {
            e.preventDefault();

            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(() => {
                $(this).closest('.item').remove();
            });
        })
    }
}

$(document).ready(() => {
    new ReportBuilder();
});
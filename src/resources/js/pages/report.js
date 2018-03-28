class Report {
    constructor() {
        this.initFilters();
        this.initDateRange();
        this.initResponsive();
    }

    initFilters() {
        let $form = $('.report .header form');
        $('select, input', $form).change(() => {
            $form.submit();
        })
    }

    initDateRange() {
        $('#date_range').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            startDate: $('#date_range').val().split(' - ')[0],
            endDate:  $('#date_range').val().split(' - ')[1],
            autoUpdateInput: false,
            opens: "center",
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $(this).trigger('change');
        });
    }

    initResponsive() {
        $(window).resize(function () {
            $('.chart').each(function () {
                console.log('New size', $(this).closest('.report_row'), $(this).closest('.builder_row').width() * ($(this).closest('.column').data('width') / 100));
                $(this).highcharts().setSize($(this).closest('.report_row').width() * ($(this).closest('.column').data('width') / 100), 300, false);
            });
        });
    }
}

jQuery(() => {
    new Report();
});
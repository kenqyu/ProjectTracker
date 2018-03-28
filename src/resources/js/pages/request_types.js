/**
 * Created by alex on 5/15/17.
 */

class RequestTypes {
    constructor() {
        this.initDefaultCostCenters();
    }

    initDefaultCostCenters() {
        this.checkCostCenterState();
        $('#requesttypeform-show_cost_center').change(() => {
            this.checkCostCenterState();
        });

        $('#add_cost_center').click((e) => {
            e.preventDefault();
            let $cost_center_name = $('#new_cost_center_name');
            let $percent = $('#new_cost_center_percent');

            if ($cost_center_name.val().length > 0 && $percent.val() > 0) {
                let sum = 0;
                $('.default_cost_centers tr').each(function () {
                    sum += parseInt($(this).data('percent'));
                });
                if (sum + parseInt($percent.val()) > 100) {
                    alert('Total of all cost centers more that 100%');
                    return;
                }
                let source = $('#cost-center-template').html();
                let template = Handlebars.compile(source);
                $('.default_cost_centers table tbody').append(template({
                    id: $('.default_cost_centers table tr').length,
                    cost_center_name: $cost_center_name.val(),
                    percent: $percent.val()
                }));
            } else {
                alert('Total percentage for cost centers can’t exceed 100%')
            }
        });

        $('.default_cost_centers').on('click', '.delete', function (e) {
            e.preventDefault();
            if (confirm('You sure?')) {
                $(this).closest('tr').remove();
            }
        });
    }

    checkCostCenterState() {
        if ($('#requesttypeform-show_cost_center').is(':checked')) {
            $('.default_cost_centers').hide();
        } else {
            $('.default_cost_centers').show();
        }
    }
}
new RequestTypes();
/**
 * Created by alex on 8/4/16.
 */

class Jobs {
    constructor() {
        this.initAddProject();
        this.initGridScroll();
        this.initViewFilterChange();
        this.initAdditionalColumns();
        this.initPopovers();
        this.initTextarea();
        this.initDeleteJob();
    }

    initAddProject() {
        $('.add_project').click((e) => {
            e.preventDefault();

            $('#add').modal({
                backdrop: 'static'
            });
        });
    }

    initTextarea() {
        autosize($('#createjobform-description'));
    }

    initViewFilterChange() {
        $('form.view_filter label').click(function (e) {
            $(this).closest('form').submit();
        });
    }

    initAdditionalColumns() {
        $('.additionalColumns').click(function (e) {
            e.preventDefault();

            if ($('.grid-view .column:visible').length > 4) {
                $('[data-column="5"],[data-column="4"],[data-column="6"],[data-column="8"]').hide();
                $(this).html('Show more columns');
            } else {
                $('[data-column="5"],[data-column="4"],[data-column="6"],[data-column="8"]').show();
                $(this).html('Show fewer columns');
            }

            $('.grid-view').attr('data-show', $('.grid-view .column:visible').length);
        })
    }

    initGridScroll() {
        $('.grid-view .column .items').mCustomScrollbar({theme: 'dark-3', scrollInertia: 400});
    }

    initPopovers() {
        $('[data-toggle="popover"]').popover({
            trigger: 'hover'
        });
    }

    initDeleteJob() {
        $('.delete_job').click(function (e) {
            e.preventDefault();

            swal({
                type: 'warning',
                title: 'Are you sure you want to delete this request?',
                text: "Deleted requests cannot be recovered. If yes, please enter the request number to complete the deletion.",
                input: 'text',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete request',
                confirmButtonColor: '#f27474',
                inputValidator: result => {
                    return new Promise((resolve, reject) => {
                        console.log($(this).data('legacy-id'));
                        if (result === $(this).data('legacy-id')) {
                            resolve()
                        } else {
                            reject('Please enter job number in format XXXXXX-XXX.')
                        }
                    })
                }
            }).then(r => {
                window.location = '/jobs/jobs/delete?id=' + $(this).closest('tr').data('key');
            })
        })
    }
}

new Jobs();
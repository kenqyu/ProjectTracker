/**
 * Created by alex on 8/4/17.
 */

class UserUpdate {
    constructor() {
        this.initDepartment();

        if (!$('#userupdateform-department_id option:selected').val())
            this.checkDepartment();
        else {
            if ($('#userupdateform-organization_unit_id option:selected').val() > 0) {
                $('.field-userupdateform-organization_unit_other').hide();
            }
        }
    }

    initDepartment() {
        $('#userupdateform-organization_unit_id').change(this.checkDepartment.bind(this));
        $('#userupdateform-department_id').change(() => {
            this.loadSubDepartments($('#userupdateform-department_id option:selected').val());
        });
    }

    checkDepartment() {
        let $ou = $('#userupdateform-organization_unit_id option:selected');
        if ($ou.data('freeform') == 0) {
            this.loadDepartments($ou.val(), () => {
                this.loadSubDepartments($('#userupdateform-department_id option:selected').val());
            });
            $('.field-userupdateform-department_id').show();
            $('.field-userupdateform-sub_department_id').show();
            $('.field-userupdateform-organization_unit_other').hide();
        } else {
            $('.field-userupdateform-department_id').hide();
            $('.field-userupdateform-sub_department_id').hide();
            $('.field-userupdateform-organization_unit_other').show();
        }
    }

    loadDepartments(id, callback) {
        axios.get('/jobs/departments/get?organizationUnit=' + id).then(response => {
            if (response.status === 200) {
                let out = [];
                Object.keys(response.data).forEach(key => {
                    let obj = response.data[key];
                    out.push('<option value="' + key + '"> ' + obj + '</option>');
                });
                let $obj = $('#userupdateform-department_id');
                $obj.html(out.join(''));
                if (out.length === 0) {
                    $obj.closest('.form-group').hide();
                } else {
                    $obj.closest('.form-group').show();
                }
                if (typeof callback === 'function')
                    callback();
            } else {
                $('#userupdateform-department_id').html('').closest('.form-group').hide();
                throw 'Organization Unit not found';
            }
        }).catch(() => {
            $('#userupdateform-department_id').html('').closest('.form-group').hide();
        });
    }

    loadSubDepartments(id, callback) {
        axios.get('/jobs/sub-departments/get?department=' + id).then(response => {
            if (response.status === 200) {
                let out = [];
                Object.keys(response.data).forEach(key => {
                    let obj = response.data[key];
                    out.push('<option value="' + key + '"> ' + obj + '</option>');
                });
                let $obj = $('#userupdateform-sub_department_id');
                $obj.html(out.join(''));
                if (out.length === 0) {
                    $obj.closest('.form-group').hide();
                } else {
                    $obj.closest('.form-group').show();
                }
                if (typeof callback === 'function')
                    callback();
            } else {
                $('#userupdateform-sub_department_id').html('').closest('.form-group').hide();
                throw 'Department not found';
            }
        }).catch(() => {
            $('#userupdateform-sub_department_id').html('').closest('.form-group').hide();
        });
    }
}

new UserUpdate();
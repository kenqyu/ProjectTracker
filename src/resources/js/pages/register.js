/**
 * Created by alex on 8/4/17.
 */


class Register {
    constructor() {
        this.initDepartment();

        this.checkDepartment();
    }

    initDepartment() {
        $('#registerform-organization_unit').change(this.checkDepartment.bind(this));
        $('#registerform-department_id').change(() => {
            this.loadSubDepartments($('#registerform-department_id option:selected').val());
        });
    }

    checkDepartment() {
        let $ou = $('#registerform-organization_unit option:selected');
        if ($ou.val() === '') {
            $('.field-registerform-department_id').hide();
            $('.field-registerform-sub_department_id').hide();
            $('.field-registerform-organization_unit_other').hide();
            return;
        }
        if ($ou.data('freeform') == 0) {
            this.loadDepartments($ou.val(), () => {
                this.loadSubDepartments($('#registerform-department_id option:selected').val());
            });
            $('.field-registerform-department_id').show();
            $('.field-registerform-sub_department_id').show();
            $('.field-registerform-organization_unit_other').hide();
        } else {
            $('.field-registerform-department_id').hide();
            $('.field-registerform-sub_department_id').hide();
            $('.field-registerform-organization_unit_other').show();
        }
    }

    loadDepartments(id, callback) {
        axios.get('/user/auth/get-departments?organizationUnit=' + id).then(response => {
            if (response.status === 200) {
                let out = [];
                Object.keys(response.data).forEach(key => {
                    let obj = response.data[key];
                    out.push('<option value="' + key + '"> ' + obj + '</option>');
                });
                let $obj = $('#registerform-department_id');
                $obj.html(out.join(''));
                if (out.length === 0) {
                    $obj.closest('.form-group').hide();
                } else {
                    $obj.closest('.form-group').show();
                }
                if (typeof callback === 'function')
                    callback();
            } else {
                $('#registerform-department_id').html('').closest('.form-group').hide();
                throw 'Organization Unit not found';
            }
        }).catch(() => {
            $('#registerform-department_id').html('').closest('.form-group').hide();
        });
    }

    loadSubDepartments(id, callback) {
        axios.get('/user/auth/get-sub-departments?department=' + id).then(response => {
            if (response.status === 200) {
                let out = [];
                Object.keys(response.data).forEach(key => {
                    let obj = response.data[key];
                    out.push('<option value="' + key + '"> ' + obj + '</option>');
                });
                let $obj = $('#registerform-sub_department_id');
                $obj.html(out.join(''));
                if (out.length === 0) {
                    $obj.closest('.form-group').hide();
                } else {
                    $obj.closest('.form-group').show();
                }
                if (typeof callback === 'function')
                    callback();
            } else {
                $('#registerform-sub_department_id').html('').closest('.form-group').hide();
                throw 'Department not found';
            }
        }).catch(() => {
            $('#registerform-sub_department_id').html('').closest('.form-group').hide();
        });
    }
}

new Register();
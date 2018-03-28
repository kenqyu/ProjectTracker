<?php
/**
 * @var $model \app\models\Job
 * @var $user \app\models\User
 * @var $role string
 */
?>
<!--[if mso | IE]>
<table border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
    <tr>
        <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="margin:0 auto;max-width:600px;background:#FFFFFF;">
    <table cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#FFFFFF;" align="center"
           border="0">
        <tbody>
        <tr>
            <td style="text-align:center;vertical-align:top;font-size:0px;padding:20px 0px;"><!--[if mso | IE]>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="vertical-align:top;width:600px;">
                <![endif]-->
                <div aria-labelledby="mj-column-per-100" class="mj-column-per-100"
                     style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;">
                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
                        <tbody>
                        <tr>
                            <td style="word-break:break-word;font-size:0px;padding:10px 25px;" align="left">
                                <div
                                    style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px;line-height:22px;">
                                    Dear <?= $user->first_name ?>,
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="word-break:break-word;font-size:0px;padding:10px 25px;" align="left">
                                <div
                                    style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px;line-height:22px;">
                                    A new request has been submitted and requires management approval. Please log into
                                    the SCE CX Requests and Project Tracker to review the request and approve, assign,
                                    or cancel as needed.
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="word-break:break-word;font-size:0px;padding:10px 25px;" align="left">
                                <div
                                    style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px;line-height:22px;">
                                    <strong>Job #: </strong><?= $model->legacy_id ?><br>
                                    <strong>Job Name: </strong><?= $model->name ?><br>
                                    <strong>Due Date: </strong><?= date('m/d/Y', strtotime($model->due_date)) ?><br>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--[if mso | IE]>
                </td></tr></table>
                <![endif]--></td>
        </tr>
        </tbody>
    </table>
</div>
<!--[if mso | IE]>
</td></tr></table>
<![endif]-->
<!--[if mso | IE]>
<table border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;">
    <tr>
        <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="margin:0 auto;max-width:600px;background:#FFFFFF;">
    <table cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;background:#FFFFFF;" align="center"
           border="0">
        <tbody>
        <tr>
            <td style="text-align:center;vertical-align:top;font-size:0px;padding:20px 0px;"><!--[if mso | IE]>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="vertical-align:top;width:600px;">
                <![endif]-->
                <div aria-labelledby="mj-column-per-100" class="mj-column-per-100"
                     style="vertical-align:top;display:inline-block;font-size:13px;text-align:left;width:100%;">
                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
                        <tbody>
                        <tr>
                            <td style="word-break:break-word;font-size:0px;padding:10px 25px;" align="left">
                                <table cellpadding="0" cellspacing="0" align="left" border="0">
                                    <tbody>
                                    <tr>
                                        <td style="border-radius:0px;color:#ffffff;cursor:auto;padding:10px 25px;"
                                            align="center" valign="middle" bgcolor="#4d863d"><a
                                                href="<?= \yii\helpers\Url::to([
                                                    '/jobs/jobs/update',
                                                    'id' => $model->id
                                                ]) ?>"
                                                style="display:inline-block;text-decoration:none;background:#4d863d;border-radius:0px;color:#ffffff;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:15px;font-weight:normal;margin:0px;"
                                                target="_blank">See/Edit Request
                                            </a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--[if mso | IE]>
                </td></tr></table>
                <![endif]--></td>
        </tr>
        </tbody>
    </table>
</div>
<!--[if mso | IE]>
</td></tr></table>
<![endif]-->
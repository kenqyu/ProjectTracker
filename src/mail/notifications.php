<?php
/**
 * @var $this \yii\web\View
 * @var $items \app\models\Notifications[]
 */
?>
<!--[if mso | IE]>
<table border="0" cellpadding="0" cellspacing="0" width="800" align="center" style="width:600px;">
    <tr>
        <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="margin:0 auto;max-width:800px;background:#FFFFFF;">
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
                                    Dear <?= $user->first_name ?>,<br>
                                    Here’s what you missed...
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="word-break:break-word;font-size:0px;padding:10px 25px;" align="left">
                                <div
                                    style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:16px;line-height:22px;">
                                    <?php
                                    foreach ($items as $item) {
                                        echo "<div style='margin-bottom: 5px'>" . $item->message . "</div>\n";
                                    }
                                    ?>
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <title>Tailormade</title>
    <meta content="IE=edge" http-equiv="X-UA-Compatible" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap" rel="stylesheet">

    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <!--[if mso]>
    <style>
        body,
        table tr,
        table td,
        a, span,
        table.MsoNormalTable {
            font-family: 'Roboto Condensed', sans-serif;
        }

        .brandMembers a {
            margin-right: 5px;
            display: inline-block;
        }

    </style>
    <![endif]-->

</head>


<body style="margin:0; padding: 0;">

    <table cellpadding="10" cellspacing="10" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: 'Roboto Condensed', sans-serif;
      max-width: 800px; margin: 0 auto; width: 100%;">

        <thead>
            <tr style="background-color: #000000">
                <th colspan="2">
                    <img src="https://dilijentsystems.com/assets/images/logo.png" width="200" height="auto">
                </th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td>Name</td>
                <td><?= $name; ?></td>
            </tr>

            <tr>
                <td>Email</td>
                <td><?= $email; ?></td>
            </tr>


            <?php if (isset($_POST['telephone']) && $_POST['telephone'] != "") : ?>
                <tr>
                    <td>Phone</td>
                    <td><?= $telephone; ?></td>
                </tr>

            <?php endif; ?>

            <tr>
                <td>Message</td>
                <td>
                    <p>
                        <?= $message; ?>
                    </p>
                </td>
            </tr>
        </tbody>

    </table>

</body>

</html>
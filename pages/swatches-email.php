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

    <style>
        .swatch-thumb img {
            max-width: 100%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            object-position: center;
        }

        .meta_row {
            display: flex;
            gap: 10px;
        }

        .dfx {
            display: flex;
            gap: 10px;
        }
    </style>

</head>




<body style="margin:0; padding: 0;">

    <?php

    /*    
    $data = $_POST;
    */


    $customerEmail = $data['customerEmail'];
    $swatches = $data['swachtes'];


    $currentDateTime = new DateTime();
    $formattedDateTime = $currentDateTime->format("D, jS M g:i A");


    ?>

    <table cellpadding="10" cellspacing="10" style="vertical-align: -webkit-baseline-middle; font-size: medium; font-family: 'Roboto Condensed', sans-serif;
      max-width: 800px; margin: 0 auto; width: 100%;">

        <thead>
            <tr>
                <th colspan="3" style="text-align: center">
                    <img src="https://www.tailormadelondon.com/cdn/shop/files/Logo-Black-1-1_4.svg?height=200&v=1683116234" width="200" height="auto">
                </th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td colspan="3">Request of <?= sizeof($swatches) ?> swatches</td>
            </tr>
            <tr>

                <td colspan="3">

                    <div class="dfx">
                        <div>From:</div>
                        <div><?= $customerEmail ?></div>
                    </div>

                    <div class="dfx">
                        <div>At:</div>
                        <div><?= $formattedDateTime; ?></div>
                    </div>

                </td>


            </tr>
            <tr>
                <td>At</td>
                <td colspan="3">
                    <?php

                    ?>

                </td>
            </tr>



            <?php

            $counter = 1;

            foreach ($swatches as $swatch) :
                $swatchId = $swatch['id'];
                $swatchTitle = $swatch['title'];
                $swatchImageUrl = $swatch['imageUrl'];
                $productPrice = $swatch['productPrice'];
                $productSource = $swatch['source'];

            ?>

                <tr>


                    <td><?= $counter ?></td>

                    <td>

                        <div class="swatch-thumb">

                            <?php
                            $rawImageIurl = SITE_URL . $swatchImageUrl;
                            $cleanedUrl = preg_replace('#([^:])//+#', '$1/', $rawImageIurl);
                            ?>
                            <img src="<?= $cleanedUrl ?>" alt="" width="100">

                        </div>

                    </td>

                    <td>

                        <div class="id"><strong>Id: </strong><?= $swatchId ?></div>
                        <div class="title"><strong>Title:</strong> <?= $swatchTitle ?></div>


                        <div class="metaContainer">

                            <div class="meta_row">
                                <div class="meta_key"><strong>Source</strong></div>
                                <div class="meta_value"><?= $productSource ?></div>
                            </div>

                            <?php

                            $productMeta = json_decode($swatch['productMeta'], true);



                            foreach ($productMeta as $key => $value) : ?>
                                <div class="meta_row">
                                    <div class="meta_key"><strong><?php echo htmlspecialchars($key); ?></strong></div>
                                    <div class="meta_value"><?php echo htmlspecialchars($value); ?></div>
                                </div>

                            <?php endforeach; ?>

                        </div>
                    </td>
                </tr>

            <?php

                $counter++;

            endforeach; ?>


        </tbody>

    </table>

</body>

</html>
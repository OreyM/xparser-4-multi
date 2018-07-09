<?php
require_once '../config/config.php';
require_once  '../classes/SomePage.php';
$getData = (new SomePage())->getSomePageData($_REQUEST['urlParsing']);
$allDiscountGameID = implode(" ", $getData);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XParser Multi RC1</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<section>
    <div class="container">
        <div class="row">
            <div class="games-id-wrap">

                <div class="data">
                    <?php echo $allDiscountGameID ?>
                </div>

            </div>
        </div>
    </div>
</section>



<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
</body>
</html>
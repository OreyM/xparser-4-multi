s<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XParser Multi RC1</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
require_once 'config/config.php';
# $offset - смещение относительно Гринвича
function currentDateTime ($offset) {
    date_default_timezone_set("UTC");
    $time = time();
    $time += $offset * 3600;

    return $time;
}
?>




<section>
    <div class="container">
        <div class="row">
            <div class="buttons-wrap">
                <div class="title">
                    <h3>XParser Multi RC1 /Prototype <?php echo VERSION?></h3>
                </div>
                <div class="col s12 btn-wrap">
                    <a class="btn btn-large waves-effect waves-light red darken-4" href="core/start_parsing.php">Начать парсинг</a>
                </div>
                <div class="col s12 btn-wrap">
                    <a class="btn btn-large waves-effect waves-light purple darken-4" href="core/create_table.php">Сформировать таблицу</a>
                </div>
                <div class="col s12 btn-wrap">
                    <a class="btn btn-large waves-effect waves-light light-green darken-4" href="core/show_table.php">Отобразить результаты</a>
                </div>
                <div class="col s12 btn-wrap">
                    <form action="core/parsing_discount.php" method="POST">
                        <div class="input-field">
                            <input value="" id="first_name2" type="text" class="validate" name="urlParsing">
                            <label class="active" for="first_name2">Enter url for parsing</label>
                            <div class="col s12 btn-wrap">
                                <input type="submit" name="send" value="Собрать скидки" class="btn btn-large waves-effect waves-light light-yellow darken-4">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col s12">
                    <div id="card-alert" class="card green current-time">
                        <div class="card-content white-text">
                            <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Текущая Дата / Время</span>
                            <p><?php echo date('d.m.Y', currentDateTime(2)) ?></p>
                            <p><?php echo date('H:i:s', currentDateTime(2)) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row">

            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Mexico</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-6)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-6)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Columbia</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-5)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-5)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>USA</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-4)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-4)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Canada</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-4)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-4)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Brazil</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-3)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-3)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Argentina</span>
                        <p><?php echo date('d.m.Y', currentDateTime(-3)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(-3)) ?></p>
                    </div>
                </div>
            </div>
        </div>

<!---->

        <div class="row">

            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>S.Africa</span>
                        <p><?php echo date('d.m.Y', currentDateTime(2)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(2)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Turkish</span>
                        <p><?php echo date('d.m.Y', currentDateTime(3)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(3)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>India</span>
                        <p><?php echo date('d.m.Y', currentDateTime(5.5)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(5.5)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>HongKong</span>
                        <p><?php echo date('d.m.Y', currentDateTime(8)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(8)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>Singapore</span>
                        <p><?php echo date('d.m.Y', currentDateTime(8)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(8)) ?></p>
                    </div>
                </div>
            </div>
            <div class="col s2">
                <div id="card-alert" class="card green">
                    <div class="card-content white-text">
                        <span class="card-title white-text darken-1"><i class="mdi-social-notifications"></i>N.Zeland</span>
                        <p><?php echo date('d.m.Y', currentDateTime(13)) ?></p>
                        <p><?php echo date('H:i:s', currentDateTime(13)) ?></p>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

</body>
</html>


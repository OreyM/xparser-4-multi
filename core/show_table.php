<?php
require_once '../config/config.php';
require_once '../classes/Database.php';

$Database = Database::checkConnect();
$sql = $Database->connectDatabase();

$result = $Database->selectData($sql, 'ready', '*');
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

            <table class="striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th style="width: 20%;">Game</th>
                    <th>Game ID</th>

                    <th>Price</th>
                    <th>Country</th>

                    <th>Price</th>
                    <th>Country</th>

                    <th>Price</th>
                    <th>Country</th>

                    <th>Price</th>
                    <th>Country</th>

                    <th>Price</th>
                    <th>Country</th>
                </tr>
                </thead>

                <tbody>
<?php while ($gamesData = $result->fetch_object()): ?>
                <tr>
                    <td><?php echo $gamesData->id ?></td>
                    <td><?php echo $gamesData->game_name ?></td>
                    <td><?php echo $gamesData->game_id ?></td>
                    <td><?php echo $gamesData->bp_price ?></td>
                    <td><a href="<?php echo $gamesData->bp_link ?>"><?php echo $gamesData->bp_country ?></a></td>
                    <td><?php echo $gamesData->g1_price ?></td>
                    <td><a href="<?php echo $gamesData->g1_link ?>"><?php echo $gamesData->g1_country ?></a></td>
                    <td><?php echo $gamesData->g2_price ?></td>
                    <td><a href="<?php echo $gamesData->g2_link ?>"><?php echo $gamesData->g2_country ?></a></td>
                    <td><?php echo $gamesData->g3_price ?></td>
                    <td><a href="<?php echo $gamesData->g3_link ?>"><?php echo $gamesData->g3_country ?></a></td>
                    <td><?php echo $gamesData->g4_price ?></td>
                    <td><a href="<?php echo $gamesData->g4_link ?>"><?php echo $gamesData->g4_country ?></a></td>
                </tr>
<?php endwhile; ?>
                </tbody>
            </table>

</section>


<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
</body>
</html>
<?php $sql->close(); ?>
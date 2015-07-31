<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="content/css/bootstrap.min.css" rel="stylesheet">
    <script src="content/js/bootstrap.min.js"></script>
</head>

<body>
<div class="container">
    <div class="row">
        <h3>PHP CRUD Grid</h3>
    </div>
    <p>
        <a href="create.php" class="btn btn-success">Create</a>
    </p>
    <div class="row">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            include 'database.php';
            $pdo = connect();
            $sql = 'SELECT * FROM orders ORDER BY id DESC';
            foreach ($pdo->query($sql) as $row) {
                echo '<tr>';
                echo '<td>'. $row['title'] . '</td>';
                echo '<td>'. $row['text'] . '</td>';
                echo '<td>'. $row['cost'] . '$</td>';
                echo '<td><a class="btn" href="read.php?id='.$row['id'].'">Read</a></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div> <!-- /container -->
</body>
</html>
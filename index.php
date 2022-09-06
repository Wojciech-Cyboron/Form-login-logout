<?php

declare(strict_types=1);

// ZADANIE - utwórz pulpit logowania z podpiętą bazą danych

// PDO 

$host = "localhost";
$name = "kurs_php";
$user = "root";
$password = "";


try {
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $name, $user, $password);
    echo "Poprawnie połączono z bazą danych! ";
} catch (Exception $e) {
    echo "wystąpił błąd podczas połączenia! " . $e->getMessage();
}

// Podłączenie pod bazę danych

if (isset($_POST["submit"])) {
    // $id = $_POST["id"];
    $firstname = $_POST["firstname"];
    $secondname = $_POST["secondname"];
    $email = $_POST["email"];



    $sql = ("INSERT INTO users (firstname, secondname, email) VALUES (:firstname, :secondname, :email)");
    //$stm = $pdo->prepare("UPDATE users SET firstname=:firstname, secondname=:secondname, email=:email WHERE id=:id ");



    $stm = $pdo->prepare($sql);
    // $stm->bindParam(':id', $id);
    $stm->bindParam(':firstname', $firstname);
    $stm->bindParam(':secondname', $secondname);
    $stm->bindParam(':email', $email);

    $stm->execute();
    echo "Dodano dane do tabeli! ";
}


// samo uzupełnianie danych 
function getUserById(int $userId, PDO $pdo): array
{
    $id = $userId;
    $stm = $pdo->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
    $stm->bindValue(":id", $id);
    $stm->execute();
    $user = $stm->fetch();
    return $user;
}
// id do samo uzupełniania
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user = getUserById($id, $pdo);
} else {
    $user = array(
        'id' => 0,
        'firstname' => '',
        'secondname' => '',
        'email' => '',
    );
}
// usuwanie id
if (isset($_POST['deleteElement'])) {
    $id = $_POST['delete_id'];
    $stm = $pdo->prepare("DELETE FROM users WHERE id= :id ");
    $stm->bindValue(":id", $id);
    $stm->execute();
    echo 'Usunięto użytkownika';
}



?>


<!doctype html>
<html lang="en">
<!-- html i boostrap-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-2">
        <h1>Tabela - kont</h1>

        <!-- panel do logowania-->
        <form method="POST" action="">

            <input type="hidden" name="id" value=" <?= $user['id'] ?> ">

            <div class="form-group">
                <label>Imię</label>
                <input type="text" name="firstname" class="form-control" value=" <?= $user['firstname'] ?> ">
            </div>
            <div class="form-group">
                <label>Nazwisko</label>
                <input type="text" name="secondname" class="form-control" value=" <?= $user['secondname'] ?> ">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value=" <?= $user['email'] ?> ">
            </div>
            <div class="form-group mt-4">
                <button class="btn col-12 btn-info" type="submit" name="submit">Dodaj dane</button>
            </div>

        </form>

        <div class="mt-5">
            <form method="POST" action="">
                <button class="btn col-12 btn-info mb-4" name="show_users" type="submit">Pokaż dane użytkowników</button>
            </form>

            <?php
            // wypis użytkowników
            if (isset($_POST['show_users'])) {
                $sql = "SELECT * FROM users";
                $data = $pdo->query($sql)->fetchAll();
                if (count($data) > 0) {
                    echo '<ul>';
                    foreach ($data as $row) {

                        $button_delete = '
                        <form action="" method="post">
                        <input type="hidden" name="delete_id" value="' . $row['id'] . '" >
                        <button class="btn btn-danger" type="submit" name="deleteElement">Usuń</button>
                        </form>
                        ';

                        echo '<li class="mt-2">' . $row['firstname'] . ' ' . $row['secondname'] .
                            ' - <a href="index.php?id=' . $row['id'] . '">
                        <button class="btn btn-info">Edytuj</button>
                         ' . $button_delete . '
                        </a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<li> Brak rekordów w bazie danych </li>';
                }
            }
            ?>

        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>
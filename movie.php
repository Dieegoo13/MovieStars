<?php 
    require_once("templates/header.php");

    //Verifica se user está autenticado
    require_once("models/Movie.php");
    require_once("dao/MovieDAO.php");

    $id = filter_input(INPUT_GET, "id");

    $movie;

    $movieDao = new MovieDAO($conn, $BASE_URL);

    if (empty($id)) {
        $message->setMessage("O filme não foi encontrado!", "error", "index.php");
    }else {
        $movie = $movieDao -> findById($id);

        // verifica se o filme existe
        if (!$movie) {
            $message->setMessage("O filme não foi encontrado!", "error", "index.php");
        }
    }


    //checar se o filme é do usuário
    $userOwnsMovie = false;

    if (!empty($userData)) {

        if ($userData->id === $movie->user_id) {
            $userOwnsMovie = true;
        }
    }
?>

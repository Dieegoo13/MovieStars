<?php
require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");

$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);

$type = filter_input(INPUT_POST, "type");

//Resgata dados do user
$userData = $userDao->verifyToken();

if ($type === "create") {
    //receber os dados dos inputs
    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");

    $movie = new Movie();

    // Validação minima de dados

    if (!empty($title) && !empty($description) && !empty($category)) {

        $movie->title = $title;
        $movie->category = $category;
        $movie->trailer = $trailer;
        $movie->category = $category;
        $movie->length = $length;
        $movie->user_id = $userData->id;

        //upload de image do filme
        if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

            $image = $_FILES["image"];
            $imageType = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpeg", "image/jpg"];

            //checando o tipo da imagem
            if (in_array($image["type"], $imageType)) {

                //checar se imagem é jpg
                if (in_array($image["type"], $jpgArray)) {
                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                } else {
                    $imageFile = imagecreatefrompng($image["tmp_name"]);
                }

                //gerando o nome da imagem
                $imageName = $movie->imageGenerateName();

                imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                $movie->image = $imageName;
            } else {

                $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
            }
        }

        $movieDao->create($movie);
    } else {
        $message->setMessage("Você precisa adicionar: Título, descrição e categoria!", "error", "back");
    }
} else if ($type === "delete") {

    $id = filter_input(INPUT_POST, "id");
    $movie = $movieDao->findById($id);

    if ($movie) {
        if ($movie->user_id === $userData->id) {
            $movieDao->destroy($movie->id);
        }
    } else {
        $message->setMessage("Informações inválidas!", "error", "index.php");
    }
} else if ($type === "update") {

    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");
    $id = filter_input(INPUT_POST, "id");

    $movieData = $movieDao->findById($id);

    if ($movieData) {

        // Verifica se o filme pertence ao usuário logado
        if ($movieData->user_id === $userData->id) {

            // Verifica se os campos obrigatórios estão preenchidos
            if (!empty($title) && !empty($description) && !empty($category)) {

                $movieData->title = $title;
                $movieData->description = $description;
                $movieData->trailer = $trailer;
                $movieData->category = $category;
                $movieData->length = $length;

                //upload de imagem do filme
                if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

                    $image = $_FILES["image"];
                    $imageType = ["image/jpeg", "image/jpg", "image/png"];
                    $jpgArray = ["image/jpeg", "image/jpg"];

                    //checando o tipo da imagem
                    if (in_array($image["type"], $imageType)) {

                        //checar se imagem é jpg
                        if (in_array($image["type"], $jpgArray)) {
                            $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                        } else {
                            $imageFile = imagecreatefrompng($image["tmp_name"]);
                        }

                        //gerando o nome da imagem
                        $imageName = $movieData->imageGenerateName();

                        imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                        $movieData->image = $imageName;
                    } else {

                        $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
                    }
                }

                $movieDao->update($movieData);
            } else {
                $message->setMessage("Você precisa adicionar título, descrição e categoria!", "error", "back");
            }
        } else {
            $message->setMessage("Informações inválidas!", "error", "index.php");
        }
    } else {
        $message->setMessage("O filme não foi encontrado!", "error", "index.php");
    }
} else {
    $message->setMessage("Informações inválidas!", "error", "index.php");
}

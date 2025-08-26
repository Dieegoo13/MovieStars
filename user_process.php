<?php 
        
    require_once("globals.php");
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);

    $userDao = new UserDAO($conn, $BASE_URL);

    $type = filter_input(INPUT_POST, "type");

    //atualizar usuário
    if($type === "update"){
        
        //Resgata dados do user
        $userData = $userDao->verifyToken();

        //Receber dados do post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        //criar novo objeto de usuário
        $user = new User();

        //preencher os dados do usário
        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

        //Upload da imagem

        if(isset($_FILES["image"]) && !empty($_FILES["image"] ["tmp_name"])){
            
            $image = $_FILES["image"];
            $imageType = ["image/jpeg", "image/jpg", "image/png"];
            $jpgArray = ["image/jpg", "image/jpg"];

            if(in_array($image["type"], $imageType)){
                //checar se jpg
                if (in_array($image, $jpgArray)) {
                    //image é png

                    $imageFile =  imagecreatefromjpeg($image["tmp_name"]);
                }else {
                    $imageFile =  imagecreatefrompng($image["tmp_name"]);
                }

                $imageName = $user->imageGenerateName();

                imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                $userData->image = $imageName;
                
            }else {
                $message -> setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
            }
        }



        $userDao->update($userData);
     
    //atualizar senha do usuário    
    } else if($type === "changepassword") {
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        $userData = $userDao->verifyToken();
        $id = $userData -> id;

        if($password && $confirmpassword) {
            if($password === $confirmpassword) {
                
                $user = new User();
                $finalPassword = $user->generatePassword($password);

                $user->password = $finalPassword;
                $user->id = $id;

                $userDao->changePassword($user);

            } else {
                $message->setMessage("As senhas não são iguais!", "error", "back");
            }
        } else {
            $message->setMessage("Preencha todos os campos!", "error", "back");
        }

    } else {
        $message->setMessage("Informações inválidas", "error", "index.php");
    }


?>
<?php 

require_once("models/User.php");

$userModel = new User();

$fullName = $userModel ->getFullName($review -> user);

if ($review->user->image =="") {
    $review->user->image = "user.png";
}

?>


<div class="col-md-12 review">
    <div class="row">
        <!-- Coluna imagem do usuário -->
        <div class="col-md-1">
            <div class="profile-image-container review-image" style="background-image: url('<?= $BASE_URL ?>img/users/user.png')">
            </div>
        </div>

        <!-- Coluna detalhes do autor -->
        <div class="col-md-9 author-details-container">
            <h4 class="author-name">
                <a href="#"><?= $fullName ?></a>
            </h4>
            <p><i class="fas fa-star"></i> <?= $review->rating?></p>
        </div>

        <!-- Coluna comentário -->
        <div class="col-md-12">
            <p class="comment-title">Comentário:</p>
            <p><?=$review->review?></p>
        </div>
    </div>
</div>
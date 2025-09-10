<?php

/** @var yii\web\View $this */

$this->title = 'Início';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Bem-vindo ao Minhas Despesas!</h1>

        <h3 class="display-8">
            Gerencie suas despesas de forma fácil e eficiente.
        </h3>

        <hr class="my-4">

        <?php if (Yii::$app->user->isGuest) { ?>
            <p class="lead">
                Para começar, por favor, inscreva-se ou conecte-se à sua conta.
            </p>
            <p>
                <a class="btn btn-lg btn-primary" href="/auth/register">Registrar-se</a>
                <a class="btn btn-lg btn-success" href="/auth/login">Entrar</a>
            </p>
        <?php } else { ?>
            <p><a class="btn btn-lg btn-success" href="/expense">Ver Minhas Despesas</a></p>
        <?php } ?>
    </div>
</div>
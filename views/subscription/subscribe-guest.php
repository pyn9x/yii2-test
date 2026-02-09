<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Подписаться на автора: ' . Html::encode($author->name);
?>
<div class="subscription-guest">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info">
        <p><strong>Информация об авторе:</strong></p>
        <p><strong>Имя:</strong> <?= Html::encode($author->name) ?></p>
        <?php if ($author->bio): ?>
            <p><strong>Биография:</strong> <?= Html::encode($author->bio) ?></p>
        <?php endif; ?>
    </div>

    <p>Чтобы получать уведомления о новых книгах автора <strong><?= Html::encode($author->name) ?></strong>,
    укажите ваш номер телефона:</p>

    <div class="subscription-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'phone')->textInput([
            'maxlength' => true,
            'placeholder' => '+79991234567',
            'autofocus' => true,
        ])->hint('Формат: +79991234567 или 79991234567') ?>

        <?= $form->field($model, 'author_id')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', ['author/view', 'id' => $author->id], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="alert alert-warning">
        <p><strong>Обратите внимание:</strong></p>
        <ul>
            <li>На один номер телефона можно отправить не более 3 SMS в час</li>
            <li>Максимум 10 SMS в день на один номер</li>
            <li>Вы можете зарегистрироваться, чтобы управлять своими подписками в личном кабинете</li>
        </ul>
    </div>
</div>


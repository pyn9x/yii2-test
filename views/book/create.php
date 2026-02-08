<?php
use yii\helpers\Html;

$this->title = 'Create Book';
?>
<div class="book-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model, 'authors' => $authors]) ?>
</div>


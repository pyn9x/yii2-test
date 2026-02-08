<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
?>
<div class="book-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Are you sure you want to delete this book?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'author_id',
                'value' => $model->author ? $model->author->name : null,
                'label' => 'Author',
            ],
            'title',
            'description:ntext',
            'published_at',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>


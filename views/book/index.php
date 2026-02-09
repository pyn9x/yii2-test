<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Author;

$this->title = 'Books';
?>
<div class="book-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Create Book', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'title',
                'format' => 'text',
            ],
            [
                'label' => 'Authors',
                'value' => fn($model) => implode(', ', array_map(fn($author) => $author->name, $model->authors)),
                'format' => 'text',
            ],
            'isbn',
            'published_at',
            [
                'class' => ActionColumn::class,
                'template' => Yii::$app->user->isGuest ? '{view}' : '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>


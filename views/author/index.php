<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Authors';
?>
<div class="author-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Create Author', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'bio:ntext',
            [
                'class' => ActionColumn::class,
                'template' => Yii::$app->user->isGuest ? '{view}' : '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>


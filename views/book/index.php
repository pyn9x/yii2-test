<?php
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
                'attribute' => 'author_id',
                'value' => fn($model) => $model->author ? $model->author->name : null,
                'filter' => ArrayHelper::map(Author::find()->all(), 'id', 'name'),
            ],
            'title',
            'published_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>


<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
?>
<div class="author-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Are you sure you want to delete this author?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>

        <?php
        $subscription = Yii::$app->user->isGuest ? null : app\models\Subscription::findOne(['user_id' => Yii::$app->user->id, 'author_id' => $model->id]);
        if ($subscription): ?>
            <?= Html::a('Отписаться', ['/subscription/unsubscribe', 'id' => $subscription->id], [
                'class' => 'btn btn-warning',
                'data' => ['method' => 'post', 'confirm' => 'Вы уверены, что хотите отписаться?'],
            ]) ?>
        <?php else: ?>
            <?php $buttonText = Yii::$app->user->isGuest ? 'Подписаться на уведомления (SMS)' : 'Подписаться'; ?>
            <?= Html::a($buttonText, ['/subscription/subscribe', 'authorId' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'bio:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <h3>Books</h3>
    <ul>
        <?php foreach ($model->books as $book): ?>
            <li><?= Html::a(Html::encode($book->title), ['/book/view', 'id' => $book->id]) ?></li>
        <?php endforeach; ?>
    </ul>
</div>


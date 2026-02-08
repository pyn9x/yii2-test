<?php
use yii\helpers\Html;

$this->title = 'My Subscriptions';
?>
<div class="subscription-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <ul>
        <?php foreach ($subscriptions as $subscription): ?>
            <li>
                <?= Html::encode($subscription->author->name) ?>
                <?= Html::a('Unsubscribe', ['unsubscribe', 'id' => $subscription->id], [
                    'class' => 'btn btn-sm btn-warning',
                    'data' => ['method' => 'post'],
                ]) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


<?php
use yii\helpers\Html;

$this->title = 'Top 10 Authors by Subscribers';
?>
<div class="report-top-authors">
    <h1><?= Html::encode($this->title) ?></h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Subscribers</th>
                <th>Books</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($authors as $index => $author): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= Html::encode($author->name) ?></td>
                <td><?= (int)$author->subscriptions_count ?></td>
                <td><?= (int)$author->books_count ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


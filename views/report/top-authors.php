<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Top 10 Authors by Books in ' . $year;
?>
<div class="report-top-authors">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <label>Select Year:</label>
        <form method="get" action="<?= Url::to(['report/top-authors']) ?>" style="display: inline-block;">
            <select name="year" class="form-control" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Books</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($authors as $index => $author): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= Html::encode($author->name) ?></td>
                <td><?= (int)$author->books_count ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($authors)): ?>
            <tr>
                <td colspan="3" class="text-center">No data for year <?= $year ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


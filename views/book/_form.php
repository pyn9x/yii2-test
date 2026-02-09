<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="book-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true, 'placeholder' => 'ISBN-10 or ISBN-13']) ?>

    <?= $form->field($model, 'authorIds')->checkboxList($authors) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'published_at')->input('date') ?>

    <?= $form->field($model, 'coverImageFile')->fileInput() ?>

    <?php if ($model->cover_image): ?>
        <div class="form-group">
            <label>Current Cover:</label><br>
            <?= Html::img('@web/uploads/covers/' . $model->cover_image, ['style' => 'max-width: 200px']) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>


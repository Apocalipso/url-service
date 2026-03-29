<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="text-center mb-4">Сервис коротких ссылок + QR</h1>

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="urlInput" class="form-label">Введите ссылку</label>
                    <input type="text" class="form-control" id="urlInput" placeholder="https://example.com">
                    <div class="invalid-feedback" id="urlError"></div>
                </div>
                <button id="submitBtn" class="btn btn-primary w-100">ОК</button>
            </div>
        </div>

        <div id="resultArea" class="mt-4 text-center" style="display: none;">
            <div class="alert alert-success" id="successMessage"></div>
            <div id="qrContainer" class="mb-3"></div>
            <div class="mb-3">
                <strong>Короткая ссылка:</strong> <a id="shortLink" href="#" target="_blank"></a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#submitBtn').click(function() {
        var url = $('#urlInput').val().trim();
        if (!url) {
            showError('Пожалуйста, введите ссылку');
            return;
        }

        $('#submitBtn').prop('disabled', true).text('Проверка...');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['site/create']) ?>',
            type: 'POST',
            data: {url: url},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#resultArea').show();
                    $('#successMessage').text('Ссылка успешно сокращена!');
                    $('#shortLink').attr('href', response.short_url).text(response.short_url);
                    $('#qrContainer').html('<img src="' + response.qr_code + '" alt="QR Code">');
                    $('#urlInput').val('');
                    $('#urlError').hide();
                } else {
                    showError(response.error);
                }
            },
            error: function() {
                showError('Ошибка сервера. Попробуйте позже.');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).text('ОК');
            }
        });
    });

    function showError(msg) {
        $('#urlError').text(msg).show();
        $('#resultArea').hide();
    }
});
</script>
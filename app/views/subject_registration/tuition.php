<?php $this->layout("layouts/default", ["title" => APPNAME . " - Lịch học sinh viên"]) ?>
<?php $messages = session_get_once('messages'); ?>

<?php $this->start("page") ?>
<div class="container mt-5">
    <h2 class="mb-4">Thông tin học phí học kỳ mới nhất</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Tổng số tín chỉ đã đăng ký:</strong> <?= $totalCredits ?></p>
            <p><strong>Học phí mỗi tín chỉ:</strong> <?= number_format($feePerCredit, 0, ',', '.') ?> VNĐ</p>
            <hr>
            <p><strong>Tổng học phí phải đóng:</strong> 
                <span class="text-danger fs-5"><?= number_format($totalFee, 0, ',', '.') ?> VNĐ</span>
            </p>
        </div>
    </div>
</div>
<?php $this->stop() ?>

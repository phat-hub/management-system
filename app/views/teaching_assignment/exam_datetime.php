<?php $this->layout("layouts/default", ["title" => "Nhập lịch thi"]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-8 offset-md-2 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Nhập thời gian thi</h2>
          <form class="row g-3" method="post" action="/teaching_assignment/exam_datetime/<?= $this->e($id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Thời gian thi -->
            <div class="form-group col-md-6 offset-md-3">
              <label for="exam_datetime" class="fw-bold">Thời gian thi:</label>
              <input 
                type="datetime-local" 
                id="exam_datetime" 
                name="exam_datetime" 
                class="form-control <?= isset($errors['exam_datetime']) ? 'is-invalid' : '' ?>"
                value="<?= isset($old['exam_datetime']) 
                            ? $this->e($old['exam_datetime']) 
                            : (!empty($exam_datetime) ? $this->e(date('Y-m-d\TH:i', strtotime($exam_datetime))) : '') ?>"
              >
              <?php if (isset($errors['exam_datetime'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['exam_datetime']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary px-4">Lưu lịch thi</button>
              <a href="/teaching_assignment" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

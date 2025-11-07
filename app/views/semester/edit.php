<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa học kỳ</h2>
          <form class="row g-3" method="post" action="/semester/edit/<?= $this->e($semester->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group col-12">
              <label for="semester" class="fw-bold">Học kỳ:</label>
              <input 
                type="text" 
                id="semester" 
                name="semester" 
                class="form-control <?= isset($errors['semester']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập học kỳ"
                value="<?= isset($old['semester']) ? $this->e($old['semester']) : $this->e($semester->semester) ?>" 
              >
              <?php if (isset($errors['semester'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['semester']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="academicYear" class="fw-bold">Năm học:</label>
              <input 
                type="text" 
                id="academicYear" 
                name="academicYear" 
                class="form-control <?= isset($errors['academicYear']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập năm học"
                value="<?= isset($old['academicYear']) ? $this->e($old['academicYear']) : $this->e($semester->academicYear) ?>" 
              >
              <?php if (isset($errors['academicYear'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['academicYear']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <a href="/semester" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

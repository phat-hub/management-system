<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa học phần</h2>
          <form class="row g-3" method="post" action="/subject/edit/<?= $this->e($subject->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group col-12">
              <label for="subjectCode" class="fw-bold">Mã học phần:</label>
              <input 
                type="text" 
                id="subjectCode" 
                name="subjectCode" 
                class="form-control <?= isset($errors['subjectCode']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mã học phần"
                value="<?= isset($old['subjectCode']) ? $this->e($old['subjectCode']) : $this->e($subject->subjectCode) ?>" 
              >
              <?php if (isset($errors['subjectCode'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['subjectCode']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="subjectName" class="fw-bold">Tên học phần:</label>
              <input 
                type="text" 
                id="subjectName" 
                name="subjectName" 
                class="form-control <?= isset($errors['subjectName']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên học phần"
                value="<?= isset($old['subjectName']) ? $this->e($old['subjectName']) : $this->e($subject->subjectName) ?>" 
              >
              <?php if (isset($errors['subjectName'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['subjectName']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="credits" class="fw-bold">Số tín chỉ:</label>
              <input 
                type="number" 
                id="credits" 
                name="credits" 
                class="form-control <?= isset($errors['credits']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập số tín chỉ"
                value="<?= isset($old['credits']) ? $this->e($old['credits']) : $this->e($subject->credits ?? '') ?>"
              >
              <?php if (isset($errors['credits'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['credits']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <a href="/subject" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

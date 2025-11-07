<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa khoa</h2>
          <form class="row g-3" method="post" action="/department/edit/<?= $this->e($department->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group col-12">
              <label for="departmentCode" class="fw-bold">Mã khoa:</label>
              <input 
                type="text" 
                id="departmentCode" 
                name="departmentCode" 
                class="form-control <?= isset($errors['departmentCode']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mã khoa"
                value="<?= isset($old['departmentCode']) ? $this->e($old['departmentCode']) : $this->e($department->departmentCode) ?>" 
              >
              <?php if (isset($errors['departmentCode'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['departmentCode']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="departmentName" class="fw-bold">Tên khoa:</label>
              <input 
                type="text" 
                id="departmentName" 
                name="departmentName" 
                class="form-control <?= isset($errors['departmentName']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên khoa"
                value="<?= isset($old['departmentName']) ? $this->e($old['departmentName']) : $this->e($department->departmentName) ?>" 
              >
              <?php if (isset($errors['departmentName'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['departmentName']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <a href="/department" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Thêm ngành mới</h2>
          <form class="row g-3" method="post" action="/major/create">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group col-12">
              <label for="majorCode" class="fw-bold">Mã ngành:</label>
              <input 
                type="text" 
                id="majorCode" 
                name="majorCode" 
                class="form-control <?= isset($errors['majorCode']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập mã ngành"
                value="<?= isset($old['majorCode']) ? $this->e($old['majorCode']) : '' ?>" 
              >
              <?php if (isset($errors['majorCode'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['majorCode']) ?></strong></span>
              <?php endif ?>
            </div>
            
            <div class="form-group col-12">
              <label for="majorName" class="fw-bold">Tên ngành:</label>
              <input 
                type="text" 
                id="majorName" 
                name="majorName" 
                class="form-control <?= isset($errors['majorName']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên ngành"
                value="<?= isset($old['majorName']) ? $this->e($old['majorName']) : '' ?>" 
              >
              <?php if (isset($errors['majorName'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['majorName']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="form-group col-12">
              <label for="department_id" class="fw-bold">Khoa:</label>
              <select 
                id="department_id" 
                name="department_id" 
                class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>"
              >
                <option value="">-- Chọn khoa --</option>
                <?php foreach ($departments as $department) : ?>
                  <option 
                    value="<?= $department->id ?>"
                    <?= (isset($old['department_id']) && $old['department_id'] == $department->id) ? 'selected' : '' ?>
                  >
                    <?= ucfirst($this->e($department->departmentName)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['department_id'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['department_id']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Lưu</button>
              <a href="/major" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

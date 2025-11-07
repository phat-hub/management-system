<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa phòng học</h2>
          <form class="row g-3" method="post" action="/classroom/edit/<?= $this->e($classroom->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Tên phòng học -->
            <div class="form-group col-12">
              <label for="classroomName" class="fw-bold">Tên phòng học:</label>
              <input 
                type="text" 
                id="classroomName" 
                name="classroomName" 
                class="form-control <?= isset($errors['classroomName']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên phòng học"
                value="<?= isset($old['classroomName']) ? $this->e($old['classroomName']) : $this->e($classroom->classroomName) ?>" 
              >
              <?php if (isset($errors['classroomName'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['classroomName']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Sức chứa -->
            <div class="form-group col-12">
              <label for="capacity" class="fw-bold">Sức chứa:</label>
              <input 
                type="number" 
                id="capacity" 
                name="capacity" 
                class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập sức chứa"
                value="<?= isset($old['capacity']) ? $this->e($old['capacity']) : $this->e($classroom->capacity) ?>" 
              >
              <?php if (isset($errors['capacity'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['capacity']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <a href="/classroom" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

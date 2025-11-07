<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa khóa học</h2>
          <form class="row g-3" method="post" action="/course/edit/<?= $this->e($course->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group col-12">
              <label for="courseName" class="fw-bold">Tên khóa học:</label>
              <input 
                type="text" 
                id="courseName" 
                name="courseName" 
                class="form-control <?= isset($errors['courseName']) ? 'is-invalid' : '' ?>" 
                placeholder="Nhập tên khóa học"
                value="<?= isset($old['courseName']) ? $this->e($old['courseName']) : $this->e($course->courseName) ?>" 
              >
              <?php if (isset($errors['courseName'])) : ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['courseName']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-3">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <a href="/course" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

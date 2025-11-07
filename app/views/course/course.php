<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>
<?php $messages = session_get_once('messages'); ?>

<?php $this->start("page") ?>
<div class="container py-5">
  <div class="bg-white p-5 rounded-3 shadow-lg">

    <!-- Thông báo -->
    <?php if (isset($messages['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> <?= $this->e($messages['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php elseif (isset($messages['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Lỗi!</strong> <?= $this->e($messages['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Danh sách khóa học</h2>
      <a href="/course/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm khóa học
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Tên khóa học</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($courses as $course) : ?>
            <tr>
              <td><?= ucfirst($course->courseName) ?></td>
              <td>
                <a href="/course/edit/<?= $course->id ?>" class="btn btn-sm btn-warning me-1">
                  <i class="bi bi-pencil-square"></i> Sửa
                </a>
                <form method="POST" action="/course/delete/<?= $course->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i> Xóa
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
<?php $this->stop() ?>

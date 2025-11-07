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
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Danh sách tài khoản giảng viên</h2>
      <a href="/lecturer/register" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm tài khoản
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Mã số giảng viên</th>
            <th>Họ tên</th>
            <th>Ngày sinh</th>
            <th>Số điện thoại</th>
            <th>Quê quán</th>
            <th>Khoa</th>
            <th>Giới tính</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lecturers as $lecturer) : ?>
            <tr>
              <td><?= ucfirst($lecturer->peopleId) ?></td>
              <td><?= ucwords($lecturer->name) ?></td>
              <td>
                <?= ($lecturer->dob !== 'Chưa cập nhật')
                      ? date('d/m/Y', strtotime($lecturer->dob))
                      : 'Chưa cập nhật' ?>
              </td>
              <td><?= ucfirst($lecturer->phone) ?></td>
              <td><?= ucfirst($lecturer->hometown) ?></td>
              <td><?= ucfirst($lecturer->department_name) ?></td>
              <td><?= ucfirst($lecturer->gender) ?></td>
              <td>
                <a href="/lecturer/edit/<?= $lecturer->id ?>" class="btn btn-sm btn-warning mb-1" style="min-width: 65px;">
                  <i class="bi bi-pencil-square"></i> Sửa
                </a>

                <?php if ($lecturer->is_locked == 0): ?>
                  <form method="POST" action="/lecturer/lock/<?= $lecturer->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn khóa tài khoản này?');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" style="min-width: 65px;">
                      <i class="bi bi-lock"></i> Khóa
                    </button>
                  </form>
                <?php else: ?>
                  <form method="POST" action="/lecturer/unlock/<?= $lecturer->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn mở khóa tài khoản này?');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-sm btn-success" style="min-width: 65px;">
                      <i class="bi bi-unlock"></i> Mở
                    </button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $this->stop() ?>

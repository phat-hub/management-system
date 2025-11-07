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
      <h2 class="fw-bold mb-0">Danh sách tài khoản sinh viên</h2>
      <a href="/student/register" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Thêm tài khoản
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Mã số sinh viên</th>
            <th>Họ tên</th>
            <th>Ngày sinh</th>
            <th>Số điện thoại</th>
            <th>Quê quán</th>
            <th>Ngành</th>
            <th>Khóa</th>
            <th>Khoa</th>
            <th>Giới tính</th>
            <th>Lớp</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $student) : ?>
            <tr>
              <td><?= ucfirst($student->peopleId) ?></td>
              <td><?= ucwords($student->name) ?></td>
              <td>
                <?= ($student->dob !== 'Chưa cập nhật')
                      ? date('d/m/Y', strtotime($student->dob))
                      : 'Chưa cập nhật' ?>
              </td>
              <td><?= ucfirst($student->phone) ?></td>
              <td><?= ucfirst($student->hometown) ?></td>
              <td><?= ucfirst($student->major_name) ?></td>
              <td><?= $student->course_name ?></td>
              <td><?= ucfirst($student->department_name) ?></td>
              <td><?= ucfirst($student->gender) ?></td>
              <td><?= strtoupper($student->class) ?></td>
              <td>
                <a href="/student/edit/<?= $student->id ?>" class="btn btn-sm btn-warning mb-1" style="min-width: 65px;">
                  <i class="bi bi-pencil-square"></i> Sửa
                </a>

                <?php if ($student->is_locked == 0): ?>
                  <form method="POST" action="/student/lock/<?= $student->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn khóa tài khoản này?');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" style="min-width: 65px;">
                      <i class="bi bi-lock"></i> Khóa
                    </button>
                  </form>
                <?php else: ?>
                  <form method="POST" action="/student/unlock/<?= $student->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn mở khóa tài khoản này?');">
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

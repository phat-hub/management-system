<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>
<?php $messages = session_get_once('messages'); ?>
<?php 
  $currentUser = AUTHGUARD()->user();
  $currentUserRole = $currentUser->role;
?>

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
      <h2 class="fw-bold mb-0">Danh sách phân công giảng dạy</h2>
      <?php if ($currentUserRole === 'admin'): ?>
        <a href="/teaching_assignment/create" class="btn btn-primary">
          <i class="bi bi-plus-circle me-2"></i>Thêm phân công
        </a>
      <?php endif; ?>
    </div>
    <?php if ($currentUserRole === 'admin' || $currentUserRole === 'lecturer'): ?>
      <?php if(!empty($semesters)): ?>
      <div class="position-relative my-4">
        <!-- Form liệt kê căn giữa -->
        <form method="POST" action="/teaching_assignment" class="row g-2 justify-content-center align-items-center">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

          <div class="col-auto">
            <label for="semester_id" class="col-form-label fw-bold">Học kỳ: </label>
          </div>

          <div class="col-auto">
            <select name="semester_id" id="semester_id" class="form-select">
              <?php foreach ($semesters as $semester): ?>
                <option value="<?= $semester->id ?>" <?= ($semester->id == $selectedSemesterId) ? 'selected' : '' ?>>
                  <?= $this->e($semester->semester . ' - ' . $semester->academicYear) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-auto">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-list-ul"></i> Liệt kê
            </button>
          </div>
        </form>

        <!-- Nút mở đăng ký cố định bên phải -->
        <?php if ($currentUserRole === 'admin'): ?>
          <?php if ($isOpen): ?>
            <!-- Nút Đóng đăng ký -->
            <form method="POST" action="/registration_status/close" class="position-absolute top-0 end-0" onsubmit="return confirm('Bạn có chắc chắn muốn đóng đăng ký không?');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <button type="submit" class="btn btn-danger">
                <i class="bi bi-lock"></i> Đóng đăng ký
              </button>
            </form>
          <?php else: ?>
            <!-- Nút Mở đăng ký -->
            <form method="POST" action="/registration_status/open" class="position-absolute top-0 end-0" onsubmit="return confirm('Bạn có chắc chắn muốn mở đăng ký không?');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-unlock"></i> Mở đăng ký
              </button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Thứ</th>
            <th>Tiết học</th>
            <th>Tên học phần</th>
            <th>Số tín chỉ</th>
            <th>Phòng học</th>
            <?php if ($currentUserRole === 'admin' || $currentUserRole === 'student'): ?>
              <th>Giảng viên</th>
            <?php endif; ?>
            <th>Sỉ số</th>
            <th>Sỉ số còn lại</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($teachingAssignments as $assignment): ?>
            <tr>
              <?php
                $dayMap = [
                    'Monday'    => '2',
                    'Tuesday'   => '3',
                    'Wednesday' => '4',
                    'Thursday'  => '5',
                    'Friday'    => '6',
                    'Saturday'  => '7'
                ];
              ?>
              <td><?= $dayMap[$assignment->day_of_week] ?? $this->e($assignment->day_of_week) ?></td>
              <td>
                <?php
                  $totalPeriods = 9;
                  $periodsStr = str_repeat('-', $assignment->start_period - 1);
                  for ($i = $assignment->start_period; $i <= $assignment->end_period; $i++) {
                    $periodsStr .= $i;
                  }
                  $periodsStr .= str_repeat('-', $totalPeriods - $assignment->end_period);
                  echo $this->e($periodsStr);
                ?>
              </td>
              <td><?= $this->e($assignment->subject_name) ?></td>
              <td><?= $this->e($assignment->credits) ?></td>
              <td><?= $this->e($assignment->classroom_name) ?></td>
              <?php if ($currentUserRole === 'admin' || $currentUserRole === 'student'): ?>
                <td><?= $this->e($assignment->lecturer_name) ?></td>
              <?php endif; ?>
              <td><?= $this->e($assignment->classroom_capacity) ?></td>
              <td><?= $this->e($assignment->slots_remaining) ?></td>
              <td>
                <?php if ($currentUserRole === 'admin'): ?>
                  <a href="/teaching_assignment/edit/<?= $assignment->id ?>" class="btn btn-sm btn-warning me-1">
                    <i class="bi bi-pencil-square"></i> Sửa
                  </a>
                  <form method="POST" action="/teaching_assignment/delete/<?= $assignment->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i> Xóa
                    </button>
                  </form>
                <?php elseif ($currentUserRole === 'student'): ?>
                  <?php if (!empty($assignment->is_registered) && $assignment->is_registered): ?>
                    <!-- Nút Hủy đăng ký -->
                    <form method="POST" action="/teaching_assignment/subject_registration/delete/<?= $assignment->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đăng ký?');">
                      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-x-circle"></i> Hủy đăng ký
                      </button>
                    </form>
                  <?php else: ?>
                    <!-- Nút Đăng ký -->
                    <form method="POST" action="/teaching_assignment/subject_registration/<?= $assignment->id ?>" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn đăng ký?');">
                      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                      <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-box-arrow-in-right"></i> Đăng ký
                      </button>
                    </form>
                  <?php endif; ?>
                <?php elseif ($currentUserRole === 'lecturer'): ?>
                  <a href="/teaching_assignment/<?= $assignment->id ?>/students" class="btn btn-sm btn-info me-1">
                    <i class="bi bi-people"></i> Xem SV
                  </a>
                  <a href="/teaching_assignment/exam_datetime/<?= $assignment->id ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-calendar2-plus"></i> Nhập lịch thi
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($teachingAssignments)): ?>
            <tr>
              <td colspan="<?= ($currentUserRole === 'admin' || $currentUserRole === 'student') ? 9 : 8 ?>">Chưa có phân công giảng dạy nào.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $this->stop() ?>

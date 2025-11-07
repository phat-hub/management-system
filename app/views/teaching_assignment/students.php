<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>
<?php $messages = session_get_once('messages'); ?>

<?php $this->start("page") ?>
<div class="container py-5">
  <div class="bg-white p-5 rounded-3 shadow-lg">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">Danh sách sinh viên đã đăng ký</h2>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Mã số sinh viên</th>
            <th>Họ và tên</th>
            <th>Điểm</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $student): ?>
            <tr>
              <td><?= $this->e($student['peopleId']) ?></td>
              <td><?= $this->e($student['name']) ?></td>
              <td><?= is_numeric($student['score']) ? $this->e(number_format($student['score'], 1)) : '-' ?></td>
              <td>
                <a href="/teaching_assignment/score/<?= $student['id'] ?>" class="btn btn-sm btn-primary">
                  <i class="bi bi-pencil-square"></i> Nhập điểm
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($students)): ?>
            <tr>
              <td colspan="4">Chưa có sinh viên đăng ký học phần này.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <a href="/teaching_assignment" class="btn btn-secondary mt-3">
      <i class="bi bi-arrow-left"></i> Quay lại danh sách phân công
    </a>
  </div>
</div>
<?php if (isset($messages['success'])): ?>
  <script>
    alert("<?= $this->e($messages['success']) ?>");
  </script>
<?php endif; ?>
<?php $this->stop() ?>

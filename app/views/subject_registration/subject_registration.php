<?php $this->layout("layouts/default", ["title" => APPNAME . " - Lịch học sinh viên"]) ?>
<?php $messages = session_get_once('messages'); ?>

<?php $this->start("page") ?>
<div class="container py-5">
  <div class="bg-white p-5 rounded-3 shadow-lg">

    <h2 class="fw-bold mb-4">Lịch học</h2>

    <div class="d-flex justify-content-center my-4">
        <form method="POST" action="/subject_registration/" class="row g-2 align-items-center">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="col-auto">
            <label for="semester_id" class="col-form-label fw-bold">Học kỳ</label>
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
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Thứ</th>
            <th>Tiết học</th>
            <th>Tên học phần</th>
            <th>Số tín chỉ</th>
            <th>Phòng học</th>
            <th>Giảng viên</th>
            <th>Ngày thi</th>
            <th>Giờ thi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($studentSchedule as $schedule): ?>
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
              <td><?= $dayMap[$schedule['day_of_week']] ?? $this->e($schedule['day_of_week']) ?></td>
              <td>
              <?php
                  $totalPeriods = 9;
                  $periodsStr = str_repeat('-', $schedule['start_period'] - 1);
                  for ($i = $schedule['start_period']; $i <= $schedule['end_period']; $i++) {
                    $periodsStr .= $i;
                  }
                  $periodsStr .= str_repeat('-', $totalPeriods - $schedule['end_period']);
                  echo $this->e($periodsStr);
                ?>
              </td>
              <td><?= $this->e($schedule['subject_name']) ?></td>
              <td><?= $this->e($schedule['subject_credits']) ?></td>
              <td><?= $this->e($schedule['classroom_name']) ?></td>
              <td><?= $this->e($schedule['lecturer_name']) ?></td>
              <td><?= !empty($schedule['exam_datetime']) ? date('d/m/Y', strtotime($schedule['exam_datetime'])) : '-' ?></td>
              <td><?= !empty($schedule['exam_datetime']) ? date('H:i', strtotime($schedule['exam_datetime'])) : '-' ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($studentSchedule)): ?>
            <tr>
              <td colspan="7">Chưa có lịch học nào.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
<?php $this->stop() ?>

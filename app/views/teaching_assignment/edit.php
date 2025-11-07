<?php $this->layout("layouts/default", ["title" => "Chỉnh sửa phân công giảng dạy"]) ?>
<?php $this->start("page") ?>

<div class="container py-5">
  <div class="bg-white p-5 rounded-3 shadow-sm">
    <h2 class="fw-bold mb-4 text-center">Chỉnh sửa phân công giảng dạy</h2>

    <form action="/teaching_assignment/edit/<?= $this->e($assignment->id) ?>" method="POST">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="subject_id" class="form-label">Học phần</label>
          <select class="form-select <?= isset($errors['subject_id']) ? 'is-invalid' : '' ?>" name="subject_id">
            <option value="" disabled>-- Chọn học phần --</option>
            <?php foreach ($subjects as $subject): ?>
              <option value="<?= $subject->id ?>"
                <?= (isset($old['subject_id']) ? $old['subject_id'] : $assignment->subject_id) == $subject->id ? 'selected' : '' ?>>
                <?= $this->e($subject->subjectName) ?> (<?= $subject->credits ?> tín chỉ)
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['subject_id'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['subject_id']) ?></div>
          <?php endif; ?>
        </div>

        <div class="col-md-6">
          <label for="day_of_week" class="form-label">Thứ</label>
          <select class="form-select <?= isset($errors['day_of_week']) ? 'is-invalid' : '' ?>" name="day_of_week">
            <option value="" disabled>-- Chọn thứ --</option>
            <?php 
              $days = ['Monday' => 'Thứ 2', 'Tuesday' => 'Thứ 3', 'Wednesday' => 'Thứ 4', 'Thursday' => 'Thứ 5', 'Friday' => 'Thứ 6', 'Saturday' => 'Thứ 7'];
              foreach ($days as $value => $label): ?>
                <option value="<?= $value ?>"
                  <?= (isset($old['day_of_week']) ? $old['day_of_week'] : $assignment->day_of_week) == $value ? 'selected' : '' ?>>
                  <?= $label ?>
                </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['day_of_week'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['day_of_week']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="start_period" class="form-label">Tiết bắt đầu</label>
          <input type="number" name="start_period" class="form-control <?= isset($errors['start_period']) ? 'is-invalid' : '' ?>"
                 min="1" max="9" value="<?= $old['start_period'] ?? $assignment->start_period ?>">
          <?php if (isset($errors['start_period'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['start_period']) ?></div>
          <?php endif; ?>
        </div>

        <div class="col-md-6">
          <label for="end_period" class="form-label">Tiết kết thúc</label>
          <input type="number" name="end_period" class="form-control <?= isset($errors['end_period']) ? 'is-invalid' : '' ?>"
                 min="1" max="9" value="<?= $old['end_period'] ?? $assignment->end_period ?>">
          <?php if (isset($errors['end_period'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['end_period']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="classroom_id" class="form-label">Phòng học</label>
          <select class="form-select <?= isset($errors['classroom_id']) ? 'is-invalid' : '' ?>" name="classroom_id">
            <option value="" disabled>-- Chọn phòng học --</option>
            <?php foreach ($classrooms as $room): ?>
              <option value="<?= $room->id ?>"
                <?= (isset($old['classroom_id']) ? $old['classroom_id'] : $assignment->classroom_id) == $room->id ? 'selected' : '' ?>>
                <?= $room->classroomName ?> (Sức chứa: <?= $room->capacity ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['classroom_id'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['classroom_id']) ?></div>
          <?php endif; ?>
        </div>

        <div class="col-md-6">
          <label for="user_id" class="form-label">Giảng viên giảng dạy</label>
          <select class="form-select <?= isset($errors['user_id']) ? 'is-invalid' : '' ?>" name="user_id">
            <option value="" disabled>-- Chọn giảng viên --</option>
            <?php foreach ($users as $teacher): ?>
              <option value="<?= $teacher->id ?>"
                <?= (isset($old['user_id']) ? $old['user_id'] : $assignment->user_id) == $teacher->id ? 'selected' : '' ?>>
                <?= $this->e($teacher->name) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['user_id'])): ?>
            <div class="invalid-feedback"><?= $this->e($errors['user_id']) ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Học kỳ</label>
        <input type="text" class="form-control" value="<?= $assignment->semester_name ?> - <?= $assignment->academic_year ?>" readonly>
        <input type="hidden" name="semester_id" value="<?= $assignment->semester_id ?>">
      </div>

      <div class="d-flex justify-content-center mt-4">
        <button type="submit" class="btn btn-primary px-4 me-2">
          <i class="bi bi-save me-1"></i> Cập nhật
        </button>
        <a href="/teaching_assignment" class="btn btn-secondary">Quay lại</a>
      </div>
    </form>
  </div>
</div>

<?php $this->stop() ?>

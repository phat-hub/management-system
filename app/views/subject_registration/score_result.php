<?php $this->layout("layouts/default", ["title" => APPNAME . " - Kết quả học tập"]) ?>
<?php $messages = session_get_once('messages'); ?>

<?php $this->start("page") ?>
<div class="container py-5">
  <div class="bg-white p-5 rounded-3 shadow-lg">

    <h2 class="fw-bold mb-4">Kết quả học tập</h2>

    <div class="d-flex justify-content-center my-4">
        <form method="POST" action="/subject_registration/score_result" class="row g-2 align-items-center">
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
                <i class="bi bi-search"></i> Xem kết quả
            </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th>Mã học phần</th>
            <th>Tên học phần</th>
            <th>Số tín chỉ</th>
            <th>Điểm</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($scoreList as $item): ?>
            <tr>
              <td><?= $this->e($item['subject_code']) ?></td>
              <td><?= $this->e($item['subject_name']) ?></td>
              <td><?= $this->e($item['subject_credits']) ?></td>
              <td><?= is_numeric($item['score']) ? $this->e(number_format($item['score'], 1)) : '-' ?></td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($scoreList)): ?>
            <tr>
              <td colspan="4">Chưa có kết quả học tập.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!is_null($accumulatedScore)): ?>
    <div class="mt-4 p-4 bg-light border rounded text-center">
        <h5 class="fw-bold mb-3">Tổng kết học kỳ</h5>
        <p class="mb-1"><strong>Tổng số tín chỉ:</strong> <?= $accumulatedScore['total_credits'] ?></p>
        <p class="mb-0">
        <strong>Điểm trung bình tích lũy:</strong>
        <?= is_null($accumulatedScore['average_score']) ? '-' : number_format($accumulatedScore['average_score'], 2) ?>
        </p>
    </div>
    <?php endif; ?>


  </div>
</div>
<?php $this->stop() ?>

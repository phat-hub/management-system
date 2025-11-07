<?php $this->layout("layouts/default", ["title" => "Nhập điểm"]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-8 offset-md-2 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Nhập điểm</h2>
          <form class="row g-3" method="post" action="/teaching_assignment/score/<?= $this->e($id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Nhập điểm -->
            <div class="form-group col-md-6 offset-md-3">
              <label for="score" class="fw-bold">Điểm:</label>
              <input 
                type="number"
                id="score"
                name="score"
                class="form-control <?= isset($errors['score']) ? 'is-invalid' : '' ?>"
                min="0" max="10" step="0.1"
                value="<?= isset($old['score']) 
                            ? $this->e($old['score']) 
                            : (isset($score) ? $this->e($score) : '') ?>"
              >
              <?php if (isset($errors['score'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['score']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary px-4">Lưu điểm</button>
              <a href="/teaching_assignment" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

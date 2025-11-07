<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<hr>
<div class="container-fluid py-5 text-black" style="background-color: rgb(7, 15, 74); background-size: cover; background-position: center;">
  <div class="container ">
    <div class="row p-3 rounded-2">
      <div class="col-md-6 offset-md-3 p-3">
        <div id="signup" class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="m-0 text-center"><strong>Đăng nhập</strong></h2>


          <form class="row g-3 p-3" method="post" action="/login">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="col-12">
              <label for="peopleId" class="col-form-label fw-bold">Mã số đăng nhập: </label>
              <input  
                type="text" 
                id="peopleId" 
                placeholder="Nhập mã số đăng nhập"
                class="form-control <?= isset($errors['peopleId']) ? 'is-invalid' : '' ?>" 
                name="peopleId" 
                value="<?= isset($old['peopleId']) ? $this->e($old['peopleId']) : '' ?>" 
                required autofocus
              >
              <?php if (isset($errors['peopleId'])) : ?>
                <span class="invalid-feedback">
                  <strong><?= $this->e($errors['peopleId']) ?></strong>
                </span>
              <?php endif ?>
            </div>

            <div class="col-12">
              <label for="password" class="col-form-label fw-bold">Mật khẩu: </label>
              <input 
                type="password" 
                id="password" 
                placeholder="Nhập mật khẩu"
                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                name="password" 
                required 
              >
              <?php if (isset($errors['password'])) : ?>
                <span class="invalid-feedback">
                  <strong><?= $this->e($errors['password']) ?></strong>
                </span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-5">
              <button type="submit" class="btn btn-primary">Đăng nhập</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php if (isset($messages['error'])): ?>
  <script>
    alert("<?= $this->e($messages['error']) ?>");
  </script>
<?php endif; ?>
<?php $this->stop() ?>

<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<div class="container-fluid py-5 text-black">
  <div class="container">
    <div class="row p-3 rounded-2">
      <div class="col-md-8 offset-md-2 p-3">
        <div class="bg-white p-4 rounded-2 shadow-lg">
          <h2 class="text-center fw-bold mb-4">Chỉnh sửa thông tin sinh viên</h2>
          <form class="row g-3" method="post" action="/student/edit/<?= $this->e($student->id) ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Mã số sinh viên -->
            <div class="form-group col-md-6">
              <label for="peopleId" class="fw-bold">Mã số sinh viên:</label>
              <input 
                type="text" 
                id="peopleId" 
                name="peopleId" 
                class="form-control <?= isset($errors['peopleId']) ? 'is-invalid' : '' ?>" 
                value="<?= isset($old['peopleId']) ? $this->e($old['peopleId']) : $this->e($student->peopleId) ?>"
                <?= !$is_admin ? 'readonly' : '' ?>
              >
              <?php if (isset($errors['peopleId'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['peopleId']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Họ tên -->
            <div class="form-group col-md-6">
              <label for="name" class="fw-bold">Họ tên:</label>
              <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                value="<?= isset($old['name']) ? $this->e($old['name']) : $this->e($student->name) ?>"
                <?= !$is_admin ? 'readonly' : '' ?>
              >
              <?php if (isset($errors['name'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['name']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Ngày sinh -->
            <div class="form-group col-md-6">
              <label for="dob" class="fw-bold">Ngày sinh:</label>
              <input 
                type="date" 
                id="dob" 
                name="dob" 
                class="form-control <?= isset($errors['dob']) ? 'is-invalid' : '' ?>"
                value="<?= isset($old['dob']) ? $this->e($old['dob']) : $this->e($student->dob) ?>"
              >
              <?php if (isset($errors['dob'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['dob']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Giới tính -->
            <div class="form-group col-md-6">
              <label for="gender" class="fw-bold">Giới tính:</label>
              <select id="gender" name="gender" class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" <?= !$is_admin ? 'disabled' : '' ?>>
                <option value="Nam" <?= (isset($old['gender']) ? $old['gender'] : $student->gender) == 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= (isset($old['gender']) ? $old['gender'] : $student->gender) == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
              </select>
              <?php if (!$is_admin): ?>
                <input type="hidden" name="gender" value="<?= isset($old['gender']) ? $this->e($old['gender']) : $this->e($student->gender) ?>">
              <?php endif ?>
              <?php if (isset($errors['gender'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['gender']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Số điện thoại -->
            <div class="form-group col-md-6">
              <label for="phone" class="fw-bold">Số điện thoại:</label>
              <input 
                type="text" 
                id="phone" 
                name="phone" 
                class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                value="<?= isset($old['phone']) ? $this->e($old['phone']) : $this->e($student->phone) ?>"
              >
              <?php if (isset($errors['phone'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['phone']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Quê quán -->
            <div class="form-group col-md-6">
              <label for="hometown" class="fw-bold">Quê quán:</label>
              <input 
                type="text" 
                id="hometown" 
                name="hometown" 
                class="form-control <?= isset($errors['hometown']) ? 'is-invalid' : '' ?>"
                value="<?= isset($old['hometown']) ? $this->e($old['hometown']) : $this->e($student->hometown) ?>"
              >
              <?php if (isset($errors['hometown'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['hometown']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Ngành -->
            <div class="form-group col-md-6">
              <label for="major_id" class="fw-bold">Ngành:</label>
              <select id="major_id" name="major_id" class="form-select <?= isset($errors['major_id']) ? 'is-invalid' : '' ?>" <?= !$is_admin ? 'disabled' : '' ?>>
                <?php foreach ($majors as $major): ?>
                  <option value="<?= $major->id ?>" 
                    <?= (isset($old['major_id']) ? $old['major_id'] : $student->major_id) == $major->id ? 'selected' : '' ?>>
                    <?= $this->e($major->majorName) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (!$is_admin): ?>
                <input type="hidden" name="major_id" value="<?= isset($old['major_id']) ? $this->e($old['major_id']) : $this->e($student->major_id) ?>">
              <?php endif ?>
              <?php if (isset($errors['major_id'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['major_id']) ?></strong></span>
              <?php endif ?>
            </div>

            <!-- Khóa -->
            <div class="form-group col-md-6">
              <label for="course_id" class="fw-bold">Khóa:</label>
              <select id="course_id" name="course_id" class="form-select <?= isset($errors['course_id']) ? 'is-invalid' : '' ?>" <?= !$is_admin ? 'disabled' : '' ?>>
                <?php foreach ($courses as $course): ?>
                  <option value="<?= $course->id ?>" 
                    <?= (isset($old['course_id']) ? $old['course_id'] : $student->course_id) == $course->id ? 'selected' : '' ?>>
                    <?= $this->e($course->courseName) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if (!$is_admin): ?>
                <input type="hidden" name="course_id" value="<?= isset($old['course_id']) ? $this->e($old['course_id']) : $this->e($student->course_id) ?>">
              <?php endif ?>
              <?php if (isset($errors['course_id'])): ?>
                <span class="invalid-feedback"><strong><?= $this->e($errors['course_id']) ?></strong></span>
              <?php endif ?>
            </div>

            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
              <?php if (AUTHGUARD()->user()->role === 'admin') : ?>
                <a href="/student" class="btn btn-secondary ms-2">Quay lại</a>
              <?php elseif (AUTHGUARD()->user()->role === 'student') : ?>
                <a href="/home" class="btn btn-secondary ms-2">Quay lại</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->stop() ?>

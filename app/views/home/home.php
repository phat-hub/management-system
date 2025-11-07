<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>
<?php if (AUTHGUARD()->user()->role === 'admin') : ?>
  <hr>
  <div class="container py-5">
    <div class="bg-white p-5 rounded-3 shadow-lg">
      <h2 class="text-center mb-5 fw-bold">Chức năng hệ thống</h2>
      <div class="row row-cols-1 row-cols-md-3 g-4 text-center">
        <div class="col">
          <a href="/student" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm hover-shadow">
              <i class="bi bi-person-fill display-4 mb-3"></i>
              <h5>Sinh viên</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/lecturer" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-person-badge-fill display-4 mb-3"></i>
              <h5>Giảng viên</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/semester" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-calendar2-week-fill display-4 mb-3"></i>
              <h5>Học kỳ</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/subject" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-journal-bookmark-fill display-4 mb-3"></i>
              <h5>Học phần</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/classroom" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-building display-4 mb-3"></i>
              <h5>Phòng học</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/teaching_assignment" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-clipboard-data-fill display-4 mb-3"></i>
              <h5>Phân công giảng dạy</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/department" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-building-fill display-4 mb-3"></i>
              <h5>Khoa</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/major" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm">
              <i class="bi bi-diagram-3-fill display-4 mb-3"></i>
              <h5>Ngành</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="/course" class="text-decoration-none text-dark">
            <div class="border rounded-3 p-4 h-100 shadow-sm hover-shadow">
              <i class="bi bi-mortarboard-fill display-4 mb-3"></i>
              <h5>Khóa</h5>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
<?php elseif (AUTHGUARD()->user()->role === 'student') : ?>
<hr>
<div class="container-fluid py-5 text-white">
  <div class="container">
    <div class="row p-3 rounded-2">
      <!-- Cột thông tin sinh viên -->
      <div class="col-md-6">
        <div class="bg-white text-dark p-4 rounded-2 shadow-lg">
          <h3 class="mb-4 text-center">Thông tin sinh viên</h3>
          <p><strong>Mã số sinh viên:</strong> <?= $this->e(AUTHGUARD()->user()->peopleId) ?></p>
          <p><strong>Họ tên:</strong> <?= $this->e(AUTHGUARD()->user()->name) ?></p>
          <p>
            <strong>Ngày sinh:</strong> <?= (AUTHGUARD()->user()->dob !== 'Chưa cập nhật')
                      ? date('d/m/Y', strtotime(AUTHGUARD()->user()->dob))
                      : 'Chưa cập nhật' ?>
          </p>
          <p><strong>Giới tính:</strong> <?= $this->e(AUTHGUARD()->user()->gender) ?></p>
          <p><strong>Lớp:</strong> <?= $this->e(AUTHGUARD()->user()->class) ?></p>
          <p><strong>Ngành:</strong> <?= $this->e($majorName) ?></p>
          <p><strong>Khóa:</strong> <?= $this->e($courseName) ?></p>
          <p><strong>Khoa:</strong> <?= $this->e($departmentName) ?></p>
          <p><strong>Quê quán:</strong> <?= ucfirst($this->e(AUTHGUARD()->user()->hometown)) ?></p>
          <p><strong>Số điện thoại:</strong> <?= ucfirst($this->e(AUTHGUARD()->user()->phone)) ?></p>
          <p><strong>Số tín chỉ đã tích lũy:</strong> <?= $this->e($totalCredits) ?></p>
        </div>
      </div>

      <!-- Cột chức năng học vụ -->
      <div class="col-md-6">
        <div class="bg-white text-dark p-4 rounded-2 shadow-lg">
          <h3 class="mb-4 text-center">Chức năng hệ thống</h3>
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/subject_registration/score_result" class="text-decoration-none"><i class="bi bi-bar-chart-line-fill me-2"></i>Kết quả học tập</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/teaching_assignment" class="text-decoration-none"><i class="bi bi-pencil-square me-2"></i>Đăng ký học phần</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/subject_registration" class="text-decoration-none"><i class="bi bi-calendar-event me-2"></i>Lịch học</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/student/edit/<?= AUTHGUARD()->user()->id ?>" class="text-decoration-none"><i class="bi bi-person-lines-fill me-2"></i>Cập nhật thông tin</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/tuition" class="text-decoration-none">
                <i class="bi bi-cash-coin me-2"></i>Học phí
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php elseif (AUTHGUARD()->user()->role === 'lecturer') : ?>
<hr>
<div class="container-fluid py-5 text-white" style="background-color: rgb(7, 15, 74); background-size: cover; background-position: center;">
  <div class="container">
    <div class="row p-3 rounded-2">
      <!-- Cột thông tin giảng viên -->
      <div class="col-md-6">
        <div class="bg-white text-dark p-4 rounded-2 shadow-lg">
          <h3 class="mb-4 text-center">Thông tin giảng viên</h3>
          <p><strong>Mã số giảng viên:</strong> <?= $this->e(AUTHGUARD()->user()->peopleId) ?></p>
          <p><strong>Họ tên:</strong> <?= $this->e(AUTHGUARD()->user()->name) ?></p>
          <p>
            <strong>Ngày sinh:</strong> <?= (AUTHGUARD()->user()->dob !== 'Chưa cập nhật')
                      ? date('d/m/Y', strtotime(AUTHGUARD()->user()->dob))
                      : 'Chưa cập nhật' ?>
          </p>
          <p><strong>Giới tính:</strong> <?= $this->e(AUTHGUARD()->user()->gender) ?></p>
          <p><strong>Khoa:</strong> <?= $this->e($departmentName) ?></p>
          <p><strong>Quê quán:</strong> <?= $this->e(AUTHGUARD()->user()->hometown) ?></p>
          <p><strong>Số điện thoại:</strong> <?= ucfirst($this->e(AUTHGUARD()->user()->phone)) ?></p>
        </div>
      </div>

      <!-- Cột chức năng học vụ -->
      <div class="col-md-6">
        <div class="bg-white text-dark p-4 rounded-2 shadow-lg">
          <h3 class="mb-4 text-center">Chức năng học vụ</h3>
          <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/teaching_assignment" class="text-decoration-none"><i class="bi bi-calendar3 me-2"></i>Lịch dạy</a>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/lecturer/edit/<?= AUTHGUARD()->user()->id ?>" class="text-decoration-none"><i class="bi bi-person-lines-fill me-2"></i>Cập nhật thông tin</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if (isset($messages['success'])): ?>
  <script>
    alert("<?= $this->e($messages['success']) ?>");
  </script>
<?php endif; ?>
<?php if (isset($messages['error'])): ?>
  <script>
    alert("<?= $this->e($messages['error']) ?>");
  </script>
<?php endif; ?>
<?php $this->stop() ?>

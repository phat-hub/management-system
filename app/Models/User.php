<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $db;

    public int $id = -1;
    public string $peopleId;
    public string $name;
    public string $password;
    public string $role;
    public ?string $gender;
    public ?string $class;
    public ?int $major_id;
    public ?int $department_id;
    public ?int $course_id;
    public ?string $phone = 'Chưa cập nhật';
    public ?string $dob = 'Chưa cập nhật';
    public ?string $hometown = 'Chưa cập nhật';
    public bool $is_locked = false; 

    // Thuộc tính từ bảng liên kết
    public string $major_name;
    public string $department_name;
    public string $course_name;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): User
    {
        $statement = $this->db->prepare("SELECT * FROM users WHERE $column = :value");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch();
        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function fill(array $data): User
    {
        $this->peopleId = $data['peopleId'];
        $this->name = $data['name'];
        $this->password = $this->password ?? password_hash($data['password'], PASSWORD_DEFAULT);
        $this->role = $data['role'];
        $this->gender = $data['gender'] ?? '';
        $this->class = $data['class'];
        $this->major_id = $data['major_id'];
        $this->department_id = $data['department_id'] ?? 0;
        $this->course_id = $data['course_id'];
        $this->phone = $data['phone'] ?? $this->phone;
        $this->dob = $data['dob'] ?? $this->dob;
        $this->hometown = $data['hometown'] ?? $this->hometown;
        $this->is_locked = $data['is_locked'] ?? false; 
        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id = $row['id'];
        $this->peopleId = $row['peopleId'];
        $this->name = $row['name'];
        $this->password = $row['password'];
        $this->role = $row['role'];
        $this->gender = $row['gender'];
        $this->class = $row['class'];
        $this->major_id = $row['major_id'];
        $this->department_id = $row['department_id'];
        $this->course_id = $row['course_id'];
        $this->phone = $row['phone'];
        $this->dob = $row['dob'];
        $this->hometown = $row['hometown'];
        $this->major_name = $row['major_name'] ?? '';
        $this->department_name = $row['department_name'] ?? '';
        $this->course_name = $row['course_name'] ?? '';
        $this->is_locked = (bool)($row['is_locked'] ?? 0); 
    }

    private function isPeopleIdInUse(string $peopleId): bool
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM users WHERE peopleId = :peopleId');
        $statement->execute(['peopleId' => $peopleId]);
        return $statement->fetchColumn() > 0;
    }

    private function isPhoneInUse(string $phone): bool
    {
        $statement = $this->db->prepare('SELECT COUNT(*) FROM users WHERE phone = :phone AND id != :id');
        $statement->execute(['phone' => $phone, 'id' => $this->id]);
        return $statement->fetchColumn() > 0;
    }

    public function getAllStudents(): array
    {
        $statement = $this->db->prepare(
            'SELECT users.*, 
                    majors.majorName AS major_name, 
                    departments.departmentName AS department_name,
                    courses.courseName AS course_name
            FROM users
            LEFT JOIN majors ON users.major_id = majors.id
            LEFT JOIN departments ON majors.department_id = departments.id
            LEFT JOIN courses ON users.course_id = courses.id
            WHERE users.role = :role'
        );

        $statement->execute(['role' => 'student']);

        $students = [];
        while ($row = $statement->fetch()) {
            $student = new User($this->db);
            $student->fillFromDbRow($row);
            $students[] = $student;
        }

        return $students;
    }

    public function getAllLecturers(): array
    {
        $statement = $this->db->prepare(
            'SELECT users.*, 
                    departments.departmentName AS department_name
             FROM users
             LEFT JOIN departments ON users.department_id = departments.id
             WHERE users.role = :role'
        );

        $statement->execute(['role' => 'lecturer']);

        $lecturers = [];
        while ($row = $statement->fetch()) {
            $lecturer = new User($this->db);
            $lecturer->fillFromDbRow($row);
            $lecturer->department_name = $row['department_name'];
            $lecturers[] = $lecturer;
        }

        return $lecturers;
    }

    public function save(): bool
    {
        if ($this->id === -1) {
            // Thêm mới
            $statement = $this->db->prepare(
                'INSERT INTO users (
                    peopleId, name, password, role, gender, class, major_id, department_id, course_id, phone, dob, hometown, is_locked
                ) VALUES (
                    :peopleId, :name, :password, :role, :gender, :class, :major_id, :department_id, :course_id, :phone, :dob, :hometown, :is_locked
                )'
            );

            return $statement->execute([
                'peopleId' => $this->peopleId,
                'name' => $this->name,
                'password' => $this->password,
                'role' => $this->role,
                'gender' => $this->gender,
                'class' => $this->class,
                'major_id' => $this->major_id,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'phone' => $this->phone,
                'dob' => $this->dob,
                'hometown' => $this->hometown,
                'is_locked' => $this->is_locked ? 1 : 0,
            ]);
        } else {
            // Cập nhật
            $statement = $this->db->prepare(
                'UPDATE users SET
                    peopleId = :peopleId,
                    name = :name,
                    password = :password,
                    role = :role,
                    gender = :gender,
                    class = :class,
                    major_id = :major_id,
                    department_id = :department_id,
                    course_id = :course_id,
                    phone = :phone,
                    dob = :dob,
                    hometown = :hometown,
                    is_locked = :is_locked
                WHERE id = :id'
            );

            return $statement->execute([
                'peopleId' => $this->peopleId,
                'name' => $this->name,
                'password' => $this->password,
                'role' => $this->role,
                'gender' => $this->gender,
                'class' => $this->class,
                'major_id' => $this->major_id,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'phone' => $this->phone,
                'dob' => $this->dob,
                'hometown' => $this->hometown,
                'is_locked' => $this->is_locked ? 1 : 0,
                'id' => $this->id,
            ]);
        }
    }

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số không được để trống.';
        } elseif ($this->isPeopleIdInUse($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số đã được sử dụng.';
        }

        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên không được để trống.';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        } elseif ($data['password'] !== $data['password_confirmation']) {
            $errors['password'] = 'Mật khẩu không khớp.';
        }

        if (empty($data['gender'])) {
            $errors['gender'] = 'Vui lòng chọn giới tính.';
        }

        if (empty($data['major_id']) || $data['major_id'] == 0) {
            $errors['major_id'] = 'Vui lòng chọn ngành học.';
        }

        if (empty($data['course_id']) || $data['course_id'] == 0) {
            $errors['course_id'] = 'Vui lòng chọn khóa học.';
        }

        return $errors;
    }

    public function validateLecturer(array $data): array
    {
        $errors = [];

        if (empty($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số không được để trống.';
        } elseif ($this->isPeopleIdInUse($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số đã được sử dụng.';
        }

        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên không được để trống.';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        } elseif ($data['password'] !== $data['password_confirmation']) {
            $errors['password'] = 'Mật khẩu không khớp.';
        }

        if (empty($data['gender'])) {
            $errors['gender'] = 'Vui lòng chọn giới tính.';
        }

        if (empty($data['department_id']) || $data['department_id'] == 0) {
            $errors['department_id'] = 'Vui lòng chọn khoa.';
        }

        return $errors;
    }

    public function validateEdit(array $data): array
    {
        $errors = [];

        // Kiểm tra mã số sinh viên
        if (empty($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số sinh viên không được để trống.';
        } elseif ($data['peopleId'] !== $this->peopleId && $this->isPeopleIdInUse($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số sinh viên đã được sử dụng.';
        }

        // Kiểm tra họ tên
        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên không được để trống.';
        }

        // Kiểm tra ngày sinh
        if (empty($data['dob'])) {
            $errors['dob'] = 'Ngày sinh không được để trống.';
        } elseif (strtotime($data['dob']) >= time()) {
            $errors['dob'] = 'Ngày sinh phải nhỏ hơn ngày hiện tại.';
        }

        // Kiểm tra giới tính
        if (empty($data['gender'])) {
            $errors['gender'] = 'Vui lòng chọn giới tính.';
        }

        // Kiểm tra số điện thoại
        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống.';
        } elseif (!preg_match('/^(03|05|07|08|09)\d{8}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không đúng định dạng Việt Nam.';
        } elseif ($data['phone'] !== $this->phone && $this->isPhoneInUse($data['phone'])) {
            $errors['phone'] = 'Số điện thoại đã được sử dụng.';
        }

        // Kiểm tra quê quán
        if (empty($data['hometown'])) {
            $errors['hometown'] = 'Quê quán không được để trống.';
        }

        return $errors;
    }

    public function validateEditLecturer(array $data): array
    {
        $errors = [];

        // Kiểm tra mã số sinh viên
        if (empty($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số giảng viên không được để trống.';
        } elseif ($data['peopleId'] !== $this->peopleId && $this->isPeopleIdInUse($data['peopleId'])) {
            $errors['peopleId'] = 'Mã số giảng viên đã được sử dụng.';
        }

        // Kiểm tra họ tên
        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên không được để trống.';
        }

        // Kiểm tra ngày sinh
        if (empty($data['dob'])) {
            $errors['dob'] = 'Ngày sinh không được để trống.';
        } elseif (strtotime($data['dob']) >= time()) {
            $errors['dob'] = 'Ngày sinh phải nhỏ hơn ngày hiện tại.';
        }

        // Kiểm tra giới tính
        if (empty($data['gender'])) {
            $errors['gender'] = 'Vui lòng chọn giới tính.';
        }

        // Kiểm tra số điện thoại
        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không được để trống.';
        } elseif (!preg_match('/^(03|05|07|08|09)\d{8}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không đúng định dạng Việt Nam.';
        } elseif ($data['phone'] !== $this->phone && $this->isPhoneInUse($data['phone'])) {
            $errors['phone'] = 'Số điện thoại đã được sử dụng.';
        }

        // Kiểm tra quê quán
        if (empty($data['hometown'])) {
            $errors['hometown'] = 'Quê quán không được để trống.';
        }

        return $errors;
    }

    public function countStudentsByMajorDeptCourse(int $major_id, int $department_id, int $course_id): int
    {
        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM users 
             WHERE major_id = :major_id AND department_id = :department_id AND course_id = :course_id'
        );
        $statement->execute([
            'major_id' => $major_id,
            'department_id' => $department_id,
            'course_id' => $course_id
        ]);
        return (int)$statement->fetchColumn();
    }

    public function lock(): bool
    {
        $statement = $this->db->prepare('UPDATE users SET is_locked = 1 WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

    public function unlock(): bool
    {
        $statement = $this->db->prepare('UPDATE users SET is_locked = 0 WHERE id = :id');
        return $statement->execute(['id' => $this->id]);
    }

}

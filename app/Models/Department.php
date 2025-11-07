<?php

namespace App\Models;

use PDO;

class Department
{
    private PDO $db;

    public int $id = -1;
    public string $departmentCode;
    public string $departmentName;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function where(string $column, string $value): Department
    {
        $statement = $this->db->prepare("SELECT * FROM departments WHERE $column = :value");
        $statement->execute(['value' => $value]);
        $row = $statement->fetch();
        if ($row) {
            $this->fillFromDbRow($row);
        }
        return $this;
    }

    public function save(): bool
    {
        $result = false;

        if ($this->id >= 0) {
            $statement = $this->db->prepare(
                'UPDATE departments SET departmentCode = :code, departmentName = :name WHERE id = :id'
            );
            $result = $statement->execute([
                'id' => $this->id,
                'code' => $this->departmentCode,
                'name' => $this->departmentName,
            ]);
        } else {
            $statement = $this->db->prepare(
                'INSERT INTO departments (departmentCode, departmentName) VALUES (:code, :name)'
            );
            $result = $statement->execute([
                'code' => $this->departmentCode,
                'name' => $this->departmentName,
            ]);

            if ($result) {
                $this->id = $this->db->lastInsertId();
            }
        }

        return $result;
    }

    public function fill(array $data): Department
    {
        $this->departmentCode = $data['departmentCode'];
        $this->departmentName = $data['departmentName'];
        return $this;
    }

    private function fillFromDbRow(array $row)
    {
        $this->id = $row['id'];
        $this->departmentCode = $row['departmentCode'];
        $this->departmentName = $row['departmentName'];
    }

    public function validate(array $data): array
    {
        $errors = [];

        // Kiểm tra mã khoa đã tồn tại
        $statement = $this->db->prepare('SELECT COUNT(*) FROM departments WHERE departmentCode = :code');
        $statement->execute(['code' => $data['departmentCode']]);
        if ($statement->fetchColumn() > 0) {
            $errors['departmentCode'] = 'Mã khoa này đã tồn tại.';
        }

        // Kiểm tra tên khoa đã tồn tại
        $statement = $this->db->prepare('SELECT COUNT(*) FROM departments WHERE departmentName = :name');
        $statement->execute(['name' => $data['departmentName']]);
        if ($statement->fetchColumn() > 0) {
            $errors['departmentName'] = 'Tên khoa này đã tồn tại.';
        }

        // Kiểm tra mã khoa không được bỏ trống
        if (!$data['departmentCode']) {
            $errors['departmentCode'] = 'Mã khoa không hợp lệ.';
        }

        // Kiểm tra tên khoa không được bỏ trống
        if (!$data['departmentName']) {
            $errors['departmentName'] = 'Tên khoa không hợp lệ.';
        }

        return $errors;
    }


    public function getAllDepartments(): array
    {
        $statement = $this->db->prepare('SELECT * FROM departments');
        $statement->execute();

        $departments = [];
        while ($row = $statement->fetch()) {
            $department = new Department($this->db);
            $department->fillFromDbRow($row);
            $departments[] = $department;
        }

        return $departments;
    }

    public function find(int $id): ?Department
    {
        $statement = $this->db->prepare(
            'SELECT * FROM departments WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        if ($row = $statement->fetch()) {
            $this->fillFromDbRow($row);
            return $this;
        }

        return null;
    }

    public function validateEdit(array $data, $currentDepartmentCode, $currentDepartmentName): array
    {
        $errors = [];

        // Kiểm tra mã khoa không được bỏ trống
        if (empty($data['departmentCode'])) {
            $errors['departmentCode'] = 'Mã khoa không được để trống.';
        } else {
            // Kiểm tra xem mã khoa có bị thay đổi không, và nếu có, kiểm tra sự tồn tại trong DB
            if ($data['departmentCode'] !== $currentDepartmentCode) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM departments WHERE departmentCode = :departmentCode");
                $stmt->execute(['departmentCode' => $data['departmentCode']]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $errors['departmentCode'] = 'Mã khoa này đã tồn tại.';
                }
            }
        }

        // Kiểm tra tên khoa không được bỏ trống
        if (empty($data['departmentName'])) {
            $errors['departmentName'] = 'Tên khoa không được để trống.';
        } else {
            // Kiểm tra xem tên khoa có bị thay đổi không, và nếu có, kiểm tra sự tồn tại trong DB
            if ($data['departmentName'] !== $currentDepartmentName) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM departments WHERE departmentName = :departmentName");
                $stmt->execute(['departmentName' => $data['departmentName']]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $errors['departmentName'] = 'Tên khoa này đã tồn tại.';
                }
            }
        }

        return $errors;
    }

    public function delete(): bool
    {
        // Kiểm tra nếu id cần xóa có liên kết trong bảng majors
        $checkMajors = $this->db->prepare(
            'SELECT COUNT(*) FROM majors WHERE department_id = :id'
        );
        $checkMajors->execute(['id' => $this->id]);
        $majorCount = $checkMajors->fetchColumn();

        if ($majorCount > 0) {
            return false; // Không xóa vì có ngành liên kết
        }

        // Kiểm tra nếu id cần xóa có liên kết trong bảng users
        $checkUsers = $this->db->prepare(
            'SELECT COUNT(*) FROM users WHERE department_id = :id'
        );
        $checkUsers->execute(['id' => $this->id]);
        $userCount = $checkUsers->fetchColumn();

        if ($userCount > 0) {
            return false; // Không xóa vì có người dùng liên kết
        }

        // Nếu không có liên kết nào, tiến hành xóa
        $statement = $this->db->prepare(
            'DELETE FROM departments WHERE id = :id'
        );
        return $statement->execute(['id' => $this->id]);
    }



}

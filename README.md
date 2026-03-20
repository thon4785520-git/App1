# ระบบฐานข้อมูลผู้เชี่ยวชาญ มหาวิทยาลัยราชภัฏสงขลา

## 1. System Overview
โปรเจกต์นี้ถูกปรับใหม่ให้เป็น **PHP แบบธรรมดา (Procedural PHP)** ไม่ใช้ OOP และไม่ใช้ MVC ตามที่ต้องการ โดยแบ่งการทำงานเป็นไฟล์หน้าเว็บตรง ๆ ในโฟลเดอร์ `public/` และรวมฟังก์ชันกลางไว้ใน `includes/` เพื่อให้อ่านง่าย แก้ไขง่าย และนำขึ้นโฮสต์ Apache แบบ shared hosting ได้สะดวก

### User Roles
- **Admin**: จัดการข้อมูลทั้งหมด อนุมัติโปรไฟล์ ลบข้อมูล และดู dashboard
- **Expert**: ลงทะเบียน กรอก/แก้ไขข้อมูลตนเอง อัปโหลดรูปและ Resume
- **Viewer**: ดูข้อมูลผู้เชี่ยวชาญ ค้นหา และ export profile

### Modules
1. ข้อมูลส่วนตัว
2. ข้อมูลการปฏิบัติงาน
3. ข้อมูลด้านวิชาการ
4. ข้อมูลการพัฒนาตนเอง
5. ข้อมูลอื่น ๆ เช่น รางวัล ความเชี่ยวชาญ Portfolio และ Social Links

### Features
- Login / Register / Logout พร้อม password hashing
- Dashboard สรุปข้อมูลผู้เชี่ยวชาญ
- CRUD โปรไฟล์ผู้เชี่ยวชาญแบบหน้า PHP ธรรมดา
- Search + Filter + Pagination
- Upload รูปโปรไฟล์และ Resume PDF
- Admin approve profile
- Export profile สำหรับสั่งพิมพ์/บันทึกเป็น PDF
- AJAX skill suggestions ด้วย fetch API

## 2. ER Diagram (อธิบาย)
ฐานข้อมูลออกแบบให้เป็น **3NF** และแยกข้อมูลเป็นตารางหลัก/ตารางลูกเพื่อลดความซ้ำซ้อน

- `users` เก็บบัญชีผู้ใช้และสิทธิ์ใช้งาน
- `experts` เก็บข้อมูลโปรไฟล์หลัก
- `work_experience`, `research`, `training`, `seminars`, `awards`, `social_links` เป็นตารางลูกแบบ 1:N
- `skills` เป็นตาราง master สำหรับ tag ความสามารถ
- `expert_skill` เป็นตารางเชื่อมความสัมพันธ์ many-to-many

```text
users (1) ──── (1..n) experts
experts (1) ──── (n) work_experience
experts (1) ──── (n) research
experts (1) ──── (n) training
experts (1) ──── (n) seminars
experts (1) ──── (n) awards
experts (1) ──── (n) social_links
experts (n) ──── (n) skills ผ่าน expert_skill
```

## 3. Database Schema (SQL Script)
ใช้ไฟล์ `database/schema.sql` สำหรับสร้างฐานข้อมูลและตารางทั้งหมด พร้อม seed admin เริ่มต้นสำหรับเข้าใช้งานระบบ

## 4. Project Structure
```text
/config          ค่าคอนฟิกระบบ
/includes        ฟังก์ชันกลาง, auth, PDO, repository functions, layout
/public          หน้าเว็บหลัก, actions, api, uploads
/assets          CSS / JavaScript
/database        SQL schema
```

## 5. ตัวอย่างโค้ด (Controller, Model, View)
โปรเจกต์นี้ **ไม่มี Controller/Model/View แบบ MVC แล้ว** แต่แทนด้วยแนวทางที่ง่ายกว่า
- `includes/bootstrap.php` สำหรับ config, session, auth helper และ PDO
- `includes/expert_repository.php` สำหรับคำสั่ง query และ logic การจัดการข้อมูล
- `public/*.php` สำหรับหน้าแสดงผลและ action handler

## 6. UI Layout (HTML + Bootstrap)
- Sidebar + Top Navbar
- Card layout สำหรับ dashboard และ expert list
- Repeater form สำหรับโมดูลย่อย
- Responsive และมี animation เล็กน้อย

## 7. ฟีเจอร์สำคัญ (Login, CRUD, Upload)
### Login
ใช้ `password_hash()` / `password_verify()` และ session regeneration

### CRUD
ใช้หน้า `expert_form.php`, `experts.php`, `expert_view.php` และ action files ใน `public/actions/`

### Upload
ใช้ `save_upload()` เพื่อตรวจ MIME type และบันทึกไฟล์ลง `public/uploads/`

## Bonus: วิธี Deploy บน Apache
1. ชี้ DocumentRoot ไปที่โฟลเดอร์ `public/`
2. Import `database/schema.sql`
3. แก้ค่า database และ `base_url` ใน `config/app.php`
4. ให้สิทธิ์เขียนกับ `public/uploads/`
5. หากใช้ shared hosting สามารถอัปโหลดทั้งโปรเจกต์ได้โดยไม่ต้องมี Composer

## Bonus: การขยายในอนาคต
- เพิ่ม REST API โดยแยกไฟล์ใน `public/api/`
- เพิ่ม mobile app ได้โดย reuse ตารางเดิม
- เพิ่ม workflow อนุมัติหลายขั้นตอนหรือเชื่อม SSO ได้ภายหลัง

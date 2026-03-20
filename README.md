# ระบบฐานข้อมูลผู้เชี่ยวชาญ มหาวิทยาลัยราชภัฏสงขลา

## 1. System Overview
ระบบนี้เป็น Web Application แบบ Full Stack สำหรับจัดเก็บ ค้นหา และอนุมัติข้อมูลผู้เชี่ยวชาญของมหาวิทยาลัยราชภัฏสงขลา โดยออกแบบเป็น PHP 8 แบบ OOP ในโครงสร้าง MVC ใช้ PDO + Prepared Statements เพื่อความปลอดภัย และใช้ Bootstrap 4 เพื่อสร้าง UI ที่ดูเป็นมืออาชีพ ทันสมัย และรองรับการใช้งานทุกอุปกรณ์

### บทบาทผู้ใช้
- **Admin**: จัดการข้อมูลทั้งหมด อนุมัติโปรไฟล์ ลบข้อมูล และดู Dashboard
- **Expert**: ลงทะเบียน กรอก และแก้ไขโปรไฟล์ตนเอง พร้อมอัปโหลดรูป/Resume
- **Viewer**: เข้าดูข้อมูลผู้เชี่ยวชาญ ค้นหา และส่งออก PDF

### โมดูลหลัก
1. ข้อมูลส่วนตัว
2. ข้อมูลการปฏิบัติงาน
3. ข้อมูลด้านวิชาการ
4. ข้อมูลการพัฒนาตนเอง
5. ข้อมูลอื่น ๆ เช่น รางวัล Portfolio และ Social Links

### ฟีเจอร์สำคัญ
- Login / Register / Logout พร้อม Password Hashing
- Dashboard สรุปจำนวนผู้เชี่ยวชาญ โปรไฟล์ที่อนุมัติ และผลงานวิจัย
- CRUD สำหรับโปรไฟล์ผู้เชี่ยวชาญและโมดูลย่อย
- Search + Filter ตาม keyword และ skill tag
- Pagination สำหรับหน้าแสดงรายการผู้เชี่ยวชาญ
- Upload รูปโปรไฟล์และ Resume PDF
- Admin Approval Workflow
- Export PDF Profile
- AJAX skill suggestion ผ่าน Fetch API

## 2. ER Diagram (อธิบาย)
โครงสร้างฐานข้อมูลออกแบบให้อยู่ในระดับ **3NF** โดยแยกข้อมูลที่เปลี่ยนแปลงบ่อยและข้อมูลแบบ repeating group ออกจากตารางหลัก `experts`

- `users` เก็บข้อมูลบัญชีและสิทธิ์ใช้งาน
- `experts` เก็บข้อมูลโปรไฟล์หลัก 1:1 กับผู้ใช้ที่เป็นผู้เชี่ยวชาญ
- `work_experience`, `research`, `training`, `awards`, `social_links` เป็นตารางลูกแบบ 1:N เพื่อรองรับหลายรายการต่อหนึ่ง expert
- `skills` แยกเป็น master table เพื่อป้องกันข้อมูลซ้ำ
- `expert_skill` เป็นตารางเชื่อม M:N ระหว่าง expert และ skill

```text
users (1) ──── (1..n) experts
experts (1) ──── (n) work_experience
experts (1) ──── (n) research
experts (1) ──── (n) training
experts (1) ──── (n) awards
experts (1) ──── (n) social_links
experts (n) ──── (n) skills ผ่าน expert_skill
```

### เหตุผลเชิงออกแบบ
- ลดการซ้ำซ้อนของข้อมูลทักษะและข้อมูลผลงาน
- รองรับการขยายในอนาคต เช่น API, Mobile App, Workflow เพิ่มเติม
- ใช้ Foreign Key ครบถ้วนเพื่อรักษาความถูกต้องเชิงอ้างอิง

## 3. Database Schema (SQL Script)
ไฟล์ SQL พร้อมใช้งานอยู่ที่ `database/schema.sql` สามารถ import ได้ทันทีบน MySQL 8+ โดยมีทั้ง DDL และ seed สำหรับ admin เริ่มต้น

## 4. Project Structure
```text
/config          ค่าคอนฟิกระบบ
/controllers     Controller สำหรับ MVC
/models          Model สำหรับติดต่อฐานข้อมูล
/views           View Template แยกตามโมดูล
/assets          CSS / JavaScript / รูปประกอบ
/uploads         โฟลเดอร์สำหรับไฟล์อัปโหลด
/database        SQL schema และ data seed
/public          Front Controller (index.php)
/core            Router, Controller, Auth, Database, helpers
```

## 5. ตัวอย่างโค้ด (Controller, Model, View)
- **Controller**: `controllers/ExpertController.php` มีตัวอย่างการ validate, upload ไฟล์, authorize และจัดการ CRUD
- **Model**: `models/Expert.php` มีตัวอย่าง pagination, search/filter และ sync ตารางลูกด้วย PDO
- **View**: `views/experts/form.php` แสดงตัวอย่างฟอร์มหลายโมดูลในหน้าเดียว รองรับ Repeater Form

## 6. UI Layout (HTML + Bootstrap)
- Sidebar Menu + Top Navbar เพื่อเหมาะกับงานบริหารข้อมูล
- Card Layout สำหรับ Dashboard และรายการผู้เชี่ยวชาญ
- Badge, Icon, Timeline และ Hover Animation ช่วยให้อ่านข้อมูลได้ง่าย
- Responsive Design รองรับ Mobile / Tablet / Desktop

## 7. ฟีเจอร์สำคัญ (Login, CRUD, Upload)
### Login / Register
- ใช้ `password_hash()` และ `password_verify()`
- แยก guest layout และ app layout ชัดเจน
- ใช้ session regeneration เมื่อ login/logout

### CRUD ผู้เชี่ยวชาญ
- Admin และ Expert สามารถเพิ่ม/แก้ไขข้อมูลได้
- Admin สามารถอนุมัติและลบโปรไฟล์ได้
- Viewer เป็นสิทธิ์ดูอย่างเดียว

### Upload File
- ตรวจ MIME type ของรูปภาพและ PDF
- สร้างชื่อไฟล์แบบ unique
- แยก path เป็น `uploads/profile` และ `uploads/resume`

## Bonus: วิธี Deploy บน Apache
1. ตั้ง DocumentRoot ไปที่โฟลเดอร์ `public/`
2. เปิดใช้ `mod_rewrite` และสร้าง VirtualHost ชี้ไปยังโปรเจกต์
3. สร้างฐานข้อมูลแล้ว import `database/schema.sql`
4. แก้ค่าใน `config/app.php` ให้ตรงกับ environment จริง
5. เปิดสิทธิ์เขียนให้โฟลเดอร์ `uploads/`

## Bonus: รองรับการขยายในอนาคต
- เพิ่ม REST API โดย reuse Model/Service เดิมและส่ง JSON ผ่าน controller ใหม่
- เชื่อม Mobile App ได้โดยใช้ token-based authentication ในอนาคต
- รองรับการเพิ่ม approval workflow หลายขั้นตอน หรือเชื่อม SSO ของมหาวิทยาลัยได้

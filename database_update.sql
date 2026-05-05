-- 1. Tambahkan kolom is_locked untuk mengontrol akses SKL
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS is_locked INT DEFAULT 0;

-- 2. Tambahkan kolom prank_level untuk menentukan level prank siswa
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS prank_level VARCHAR(50) DEFAULT 'NONE';

-- 3. Tambahkan kolom prank_started_at untuk mencatat waktu dimulainya prank
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS prank_started_at DATETIME NULL;

-- 4. Tambahkan kolom prank_duration untuk mencatat durasi waktu prank
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS prank_duration INT DEFAULT 0;

-- 5. Tambahkan kolom is_viewed untuk mencatat status siswa yang sudah melihat pengumuman
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS is_viewed INT DEFAULT 0;

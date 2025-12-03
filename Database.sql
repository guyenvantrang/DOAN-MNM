CREATE TABLE LOAI_SP (
    MALOAI VARCHAR(20) PRIMARY KEY NOT NULL,
    TENLOAI VARCHAR(100) NOT NULL,
    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE THUONGHIEU (
    MATHUONGHIEU VARCHAR(20) PRIMARY KEY NOT NULL,
    TENTHUONGHIEU VARCHAR(100) NOT NULL,
    QUOCGIA VARCHAR(100),
    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 1️⃣ THÔNG SỐ: ĐƯỜNG KÍNH MẶT
-- =========================================
CREATE TABLE THONGSO_DUONGKINH(
    MADK VARCHAR(10) PRIMARY KEY NOT NULL,
    MOTA TEXT,
    CHISO TEXT,
    DONVIDO TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 2️⃣ THÔNG SỐ: CHIỀU DÀY DÂY
-- =========================================
CREATE TABLE THONGSO_CHIEUDAIDAY(
    MADD VARCHAR(10) PRIMARY KEY NOT NULL,
    MOTA TEXT,
    CHISO TEXT,
    DONVIDO TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 3️⃣ THÔNG SỐ: ĐỘ DÀY
-- =========================================
CREATE TABLE THONGSO_DODAY(
    MADDY VARCHAR(10) PRIMARY KEY NOT NULL,
    MOTA TEXT,
    CHISO TEXT,
    DONVIDO TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 4️⃣ THÔNG SỐ: CHIỀU RỘNG DÂY
-- =========================================
CREATE TABLE THONGSO_CHIEURONGDAY(
    MCRD VARCHAR(10) PRIMARY KEY NOT NULL,
    MOTA TEXT,
    CHISO TEXT,
    DONVIDO TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 5️⃣ THÔNG SỐ: KHỐI LƯỢNG
-- =========================================
CREATE TABLE THONGSO_KHOILUONG(
    MKL VARCHAR(10) PRIMARY KEY NOT NULL,
    MOTA TEXT,
    CHISO TEXT,
    DONVIDO TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 6️⃣ CÔNG NGHỆ CHỐNG NƯỚC
-- =========================================
CREATE TABLE CONGNGHE_CHONGNUOC(
    MCN VARCHAR(10) PRIMARY KEY NOT NULL,
    TEN TEXT,
    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 7️⃣ MÀU SẮC
-- =========================================
CREATE TABLE MAUSAC(
    MMS VARCHAR(10) PRIMARY KEY NOT NULL,
    TENMAU TEXT,
    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 8️⃣ CHỨC NĂNG
-- =========================================
CREATE TABLE CACCHUCNANG(
    MCNANG VARCHAR(10) PRIMARY KEY NOT NULL,
    TENCHUCNANG TEXT,
    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================
-- 9️⃣ BẢNG SẢN PHẨM
-- =========================================
CREATE TABLE SANPHAM (
    MASP VARCHAR(20) PRIMARY KEY NOT NULL,
    TENSP VARCHAR(200) NOT NULL,
    MATHUONGHIEU VARCHAR(20),
    MALOAI VARCHAR(20),
    GIABAN DECIMAL(15,0),
    GIANHAP DECIMAL(15,0),
    SOLUONGTON INT DEFAULT 0,
    HINHANHCHINH VARCHAR(255),
    CHITIETHINHANH VARCHAR(50),

    -- Khóa ngoại liên kết với các bảng thông số
    MADK VARCHAR(10),
    MADD VARCHAR(10),
    MADDY VARCHAR(10),
    MCRD VARCHAR(10),
    MKL VARCHAR(10),
    MCN VARCHAR(10),
    MMS VARCHAR(10),
    MCNANG VARCHAR(10),

    MOTA TEXT,
    NGAYTAO DATETIME DEFAULT CURRENT_TIMESTAMP,
    NGAYSUA DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- 🔗 Khóa ngoại liên kết
    CONSTRAINT FK_SANPHAM_THUONGHIEU FOREIGN KEY (MATHUONGHIEU) REFERENCES THUONGHIEU(MATHUONGHIEU),
    CONSTRAINT FK_SANPHAM_LOAI FOREIGN KEY (MALOAI) REFERENCES LOAI_SP(MALOAI),
    CONSTRAINT FK_SANPHAM_DUONGKINH FOREIGN KEY (MADK) REFERENCES THONGSO_DUONGKINH(MADK),
    CONSTRAINT FK_SANPHAM_CHIEUDAIDAY FOREIGN KEY (MADD) REFERENCES THONGSO_CHIEUDAIDAY(MADD),
    CONSTRAINT FK_SANPHAM_DODAY FOREIGN KEY (MADDY) REFERENCES THONGSO_DODAY(MADDY),
    CONSTRAINT FK_SANPHAM_CHIEURONGDAY FOREIGN KEY (MCRD) REFERENCES THONGSO_CHIEURONGDAY(MCRD),
    CONSTRAINT FK_SANPHAM_KHOILUONG FOREIGN KEY (MKL) REFERENCES THONGSO_KHOILUONG(MKL),
    CONSTRAINT FK_SANPHAM_CHONGNUOC FOREIGN KEY (MCN) REFERENCES CONGNGHE_CHONGNUOC(MCN),
    CONSTRAINT FK_SANPHAM_MAU FOREIGN KEY (MMS) REFERENCES MAUSAC(MMS),
    CONSTRAINT FK_SANPHAM_CHUCNANG FOREIGN KEY (MCNANG) REFERENCES CACCHUCNANG(MCNANG)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- LOẠI SẢN PHẨM
INSERT INTO LOAI_SP (MALOAI, TENLOAI, MOTA) VALUES
('LO01', 'Classic', 'Đồng hồ cổ điển, sang trọng'),
('LO02', 'Sport', 'Đồng hồ thể thao, năng động'),
('LO03', 'Fashion', 'Đồng hồ thời trang, hiện đại');
--THƯƠNG HIỆU
INSERT INTO THUONGHIEU (MATHUONGHIEU, TENTHUONGHIEU, QUOCGIA, MOTA) VALUES
('TH01', 'Casio', 'Nhật Bản', 'Thương hiệu phổ biến, giá tốt'),
('TH02', 'Citizen', 'Nhật Bản', 'Đồng hồ năng lượng ánh sáng'),
('TH03', 'Tissot', 'Thụy Sĩ', 'Thương hiệu cao cấp, sang trọng');
-- THÔNG SỐ ĐƯỜNG KÍNH
INSERT INTO THONGSO_DUONGKINH (MADK, MOTA, CHISO, DONVIDO) VALUES
('DK01','Đường kính mặt tròn','40','mm'),
('DK02','Đường kính mặt tròn','42','mm'),
('DK03','Đường kính mặt vuông','38','mm');

-- THÔNG SỐ CHIỀU DÀY DÂY
INSERT INTO THONGSO_CHIEUDAIDAY (MADD, MOTA, CHISO, DONVIDO) VALUES
('CD01','Chiều dài dây chuẩn','200','mm'),
('CD02','Chiều dài dây dài','220','mm'),
('CD03','Chiều dài dây ngắn','180','mm');

-- THÔNG SỐ ĐỘ DÀY
INSERT INTO THONGSO_DODAY (MADDY, MOTA, CHISO, DONVIDO) VALUES
('DD01','Độ dày mặt đồng hồ','10','mm'),
('DD02','Độ dày mặt đồng hồ','12','mm'),
('DD03','Độ dày mặt đồng hồ','8','mm');

-- THÔNG SỐ CHIỀU RỘNG DÂY
INSERT INTO THONGSO_CHIEURONGDAY (MCRD, MOTA, CHISO, DONVIDO) VALUES
('CR01','Rộng dây chuẩn','20','mm'),
('CR02','Rộng dây lớn','22','mm'),
('CR03','Rộng dây nhỏ','18','mm');

-- KHỐI LƯỢNG
INSERT INTO THONGSO_KHOILUONG (MKL, MOTA, CHISO, DONVIDO) VALUES
('KL01','Trọng lượng nhẹ','50','g'),
('KL02','Trọng lượng vừa','70','g'),
('KL03','Trọng lượng nặng','90','g');

-- CÔNG NGHỆ CHỐNG NƯỚC
INSERT INTO CONGNGHE_CHONGNUOC (MCN, TEN, MOTA) VALUES
('CN01','Chống nước 30m','Thích hợp đi mưa, rửa tay'),
('CN02','Chống nước 50m','Có thể đi bơi nhẹ'),
('CN03','Chống nước 100m','Đi bơi, snorkeling');

-- MÀU SẮC
INSERT INTO MAUSAC (MMS, TENMAU, MOTA) VALUES
('MS01','Đen','Mặt và dây đen'),
('MS02','Trắng','Mặt trắng, dây kim loại'),
('MS03','Vàng','Mặt vàng, dây vàng');

-- CHỨC NĂNG
INSERT INTO CACCHUCNANG (MCNANG, TENCHUCNANG, MOTA) VALUES
('CNF01','Chronograph','Có chức năng bấm giờ'),
('CNF02','GMT','Hiển thị múi giờ thứ 2'),
('CNF03','Báo thức','Có chức năng báo thức');

INSERT INTO SANPHAM 
(MASP, TENSP, MATHUONGHIEU, MALOAI, GIABAN, GIANHAP, SOLUONGTON, 
HINHANHCHINH, CHITIETHINHANH, MADK, MADD, MADDY, MCRD, MKL, MCN, MMS, MCNANG, MOTA)
VALUES
('SP01','Đồng hồ Classic','TH01','LO01',5000000,3500000,10,'sp01_main.jpg','sp01_1.jpg',
 'DK01','CD01','DD01','CR01','KL01','CN01','MS01','CNF01','Đồng hồ cổ điển, mặt tròn, dây da.'),
('SP02','Đồng hồ Sport','TH01','LO02',7000000,5000000,15,'sp02_main.jpg','sp02_1.jpg',
 'DK02','CD02','DD02','CR02','KL02','CN02','MS02','CNF02','Đồng hồ thể thao, chống nước 50m.'),
('SP03','Đồng hồ Luxury','TH02','LO01',15000000,12000000,5,'sp03_main.jpg','sp03_1.jpg',
 'DK03','CD03','DD03','CR03','KL03','CN03','MS03','CNF01','Đồng hồ sang trọng, mặt vuông vàng.'),
('SP04','Đồng hồ Minimal','TH02','LO03',6000000,4500000,8,'sp04_main.jpg','sp04_1.jpg',
 'DK01','CD02','DD01','CR02','KL01','CN01','MS02','CNF03','Đồng hồ tối giản, dây kim loại.'),
('SP05','Đồng hồ Chrono','TH03','LO02',12000000,9000000,12,'sp05_main.jpg','sp05_1.jpg',
 'DK02','CD01','DD02','CR01','KL02','CN02','MS01','CNF01','Đồng hồ Chronograph, dây da.'),
('SP06','Đồng hồ Quartz','TH01','LO03',4000000,3000000,20,'sp06_main.jpg','sp06_1.jpg',
 'DK01','CD03','DD01','CR03','KL01','CN01','MS03','CNF02','Đồng hồ Quartz, mặt trắng.'),
('SP07','Đồng hồ Automatic','TH03','LO01',13000000,10000000,6,'sp07_main.jpg','sp07_1.jpg',
 'DK02','CD02','DD03','CR02','KL03','CN03','MS02','CNF02','Đồng hồ Automatic, mặt tròn, dây da.'),
('SP08','Đồng hồ Fashion','TH02','LO03',5500000,4000000,9,'sp08_main.jpg','sp08_1.jpg',
 'DK03','CD03','DD01','CR03','KL02','CN02','MS01','CNF03','Đồng hồ thời trang, dây vàng.'),
('SP09','Đồng hồ Sport Pro','TH03','LO02',9000000,6500000,14,'sp09_main.jpg','sp09_1.jpg',
 'DK02','CD01','DD02','CR02','KL02','CN03','MS02','CNF01','Đồng hồ Sport Pro, bấm giờ, chống nước.'),
('SP10','Đồng hồ Classic Gold','TH01','LO01',16000000,13000000,4,'sp10_main.jpg','sp10_1.jpg',
 'DK03','CD02','DD03','CR01','KL03','CN03','MS03','CNF01','Đồng hồ Classic, mặt vuông vàng, dây vàng.');




funtions.blade
color.blade
brand.blade
parameters-diameter.blade
parameters-lenghtstrap.blade
parameters-thickness.blade
parameters-weight.blade
parameters-withstrap.blade
technology-waterproof.blade

funtions-add.blade.php
funtions-fix.blade.php

color-add.blade.php
color-fix.blade.php

brand-add.blade.php
brand-fix.blade.php

parameters-diameter-add.blade.php
parameters-diameter-fix.blade.php

parameters-lenghtstrap-add.blade.php
parameters-lenghtstrap-fix.blade.php

parameters-thickness-add.blade.php
parameters-thickness-fix.blade.php

parameters-weight-add.blade.php
parameters-weight-fix.blade.php

parameters-withstrap-add.blade.php
parameters-withstrap-fix.blade.php

technology-waterproof-add.blade.php
technology-waterproof-fix.blade.php

thuonghieucontroller.php
cacchucnangcontroller.php
mausaccontroller.php
thongsoduongkinhcontroller.php
thongsochieudaidaycontroller.php
thongsochieurongdaycontroller.php
thongsododaycontroller.php
thongsokhoiluongcontroller.php
congnghechongnuoccontroller.php
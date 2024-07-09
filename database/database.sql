CREATE DATABASE IF NOT EXISTS api_exercise;

USE api_exercise;

-- Creazione della tabella users
CREATE TABLE users (
    id INT PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Inserimento dei dati di base nella tabella users
INSERT INTO
    users (
        id,
        uuid,
        name,
        email,
        email_verified_at,
        password,
        remember_token,
        created_at,
        updated_at
    )
VALUES (
        9,
        '58dd59d7-d9d0-4bd2-ae95-a81440b64cfa',
        'admin',
        'admin@gmail.com',
        NULL,
        '$2y$12$B1nOIQXysbZtxTohwJZZoePw3VbE2BIHFNJ9Yt2Oquc4zHHkm6hfa',
        NULL,
        '2024-06-27 14:51:08',
        '2024-06-27 14:51:08'
    ),
    (
        10,
        'baa3e13c-2730-4e6d-8733-4739b6b736ac',
        'user',
        'user@gmail.com',
        NULL,
        '$2y$12$4xhKl3tts2pkBLRcEWsUAOWNphx175ReMIPn16iJWkB1GNwUC12m.',
        NULL,
        '2024-06-27 14:51:18',
        '2024-06-27 14:51:18'
    );

-- Creazione della tabella roles
CREATE TABLE roles (
    id INT PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    role_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

-- Inserimento dei dati di base nella tabella roles
INSERT INTO
    roles (
        id,
        user_id,
        role_id,
        created_at
    )
VALUES (
        1,
        'ce8010af-eb14-44ae-8732-001bf9d268e5',
        'admin',
        '2024-06-26 15:02:53'
    ),
    (
        2,
        'd04f40ab-bf5f-4c3f-80a4-16790855f945',
        'user',
        '2024-06-26 15:02:53'
    );

-- Creazione della tabella user_roles con chiavi esterne user_id e role_id
CREATE TABLE user_roles (
    id INT PRIMARY KEY,
    user_id INT,
    role_id INT,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (role_id) REFERENCES roles (id)
);

-- Esempio di inserimento di dati nella tabella user_roles
INSERT INTO
    user_roles (id, user_id, role_id)
VALUES (1, 9, 1),
    (2, 10, 2);

-- Creazione della tabella sessions
CREATE TABLE sessions (
    id INT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

-- Creazione della tabella role_permissions
CREATE TABLE role_permissions (
    id INT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles (id),
    FOREIGN KEY (permission_id) REFERENCES permissions (id)
);

-- Inserimento dei dati forniti nella tabella role_permissions
INSERT INTO
    role_permissions (id, role_id, permission_id)
VALUES (21, 1, 2),
    (22, 1, 3),
    (23, 1, 4),
    (24, 1, 1),
    (30, 1, 5),
    (31, 1, 6),
    (32, 1, 7),
    (33, 1, 8);

-- Creazione della tabella products
CREATE TABLE products (
    id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    category VARCHAR(255),
    image VARCHAR(255),
    rating_rate DECIMAL(3, 1),
    rating_count DECIMAL(10, 2)
);

-- Inserimento dei dati forniti nella tabella products
INSERT INTO
    products (
        id,
        title,
        price,
        description,
        category,
        image,
        rating_rate,
        rating_count
    )
VALUES (
        1,
        'Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops',
        109.95,
        'Your perfect pack for everyday use and walks in the forest. Stash your laptop (up to 15 inches) in the padded sleeve, your everyday',
        'men''s clothing',
        'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg',
        3.9,
        NULL
    ),
    (
        2,
        'Mens Casual Premium Slim Fit T-Shirts',
        22.30,
        'Slim-fitting style, contrast raglan long sleeve, three-button henley placket, light weight & soft fabric for breathable and comfortable wearing. And Solid stitched shirts with round neck made for durability and a great fit for casual fashion wear and die...',
        'men''s clothing',
        'https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg',
        4.1,
        NULL
    ),
    (
        3,
        'Mens Cotton Jacket',
        55.99,
        'great outerwear jackets for Spring/Autumn/Winter, suitable for many occasions, such as working, hiking, camping, mountain/rock climbing, cycling, traveling or other outdoors. Good gift choice for you or your family member. A warm hearted love to Father, ...',
        'men''s clothing',
        'https://fakestoreapi.com/img/71li-ujtlUL._AC_UX679_.jpg',
        4.7,
        NULL
    ),
    (
        4,
        'Mens Casual Slim Fit',
        15.99,
        'The color could be slightly different between on the screen and in practice. / Please note that body builds vary by person, therefore, detailed size information should be reviewed below on the product description.',
        'men''s clothing',
        'https://fakestoreapi.com/img/71YXzeOuslL._AC_UY879_.jpg',
        2.1,
        NULL
    ),
    (
        5,
        'John Hardy Women''s Legends Naga Gold & Silver Dragon Station Chain Bracelet',
        695.00,
        'From our Legends Collection, the Naga was inspired by the mythical water dragon that protects the ocean''s pearl. Wear facing inward to be bestowed with love and abundance, or outward for protection.',
        'jewelery',
        'https://fakestoreapi.com/img/71pWzhdJNwL._AC_UL640_QL65_ML3_.jpg',
        4.6,
        NULL
    ),
    (
        6,
        'Solid Gold Petite Micropave',
        168.00,
        'Satisfaction Guaranteed. Return or exchange any order within 30 days.Designed and sold by Hafeez Center in the United States. Satisfaction Guaranteed. Return or exchange any order within 30 days.',
        'jewelery',
        'https://fakestoreapi.com/img/61sbMiUnoGL._AC_UL640_QL65_ML3_.jpg',
        3.9,
        NULL
    ),
    (
        7,
        'White Gold Plated Princess',
        9.99,
        'Classic Created Wedding Engagement Solitaire Diamond Promise Ring for Her. Gifts to spoil your love more for Engagement, Wedding, Anniversary, Valentine''s Day...',
        'jewelery',
        'https://fakestoreapi.com/img/71YAIFU48IL._AC_UL640_QL65_ML3_.jpg',
        3.0,
        NULL
    ),
    (
        8,
        'Pierced Owl Rose Gold Plated Stainless Steel Double',
        10.99,
        'Rose Gold Plated Double Flared Tunnel Plug Earrings. Made of 316L Stainless Steel',
        'jewelery',
        'https://fakestoreapi.com/img/51UDEzMJVpL._AC_UL640_QL65_ML3_.jpg',
        1.9,
        NULL
    ),
    (
        9,
        'WD 2TB Elements Portable External Hard Drive - USB 3.0',
        64.00,
        'USB 3.0 and USB 2.0 Compatibility Fast data transfers Improve PC Performance High Capacity; Compatibility Formatted NTFS for Windows 10, Windows 8.1, Windows 7; Reformatting may be required for other operating systems; Compatibility may vary depending on...',
        'electronics',
        'https://fakestoreapi.com/img/61IBBVJvSDL._AC_SY879_.jpg',
        3.3,
        NULL
    ),
    (
        10,
        'SanDisk SSD PLUS 1TB Internal SSD - SATA III 6 Gb/s',
        109.00,
        'Easy upgrade for faster boot up, shutdown, application load and response (As compared to 5400 RPM SATA 2.5” hard drive; Based on published specifications and internal benchmarking tests using PCMark vantage scores) Boosts burst write performance, makin...',
        'electronics',
        'https://fakestoreapi.com/img/61U7T1koQqL._AC_SX679_.jpg',
        2.9,
        NULL
    ),
    (
        11,
        'Silicon Power 256GB SSD 3D NAND A55 SLC Cache Performance Boost SATA III 2.5',
        109.00,
        '3D NAND flash are applied to deliver high transfer speeds Remarkable transfer speeds that enable faster bootup and improved overall system performance. The advanced SLC Cache Technology allows performance boost and longer lifespan 7mm slim design suitabl...',
        'electronics',
        'https://fakestoreapi.com/img/71kWymZ+c+L._AC_SX679_.jpg',
        4.8,
        NULL
    ),
    (
        12,
        'WD 4TB Gaming Drive Works with Playstation 4 Portable External Hard Drive',
        114.00,
        'Expand your PS4 gaming experience, Play anywhere Fast and easy, setup Sleek design with high capacity, 3-year manufacturer''s limited warranty',
        'electronics',
        'https://fakestoreapi.com/img/61mtL65D4cL._AC_SX679_.jpg',
        4.8,
        NULL
    ),
    (
        13,
        'Acer SB220Q bi 21.5 inches Full HD (1920 x 1080) IPS Ultra-Thin',
        599.00,
        '21. 5 inches Full HD (1920 x 1080) widescreen IPS display And Radeon free Sync technology. No compatibility for VESA Mount Refresh Rate: 75Hz - Using HDMI port Zero-frame design | ultra-thin | 4ms response time | IPS panel Aspect ratio - 16: 9. Color Sup...',
        'electronics',
        'https://fakestoreapi.com/img/81QpkIctqPL._AC_SX679_.jpg',
        2.9,
        NULL
    ),
    (
        14,
        'Samsung 49-Inch CHG90 144Hz Curved Gaming Monitor (LC49HG90DMNXZA) – Super Ultrawide Screen QLED',
        999.99,
        '49 INCH SUPER ULTRAWIDE 32:9 CURVED GAMING MONITOR with dual 27 inch screen side by side QUANTUM DOT (QLED) TECHNOLOGY, HDR support and factory calibration provides stunningly realistic and accurate color and contrast 144HZ HIGH REFRESH RATE and 1ms ultr...',
        'electronics',
        'https://fakestoreapi.com/img/81Zt42ioCgL._AC_SX679_.jpg',
        2.2,
        NULL
    ),
    (
        15,
        'BIYLACLESEN Women''s 3-in-1 Snowboard Jacket Winter Coats',
        56.99,
        'Note:The Jackets is US standard size, Please choose size as your usual wear Material: 100% Polyester; Detachable Liner Fabric: Warm Fleece. Detachable Functional Liner: Skin Friendly, Lightweigt and Warm.Stand Collar Liner jacket, keep you warm in cold w...',
        'women''s clothing',
        'https://fakestoreapi.com/img/51Y5NI-I5jL._AC_UX679_.jpg',
        2.6,
        NULL
    ),
    (
        16,
        'Lock and Love Women''s Removable Hooded Faux Leather Moto Biker Jacket',
        29.95,
        '100% POLYURETHANE(shell) 100% POLYESTER(lining) 75% POLYESTER 25% COTTON (SWEATER), Faux leather material for style and comfort / 2 pockets of front, 2-For-One Hooded denim style faux leather jacket, Button detail on waist / Detail stitching at sides, HA...',
        'women''s clothing',
        'https://fakestoreapi.com/img/81XH0e8fefL._AC_UY879_.jpg',
        2.9,
        NULL
    ),
    (
        17,
        'Rain Jacket Women Windbreaker Striped Climbing Raincoats',
        39.99,
        'Lightweight perfet for trip or casual wear---Long sleeve with hooded, adjustable drawstring waist design. Button and zipper front closure raincoat, fully stripes Lined and The Raincoat has 2 side pockets are a good size to hold all kinds of things, it co...',
        'women''s clothing',
        'https://fakestoreapi.com/img/71HblAHs5xL._AC_UY879_-2.jpg',
        3.8,
        NULL
    ),
    (
        18,
        'MBJ Women''s Solid Short Sleeve Boat Neck V',
        9.85,
        '95% RAYON 5% SPANDEX, Made in USA or Imported, Do Not Bleach, Lightweight fabric with great stretch for comfort, Ribbed on sleeves and neckline / Double stitching on bottom hem',
        'women''s clothing',
        'https://fakestoreapi.com/img/71z3kpMAYsL._AC_UY879_.jpg',
        4.7,
        NULL
    ),
    (
        19,
        'Opna Women''s Short Sleeve Moisture',
        7.95,
        '100% Polyester, Machine wash, 100% cationic polyester interlock, Machine Wash & Pre Shrunk for a Great Fit, Lightweight, roomy and highly breathable with moisture wicking fabric which helps to keep moisture away, Soft Lightweight Fabric with comfortable ...',
        'women''s clothing',
        'https://fakestoreapi.com/img/51eg55uWmdL._AC_UX679_.jpg',
        4.5,
        NULL
    ),
    (
        20,
        'DANVOUY Womens T Shirt Casual Cotton Short',
        12.99,
        '95%Cotton,5%Spandex, Features: Casual, Short Sleeve, Letter Print,V-Neck,Fashion Tees, The fabric is soft and has some stretch., Occasion: Casual/Office/Beach/School/Home/Street. Season: Spring,Summer,Autumn,Winter.',
        'women''s clothing',
        'https://fakestoreapi.com/img/61pHAEJ4NML._AC_UX679_.jpg',
        3.6,
        NULL
    );

-- Creazione della tabella permissions
CREATE TABLE permissions (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Inserimento dei dati forniti nella tabella permissions
INSERT INTO
    permissions (
        id,
        name,
        description,
        created_at,
        updated_at
    )
VALUES (
        1,
        'create_user',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        2,
        'read_user',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        3,
        'update_user',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        4,
        'delete_user',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        5,
        'create_role',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        6,
        'read_role',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        7,
        'update_role',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    ),
    (
        8,
        'delete_role',
        '',
        '2024-06-26 15:02:53',
        '2024-06-26 15:02:53'
    );

-- Creazione della tabella password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL
);

-- Creazione della tabella order_product_details
CREATE TABLE order_product_details (
    id INT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id),
    FOREIGN KEY (product_id) REFERENCES products (id)
);

-- Creazione della tabella oauth_refresh_tokens
CREATE TABLE oauth_refresh_tokens (
    id VARCHAR(100) PRIMARY KEY,
    access_token_id VARCHAR(100) NOT NULL,
    revoked BOOLEAN NOT NULL,
    expires_at DATETIME NULL
);

-- Creazione della tabella oauth_personal_access_clients
CREATE TABLE oauth_personal_access_clients (
    id INT PRIMARY KEY,
    client_id VARCHAR(36) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Inserimento di un dato di esempio nella tabella oauth_personal_access_clients
INSERT INTO
    oauth_personal_access_clients (
        id,
        client_id,
        created_at,
        updated_at
    )
VALUES (
        1,
        '9c61391b-fda5-4747-97da-66fa8c3fd696',
        '2024-06-26 14:57:18',
        '2024-06-26 14:57:18'
    );

-- Creazione della tabella oauth_clients
CREATE TABLE oauth_clients (
    id VARCHAR(36) PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(255) NOT NULL,
    secret VARCHAR(100) NOT NULL,
    provider VARCHAR(100) NULL,
    redirect VARCHAR(255) NOT NULL,
    personal_access_client TINYINT(1) NOT NULL,
    password_client TINYINT(1) NOT NULL,
    revoked TINYINT(1) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Inserimento del dato di esempio nella tabella oauth_clients
INSERT INTO
    oauth_clients (
        id,
        user_id,
        name,
        secret,
        provider,
        redirect,
        personal_access_client,
        password_client,
        revoked,
        created_at,
        updated_at
    )
VALUES (
        '9c61391b-fda5-4747-97da-66fa8c3fd696',
        NULL,
        'Laravel Personal Access Client',
        'wp7DRKs3uwdtvAkfdisNHGD4tDp9xKa3qywsBzGt',
        'http://localhost',
        1,
        0,
        0,
        '2024-06-26 14:57:18',
        '2024-06-26 14:57:18'
    );

-- Creazione della tabella oauth_auth_codes
CREATE TABLE oauth_auth_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    client_id VARCHAR(36) NOT NULL,
    scopes TEXT,
    revoked TINYINT(1) NOT NULL,
    expires_at TIMESTAMP NULL
);

-- Creazione della tabella oauth_access_tokens
CREATE TABLE oauth_access_tokens (
    id VARCHAR(100) PRIMARY KEY,
    user_id INT NULL,
    client_id VARCHAR(36) NOT NULL,
    name VARCHAR(255),
    scopes TEXT,
    revoked TINYINT(1) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NULL
);

-- Creazione della tabella migrations
CREATE TABLE migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
);

-- Creazione della tabella jobs
CREATE TABLE jobs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts INT NOT NULL DEFAULT 0,
    reserved_at TIMESTAMP NULL,
    available_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL
);

-- Creazione della tabella job_batches
CREATE TABLE job_batches (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids TEXT,
    options TEXT,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    finished_at TIMESTAMP NULL
);

-- Creazione della tabella failed_jobs
CREATE TABLE failed_jobs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(36) NOT NULL,
    connection VARCHAR(255) NOT NULL,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL
);

-- Creazione della tabella cache_locks
CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` TIMESTAMP NOT NULL
);

-- Creazione della tabella cache
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` TEXT NOT NULL,
    `expiration` TIMESTAMP NOT NULL
);
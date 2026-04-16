SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE `boty` (
  `IDB`       int          NOT NULL AUTO_INCREMENT,
  `shoeName`  varchar(2000) NOT NULL,
  `popisek`   mediumtext   NOT NULL,
  `price`     int          NOT NULL,
  `img1`      varchar(200) NOT NULL,
  `img2`      varchar(200) NOT NULL,
  `img3`      varchar(200) NOT NULL,
  `available` tinyint(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`IDB`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `boty` (`IDB`, `shoeName`, `popisek`, `price`, `img1`, `img2`, `img3`) VALUES
(1,  'Travis Scott x Air Jordan 1 Low ''Reverse Mocha''',
     'The Travis Scott x Air Jordan 1 Retro Low OG ''Reverse Mocha'' delivers a twist on the original ''Mocha'' AJ1 Low from 2019. The upper combines a brown suede base with ivory leather overlays and the Houston rapper''s signature reverse Swoosh on the lateral side, featuring oversized dimensions and a neutral cream finish. Contrasting scarlet accents distinguish a pair of woven Nike Air tongue tags, as well as mismatched Cactus Jack and retro Wings logos embroidered on each heel tab.',
     1200, './images/jordan1-reversemocha-1.webp', './images/jordan1-reversemocha-2.webp', './images/jordan1-reversemocha-3.webp'),
(2,  'Air Max DN ''Black Dark Grey''',
     'The Nike Air Max Dn ''Black Dark Grey'' features an allover haptic print that adds texture to the breathable black mesh upper, marked with a retro-inspired Air Max Dn emblem on the molded TPU heel counter.',
     160, './images/airmax-dn-1.webp', './images/airmax-dn-2.webp', './images/airmax-dn-3.webp'),
(3,  'Air Jordan 5 Retro SE ''Sail''',
     'The Air Jordan 5 Retro SE ''Sail'' is crafted with off-white suede on the upper, featuring heritage AJ5 details, including lace locks, molded TPU eyelets and breathable quarter panel netting.',
     220, './images/jordan5-1.webp', './images/jordan5-2.webp', './images/jordan5-3.webp'),
(4,  'Rick Owens DRKSHDW Hexa High ''Black Milk''',
     'These Hexa sneaks are above ankle height and feature eyelets and hooks, Hexa front laces that criss cross the foot, and wrap around the ankle. This heavyweight cotton satin is compact with a rigid handfeel.',
     700, './images/rickowens-1.webp', './images/rickowens-2.webp', './images/rickowens-3.webp'),
(5,  'Dunk Low SB ''Sandy Bodecker''',
     'The Nike Dunk Low SB ''Sandy Bodecker'' is a unique shoe inspired by the 2003 Charity Dunk, sold in a charity auction for Portland skateparks.',
     150, './images/sbdunk-ebay-1.webp', './images/sbdunk-ebay-2.webp', './images/sbdunk-ebay-3.webp'),
(6,  'Supreme x 6 Inch Premium Waterproof Boot ''Wheat''',
     'Crafted from waterproof premium nubuck with an embossed pattern, this boot ensures durability and comfort. It features a padded leather collar and rubber outsoles.',
     400, './images/supreme-timberland-1.webp', './images/supreme-timberland-2.webp', './images/supreme-timberland-3.webp'),
(7,  'Wales Bonner x Samba ''Silver Metallic''',
     'Launching as part of a capsule collection imbued with Caribbean heritage and elevated sport style, the Wales Bonner x adidas Samba ''Silver Metallic'' showcases a polished makeover.',
     600, './images/adidas-walesbonner-1.webp', './images/adidas-walesbonner-2.webp', './images/adidas-walesbonner-3.webp'),
(8,  'HUF x Dunk Low SB ''New York''',
     'The HUF x Nike Dunk Low SB ''New York'' is a special edition shoe celebrating the 20th anniversary of the HUF brand.',
     300, './images/sbdunk-huf-1.webp', './images/sbdunk-huf-2.webp', './images/sbdunk-huf-3.webp'),
(9,  'XT-6 SKYLINE ''Bleached Sand Dazzling Blue''',
     'Experience style and comfort with the Salomon XT-6. These fashion-forward shoes feature a neutral ''Bleached Sand'' base with ''Dazzling Blue'' accents.',
     140, './images/salomon-xt-6-1.webp', './images/salomon-xt-6-2.webp', './images/salomon-xt-6-3.webp'),
(10, 'AIR MAX PLUS ''Triple Black''',
     'Experience the epitome of sleek style with the Nike Airmax Plus. These shoes feature an all-black design, delivering a timeless and versatile look.',
     150, './images/airmax-plus-1.webp', './images/airmax-plus-2.webp', './images/airmax-plus-3.webp'),
(11, 'Dunk Low ''Concord''',
     'The Nike Dunk Low ''Concord'' is built with smooth leather construction, featuring a crisp white base with bluish purple overlays and a color-matched Swoosh.',
     100, './images/dunk-blueconcord-1.webp', './images/dunk-blueconcord-2.webp', './images/dunk-blueconcord-3.webp'),
(12, 'Air Jordan 4 Retro GS ''Hyper Violet''',
     'Offered in grade school sizing, the Air Jordan 4 Retro GS ''Hyper Violet'' features white leather construction with a padded mid-cut collar and breathable quarter-panel netting.',
     140, './images/jordan4-hyperviolet-1.webp', './images/jordan4-hyperviolet-2.webp', './images/jordan4-hyperviolet-3.webp'),
(13, 'Wmns Air Jordan 4 Retro ''Frozen Moments''',
     'The Women''s Air Jordan 4 Retro ''Frozen Moments'' features a pale grey suede upper with a color-matched forefoot overlay in glossy leather.',
     400, './images/jordan4-frozenmoments-1.webp', './images/jordan4-frozenmoments-2.webp', './images/jordan4-frozenmoments-3.webp'),
(14, 'Bapesta ''Black''',
     'Step into the world of streetwear with the Bapesta ''Black''. These shoes showcase a classic all-black design, offering a versatile and timeless look.',
     600, './images/bapesta-1.webp', './images/bapesta-2.webp', './images/bapesta-3.webp'),
(15, 'Rick Owens x DRKSHDW TURBOWPN Mid ''Black Cloud Cream''',
     'Embrace the unconventional with the Rick Owens x DRKSHDW TURBOWPN Mid ''Black Cloud Cream''. These shoes embody the avant-garde aesthetic that Rick Owens is renowned for.',
     380, './images/rickowens-converse-1.webp', './images/rickowens-converse-2.webp', './images/rickowens-converse-3.webp'),
(16, 'Supreme x Air Max 98 TL SP ''Black''',
     'The Supreme x Nike Air Max 98 TL SP ''Black'' is taken from a four-piece collection made exclusively for the influential New York skate brand.',
     350, './images/supreme-airmax-1.webp', './images/supreme-airmax-2.webp', './images/supreme-airmax-3.webp'),
(17, 'Wmns Gazelle Indoor ''Collegiate Green Pink''',
     'Step into retro style with the Wmns Gazelle Indoor ''Collegiate Green Pink''. These shoes feature a vibrant color scheme, adding a pop of color to any outfit.',
     120, './images/adidas-gazelle-1.webp', './images/adidas-gazelle-2.webp', './images/adidas-gazelle-3.webp'),
(18, 'Joe Freshgoods x 1000 ''Pink Mink''',
     'Launching as part of the ''When Things Were Pure Pack'', the Joe Freshgoods x New Balance 1000 ''Pink Mink'' features off-white open-knit mesh with leather overlays.',
     450, './images/newballance-2.webp', './images/newballance-2.webp', './images/newballance-3.webp'),
(19, 'Wmns Air Jordan 3 Retro ''Georgia Peach''',
     'The Women''s Air Jordan 3 Retro ''Georgia Peach'' showcases a white tumbled leather upper, contrasted by orange molded eyelets.',
     200, './images/jordan3-peach-1.webp', './images/jordan3-peach-2.webp', './images/jordan3-peach-3.webp'),
(20, 'Off-White x Wmns Air Jordan 4 Retro SP ''Sail''',
     'Step into high fashion with the Off-White x Wmns Air Jordan 4 Retro SP ''Sail''. This collaboration offers a fresh take on the classic silhouette with a deconstructed leather build.',
     1500, './images/jordan4-offwhite-1.webp', './images/jordan4-offwhite-2.webp', './images/jordan4-offwhite-3.webp');

CREATE TABLE `kosik` (
  `idK` int NOT NULL AUTO_INCREMENT,
  `idB` int NOT NULL,
  `size` double NOT NULL,
  PRIMARY KEY (`idK`),
  KEY `idB` (`idB`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `zakaznik` (
  `idZ`            int          NOT NULL AUTO_INCREMENT,
  `Cname`          varchar(100) NOT NULL,
  `surname`        varchar(100) NOT NULL,
  `email`          varchar(255) NOT NULL,
  `phone`          varchar(30)  NOT NULL,
  `address1`       varchar(255) NOT NULL,
  `address2`       varchar(255) DEFAULT NULL,
  `city`           varchar(100) NOT NULL,
  `country`        varchar(100) NOT NULL,
  `zip`            varchar(20)  NOT NULL,
  `payment_method` varchar(50)  DEFAULT NULL,
  `user_id`        int          DEFAULT NULL,
  `created_at`     datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idZ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `objednavka` (
  `idO` int NOT NULL AUTO_INCREMENT,
  `idZ` int NOT NULL,
  PRIMARY KEY (`idO`),
  KEY `idZ` (`idZ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `name`       varchar(100) NOT NULL,
  `email`      varchar(255) NOT NULL,
  `password`   varchar(255) NOT NULL,
  `phone`      varchar(30)  DEFAULT NULL,
  `address1`   varchar(255) DEFAULT NULL,
  `address2`   varchar(255) DEFAULT NULL,
  `city`       varchar(100) DEFAULT NULL,
  `country`    varchar(100) DEFAULT NULL,
  `zip`        varchar(20)  DEFAULT NULL,
  `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id`        int          NOT NULL AUTO_INCREMENT,
  `order_id`  int          NOT NULL,
  `shoe_id`   int          NOT NULL,
  `shoe_name` varchar(2000) NOT NULL,
  `size`      float        NOT NULL,
  `price`     int          NOT NULL,
  `img`       varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

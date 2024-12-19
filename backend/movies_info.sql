-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 03:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movies_info`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `payment_id`, `schedule_id`, `seat_number`) VALUES
(25, 1, 6, 'D2'),
(26, 1, 6, 'E4'),
(27, 1, 6, 'E5'),
(28, 1, 6, 'E6'),
(29, 1, 6, 'K6-K5'),
(30, 2, 9, 'D5'),
(31, 2, 9, 'D6'),
(32, 2, 9, 'C5'),
(33, 2, 9, 'C6'),
(34, 3, 9, 'K4-K3'),
(35, 3, 9, 'B4'),
(36, 3, 9, 'B3'),
(37, 3, 9, 'G4'),
(38, 3, 9, 'G3');

-- --------------------------------------------------------

--
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `contact_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','resolved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre_name`) VALUES
(1, 'Hài hước'),
(2, 'Hành động'),
(3, 'Kinh dị'),
(4, 'Tâm lý'),
(5, 'Khoa học, viễn tưởng'),
(6, 'Tình cảm'),
(7, 'Phiêu lưu'),
(8, 'Hoạt hình'),
(9, 'Gia đình'),
(10, 'Hồi hộp'),
(11, 'Kịch');

-- --------------------------------------------------------

--
-- Table structure for table `membership_levels`
--

CREATE TABLE `membership_levels` (
  `level_id` int(11) NOT NULL,
  `level_name` enum('bronze','silver','gold','platinum') DEFAULT NULL,
  `discount_percent` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_requests`
--

CREATE TABLE `membership_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `membership_type` enum('U23') DEFAULT NULL,
  `id_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `timestamp`, `is_read`) VALUES
(23, 1, 16, 'xin chào', '2024-12-19 13:32:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `actors` text DEFAULT NULL,
  `trailer_link` varchar(255) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'coming_soon'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `description`, `duration`, `release_date`, `director`, `actors`, `trailer_link`, `poster`, `status`) VALUES
(1, 'Cười Xuyên Biên Giới', 'Cười Xuyên Biên Giới kể về hành trình của Jin-bong (Ryu Seung-ryong) - cựu vô địch bắn cung quốc gia, sau khi nghỉ hưu, anh đã trở thành một nhân viên văn phòng bình thường. Đứng trước nguy cơ bị sa thải, Jin-bong phải nhận một nhiệm vụ bất khả thi là bay đến nửa kia của trái đất trong nỗ lực tuyệt vọng để sinh tồn. Sống sót sau một sự cố đe doạ tính mạng, Jin-bong đã “hạ cánh” xuống khu rừng Amazon, nơi anh gặp bộ ba thổ dân bản địa có kỹ năng bắn cung thượng thừa: Sika, Eeba và Walbu. Tin rằng đã tìm ra cách để tự cứu mình, Jin-bong hợp tác với phiên dịch ngáo ngơ Bbang-sik (Jin Sun-kyu) và đưa ba chiến thần cung thủ đến Hàn Quốc cho một nhiệm vụ táo bạo.', 113, '2024-11-15', 'KIM Chang-ju', 'Ryu Seung-ryong , Jin Sun-kyu, Igor Rafael P EDROSO, Luan B RUM DE ABREU E LIMA, JB João Batista GOMES DE O LIVEIRA, Yeom Hye-ran và Go Kyoung- pyo, Lee Soon-won', 'https://www.youtube.com/embed/c-XbI-G6bqE', 'media_images_2024_11_08_400wx633h-1-154452-081124-12.jpg', 'stopped'),
(2, 'Venom: Kèo Cuối', 'Tom Hardy sẽ tái xuất trong bom tấn Venom: The Last Dance (Tựa Việt: Venom: Kèo Cuối) và phải đối mặt với kẻ thù lớn nhất từ trước đến nay - toàn bộ chủng tộc Symbiote Venom: Kèo cuối - Dự kiến khởi chiếu 25.10.2024', 109, '2024-10-25', 'Kelly Marcel', 'Tom Hardy, Juno Temple, Chiwetel Ejiofor, Clark Backo, Stephen Graham', 'https://www.youtube.com/watch?v=b1Yqng0uSWM', 'media_images_2024_09_19_screenshot-2024-09-19-150036-150139-190924-38.png', 'stopped'),
(3, 'Võ Sĩ Giác Đấu II', 'Sau khi đánh mất quê hương vào tay hoàng đế bạo chúa – người đang cai trị Rome, Lucius trở thành nô lệ giác đấu trong đấu trường Colosseum và phải tìm kiếm sức mạnh từ quá khứ để đưa vinh quang trở lại cho người dân Rome.', 148, '2024-11-15', 'Ridley Scott', 'Pedro Pascal, Denzel Washington, Joseph Quinn', 'https://youtu.be/4rgYUipGJNo', 'media_images_2024_10_23_151124-gladiator-ii-135227-231024-46.jpg', 'stopped'),
(4, 'Hồn Ma Mae Nak', 'Một cặp đôi trẻ đánh thức lại tinh thần của một truyền thuyết cổ xưa nổi tiếng của Thái Lan.', 101, '2024-11-22', 'Mark Duffield', 'Pataratida Pacharawirapong, Siwat Chotchaicharin, Porntip Papanai', 'https://youtu.be/p25Oc8fsr5c', 'media_images_2024_09_24_z5862436965839-ff64bd94049c8727d57fc841202ca29c-152126-240924-29.jpg', 'stopped'),
(5, 'Chiến Địa Tử Thi', 'Chiến Địa Tử Thi lấy bối cảnh miền Nam Thái Lan trong một cuộc xâm lược ít được biết đến của quân đội Nhật Bản thời kỳ Thế chiến 2. Mek (Nonkul) là một hạ sĩ quan trong quân đội Thái Lan mang tình yêu lớn với đất nước, sẵn sàng hy sinh thân mình vì đại cuộc. Ngược lại, người em trai Mok (Awat Rattanaphinta) là một chàng trai trẻ thích tự do, không bao giờ muốn trở thành một người lính như cha và anh trai mình. Đối với Mok, việc tham gia chiến tranh giống như vứt bỏ mạng sống một cách vô ích.', 105, '2024-11-29', 'Kongkiat Komesiri', 'Nonkul, Awat Ratanapintha, Supitcha Sangkhachinda, Akkarat Nimitchai, Thawatchanin Darayon, Thanadol Auepong, Guntapat Kasemsan Na Ayudhya, Seigi Ohzeki', 'https://youtu.be/BkM-we8h9b4', 'media_images_2024_11_29_aw-now-showing-095520-291124-15.jpg', 'stopped'),
(6, 'Công Tử Bạc Liêu', 'Lấy cảm hứng từ giai thoại nổi tiếng của nhân vật được mệnh danh là thiên hạ đệ nhất chơi ngông, Công Tử Bạc Liêu là bộ phim tâm lý hài hước, lấy bối cảnh Nam Kỳ Lục Tỉnh xưa của Việt Nam. BA HƠN - Con trai được thương yêu hết mực của ông Hội đồng Lịnh vốn là chủ ngân hàng đầu tiên tại Việt Nam, sau khi du học Pháp về đã sử dụng cả gia sản của mình vào những trò vui tiêu khiển, ăn chơi trác tán – nên được người dân gọi bằng cái tên Công Tử Bạc Liêu.', 113, '2024-12-06', 'Lý Minh Thắng', 'NSUT Thành Lộc, Song Luân, Công Dương, Đoàn Thiên Ân,…', 'https://www.youtube.com/watch?v=akOLfNUYBbY', 'media_images_2024_10_16_400wx633h-162649-161024-28.jpg', 'now_showing'),
(7, 'Hành Trình Của Moana 2', '“Hành Trình của Moana 2” là màn tái hợp của Moana và Maui sau 3 năm, trở lại trong chuyến phiêu lưu cùng với những thành viên mới. Theo tiếng gọi của tổ tiên, Moana sẽ tham gia cuộc hành trình đến những vùng biển xa xôi của Châu Đại Dương và sẽ đi tới vùng biển nguy hiểm, đã mất tích từ lâu. Cùng chờ đón cuộc phiêu lưu của Moana đầy chông gai sắp tới vào 29.11.2024.\r\n\r\n', 99, '2024-12-04', 'David G. Derrick Jr.', 'Auli\'i Cravalho, Dwayne Johnson, Alan Tudyk', 'https://youtu.be/HA56rBQSueY', 'media_images_2024_10_15_screenshot-2024-10-15-135233-135334-151024-46.png', 'now_showing'),
(8, 'Qủy treo đầu', 'Petai & Nicha - cặp đôi trẻ đẹp liên tục bị quấy phá sau khi chuyển đến ngôi nhà gia truyền của Nicha. Từ những tiếng động cót két chói tai đến những cái chết mất xác; tất cả đều xuất phát từ một lời nguyền cổ xưa ma quái.', 97, '2024-11-29', 'Bo Nipan Chawcharernpon', 'Khun Chanon Ukkharachata, Aniporn Chalermburanawong', 'https://youtu.be/yjL9YgnmVlg', 'media_images_2024_11_20_400x633-17-162001-201124-70.jpg', 'stopped'),
(10, 'Chúa Tể Của Những Chiếc Nhẫn: Cuộc Chiến Của Rohirrim', 'Lấy bối cảnh 183 năm trước những sự kiện trong bộ ba phim gốc, “Chúa Tể Của Những Chiếc Nhẫn: Cuộc Chiến Của Rohirrim\" kể về số phận của Gia tộc của Helm Hammerhand, vị vua huyền thoại của Rohan. Cuộc tấn công bất ngờ của Wulf, lãnh chúa xảo trá và tàn nhẫn của tộc Dunlending, nhằm báo thù cho cái chết của cha hắn, đã buộc Helm và thần dân của ngài phải chống cự trong pháo đài cổ Hornburg - một thành trì vững chãi sau này được biết đến với tên gọi Helm\'s Deep. Tình thế ngày càng tuyệt vọng, Héra, con gái của Helm, phải dốc hết sức dẫn dắt cuộc chiến chống lại kẻ địch nguy hiểm, quyết tâm tiêu diệt bọn chúng.', 135, '2024-12-13', 'Kenji Kamiyama', 'Brian Cox, Gaia Wise, Luke Pasqualino, Miranda Otto,…', 'https://youtu.be/ST08liEjNsw', 'media_images_2024_10_21_screenshot-2024-10-21-140406-140455-211024-18.png', 'stopped'),
(11, 'Mufasa: Vua Sư Tử', 'Mufasa: Vua Sư Tử là phần tiền truyện của bộ phim hoạt hình Vua Sư Tử trứ danh, kể về cuộc đời của Mufasa - cha của Simba. Phim là hành trình Mufasa từ một chú sư tử mồ côi lạc đàn trở thành vị vua sư tử huyền thoại của Xứ Vua (Pride Land). Ngoài ra, quá khứ về tên phản diện Scar và hành trình hắc hóa của hắn cũng sẽ được phơi bày trong phần phim này.', 118, '2024-12-18', 'Barry Jenkins', 'Aaron Pierre, Kelvin Harrison Jr., Tiffany Boone, Kagiso Lediga,...', 'https://youtu.be/o17MF9vnabg', 'media_images_2024_11_22_screenshot-2024-11-22-134223-134308-221124-73.png', 'now_showing'),
(12, 'Kraven - Thợ Săn Thủ Lĩnh', 'Kraven the Hunter là câu chuyện đầy khốc liệt và hoành tráng về sự hình thành của một trong những phản diện biểu tượng nhất của Marvel - kẻ thù truyền kiếp của Spiderman. Aaron Taylor-Johnson đảm nhận vai Kraven, một người đàn ông có người cha mafia vô cùng tàn nhẫn, Nikolai Kravinoff (Russell Crowe) - người đã đưa anh vào con đường báo thù với những hệ quả tàn khốc. Điều này thúc đẩy anh không chỉ trở thành thợ săn vĩ đại nhất thế giới, mà còn là một trong những nhân vật đáng sợ nhất.', 127, '2024-12-13', 'J. C. Chandor', 'Aaron Taylor-Johnson, Ariana DeBose, Fred Hechinger, Alessandro Nivola, Christopher Abbott, Russell Crowe', 'https://www.youtube.com/embed/JfZYeZPyTAQ', 'media_images_2024_11_26_screenshot-2024-11-26-154107-154146-261124-78.png', 'now_showing'),
(13, 'Ngài Qủy', 'Một bác sĩ chuyên khoa tim nghi ngờ cái chết của con gái mình sau một cuộc trừ tà, tin rằng trái tim cô bé vẫn đập. Trong đám tang kéo dài ba ngày của cô bé, anh và một linh mục đã tranh cãi về sự thật, mỗi người đều cố gắng chứng minh lập trường của mình và có khả năng cứu mạng cô bé.', 94, '2024-12-13', 'Moon-Sub Hyun', 'Park Shin-yang, Lee Min-ki, Lee Re', 'https://www.youtube.com/embed/q64jojmBNVs', 'media_images_2024_12_04_400x633-18-165742-041224-23.jpg', 'now_showing'),
(14, 'Gia Đình Hoàn Hảo', 'Jae-wan là một luật sư chuyên bào chữa thành công cho những vụ án giết người. Em trai Jae-wan là một bác sĩ lương tri, luôn ưu tiên và đặt bệnh nhân lên trên lợi ích của chính mình. Bất ngờ, một sự việc nghiêm trọng giữa hai người con của hai anh em đã diễn ra và đặt ra cho họ một bài toán lương tâm về hướng giải quyết.', 104, '2024-12-13', 'Hur Jin-ho', 'Sul Kyung-gu, Jang Dong-gun, Kim Hee-ae, Claudia Kim', 'https://www.youtube.com/embed/-ZNfYLbMywQ', 'media_images_2024_12_06_400wx633h-120555-061224-87.jpg', 'now_showing'),
(15, '404 Chạy Ngay Đi', 'Nakrob, một kẻ lừa đảo bất động sản trẻ tuổi, phát hiện ra một khách sạn trên sườn đồi bị bỏ hoang gần bãi biển. Nhìn thấy cơ hội, anh ta quyết định biến nó thành một vụ lừa đảo khách sạn sang trọng.', 104, '2024-12-27', 'Pichaya Jarusboonpracha', 'Chantavit Dhanasevi, Kanyawee Songmuang, Pittaya Saechua, Chookiat Iamsook, Supathat Opas', 'https://www.youtube.com/embed/1dR-JmWBi-g', 'media_images_2024_12_13_image001-173349-131224-84.png', 'coming_soon'),
(16, 'Chị Dâu', 'Chuyện bắt đầu khi bà Nhị - con dâu cả của gia đình quyết định nhân dịp đám giỗ của mẹ chồng, tụ họp cả bốn chị em gái - con ruột trong nhà lại để thông báo chuyện sẽ tự bỏ tiền túi ra sửa sang căn nhà từ đường cũ kỹ trước khi bão về.', 100, '2024-12-20', 'Khương Ngọc', 'Ngọc Trinh, Việt Hương, Hồng Đào,...', 'https://www.youtube.com/embed/D7Omh1AQajg', 'media_images_2024_12_06_400x633-143548-061224-52.jpg', 'now_showing'),
(17, 'Kính Vạn Hoa: Bắt Đền Con Ma', 'Sau sự thành công của hai phim điện ảnh chuyển thể từ hai tác phẩm đình đám của nhà văn Nguyễn Nhật Ánh, một tác phẩm nổi bật khác của nhà văn hiện đại thành công nhất Việt Nam chuẩn bị được đưa lên màn ảnh rộng: “Kính Vạn Hoa”. Và đạo diễn dự án bom tấn này là “đạo diễn trăm tỷ” Võ Thanh Hòa.', 127, '2024-12-23', 'Võ Thanh Hòa', '.', 'https://www.youtube.com/embed/DsTUfZ0h_KM', 'media_images_2024_12_19_400x633-141437-191224-41.jpg', 'coming_soon');

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(1, 1),
(2, 5),
(3, 1),
(3, 2),
(4, 2),
(4, 3),
(5, 2),
(5, 3),
(6, 1),
(6, 4),
(7, 7),
(7, 8),
(8, 3),
(10, 2),
(10, 7),
(11, 7),
(11, 9),
(12, 5),
(13, 3),
(14, 4),
(14, 10),
(15, 1),
(15, 3),
(16, 1),
(16, 11),
(17, 1),
(17, 7);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_type` enum('ticket','snack') DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','expired','confirmed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `amount`, `status`, `created_at`, `expired_at`) VALUES
(1, 17, 270600.00, 'confirmed', '2024-12-18 10:14:19', NULL),
(2, 17, 172200.00, 'confirmed', '2024-12-18 14:57:31', NULL),
(3, 17, 262400.00, 'confirmed', '2024-12-18 15:06:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promotion_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `applies_to` enum('ticket','snack','combo') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL CHECK (`rating` between 0 and 5),
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `show_date` date DEFAULT NULL,
  `show_time` time DEFAULT NULL,
  `theater` varchar(100) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `available_seats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `movie_id`, `show_date`, `show_time`, `theater`, `seats`, `available_seats`) VALUES
(2, 1, '2024-12-11', '13:15:00', 'CINEMA Giải Phóng ', 106, 106),
(3, 1, '2024-12-12', '13:15:00', 'CINEMA Giải Phóng ', 106, 106),
(4, 1, '2024-12-11', '17:15:00', 'CINEMA Giải Phóng ', 106, 106),
(5, 1, '2024-12-11', '21:10:00', 'CINEMA Giải Phóng ', 106, 106),
(6, 1, '2024-12-18', '13:15:00', 'CINEMA Giải Phóng ', 106, 100),
(7, 6, '2024-12-18', '21:45:00', 'CINEMA Giải Phóng ', 106, 106),
(8, 6, '2024-12-17', '21:45:00', 'CINEMA Giải Phóng ', 106, 106),
(9, 1, '2024-12-19', '10:00:00', 'CINEMA Giải Phóng ', 106, 96),
(10, 1, '2024-12-19', '13:45:00', 'CINEMA Giải Phóng ', 106, 106),
(11, 1, '2024-12-19', '17:00:00', 'CINEMA Giải Phóng ', 106, 106),
(12, 6, '2024-12-19', '08:45:00', 'CINEMA Giải Phóng ', 106, 106),
(13, 6, '2024-12-19', '10:50:00', 'CINEMA Giải Phóng ', 106, 106),
(14, 12, '2024-12-19', '09:00:00', 'CINEMA Giải Phóng ', 106, 106),
(15, 16, '2024-12-20', '09:00:00', 'CINEMA Giải Phóng ', 106, 106),
(16, 16, '2024-12-20', '10:00:00', 'CINEMA Giải Phóng ', 106, 106),
(17, 16, '2024-12-20', '11:00:00', 'CINEMA Giải Phóng ', 106, 106),
(19, 16, '2024-12-20', '13:00:00', 'CINEMA Giải Phóng ', 106, 106),
(20, 16, '2024-12-20', '14:00:00', 'CINEMA Giải Phóng ', 106, 106),
(21, 16, '2024-12-20', '15:00:00', 'CINEMA Giải Phóng ', 106, 106),
(22, 16, '2024-12-20', '16:00:00', 'CINEMA Giải Phóng ', 106, 106),
(23, 16, '2024-12-20', '17:00:00', 'CINEMA Giải Phóng ', 106, 106),
(24, 16, '2024-12-20', '18:10:00', 'CINEMA Giải Phóng ', 106, 106),
(25, 16, '2024-12-20', '19:00:00', 'CINEMA Giải Phóng ', 106, 106),
(26, 16, '2024-12-20', '23:55:00', 'CINEMA Giải Phóng ', 106, 106),
(27, 6, '2024-12-20', '09:15:00', 'CINEMA Giải Phóng ', 106, 106),
(28, 6, '2024-12-20', '19:15:00', 'CINEMA Giải Phóng ', 106, 106),
(29, 6, '2024-12-20', '21:45:00', 'CINEMA Giải Phóng ', 106, 106),
(30, 7, '2024-12-20', '13:45:00', 'CINEMA Giải Phóng ', 106, 106),
(31, 7, '2024-12-20', '16:30:00', 'CINEMA Giải Phóng ', 106, 106),
(32, 11, '2024-12-20', '11:30:00', 'CINEMA Giải Phóng ', 106, 106),
(33, 11, '2024-12-20', '14:15:00', 'CINEMA Giải Phóng ', 106, 106),
(34, 11, '2024-12-20', '17:15:00', 'CINEMA Giải Phóng ', 106, 106),
(35, 11, '2024-12-21', '17:15:00', 'CINEMA Giải Phóng ', 106, 106),
(36, 11, '2024-12-21', '20:20:00', 'CINEMA Giải Phóng ', 106, 106),
(37, 11, '2024-12-21', '21:10:00', 'CINEMA Giải Phóng ', 106, 106),
(38, 11, '2024-12-21', '22:30:00', 'CINEMA Giải Phóng ', 106, 106),
(39, 12, '2024-12-20', '10:30:00', 'CINEMA Giải Phóng ', 106, 106);

-- --------------------------------------------------------

--
-- Table structure for table `snacks`
--

CREATE TABLE `snacks` (
  `snack_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` enum('popcorn','drink','combo') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `membership_level` enum('bronze','silver','gold','platinum') DEFAULT 'bronze',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_u23_confirmed` enum('yes','no') DEFAULT 'no',
  `points` int(11) DEFAULT 0,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `phone_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `membership_level`, `created_at`, `last_login`, `name`, `age`, `avatar`, `is_u23_confirmed`, `points`, `total_spent`, `phone_number`) VALUES
(1, 'admin', '', '$2y$10$w.D1V/2df1N2fkoEJ3IVJ.3USsHsssjao0utwGwiFeeH22r.ucuIe', 'admin', '', '2024-11-20 16:03:11', '2024-12-12 15:03:13', NULL, NULL, NULL, 'no', 0, 0.00, NULL),
(16, 'rudsai1', 'dien.nx215007@sis.hust.edu.vn', '$2y$10$GCn/CjD5BYPdw55eR05wwenYgXYiZTWV8pC9iLTTffviNw5.nKZk2', 'user', 'bronze', '2024-12-07 14:48:20', '2024-12-19 06:38:40', 'Nghiêm Diện', 21, NULL, 'yes', 0, 0.00, '0357904394'),
(17, 'rudsa1', 'nxd2409@gmail.com', '$2y$10$D2Ukh8m89trHxkheyjW6M.qGdzUJ2f0AV.TL724TVOCyIv6UUijES', 'user', 'bronze', '2024-12-07 14:51:30', '2024-12-19 06:37:26', 'Nghiêm Xuân Diện', 21, '/uploads/1734534235_media_images_2024_10_21_screenshot-2024-10-21-140406-140455-211024-18.png', 'yes', 0, 705200.00, '0357904394');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `membership_levels`
--
ALTER TABLE `membership_levels`
  ADD PRIMARY KEY (`level_id`),
  ADD UNIQUE KEY `level_name` (`level_name`);

--
-- Indexes for table `membership_requests`
--
ALTER TABLE `membership_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD KEY `orders_ibfk_1` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promotion_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `snacks`
--
ALTER TABLE `snacks`
  ADD PRIMARY KEY (`snack_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `membership_levels`
--
ALTER TABLE `membership_levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership_requests`
--
ALTER TABLE `membership_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `snacks`
--
ALTER TABLE `snacks`
  MODIFY `snack_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD CONSTRAINT `contact_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `membership_requests`
--
ALTER TABLE `membership_requests`
  ADD CONSTRAINT `membership_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

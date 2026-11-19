<?php

/**
 * Demo catalog of real titles. Title, year, cast and poster come from IMDb; ratings are a snapshot
 * that `php artisan imdb:sync` refreshes. Synopses are short Indonesian summaries.
 *
 * @return list<array{imdb_id: string, title: string, type: string, genres: list<string>, year: int, date: string, synopsis: string, duration: ?int, age: string, cast: list<string>, rating: float, poster: string, featured: bool, views: int}>
 */
return [
    [
        'imdb_id' => 'tt15398776', 'title' => 'Oppenheimer', 'type' => 'movie', 'genres' => ['Drama', 'Thriller'], 'year' => 2023, 'date' => '2023-07-21',
        'synopsis' => 'Kisah J. Robert Oppenheimer memimpin Proyek Manhattan dan menanggung akibat dari bom atom yang ia ciptakan.',
        'duration' => 180, 'age' => '17+', 'cast' => ['Cillian Murphy', 'Emily Blunt'], 'rating' => 8.3, 'featured' => true, 'views' => 9840,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BN2JkMDc5MGQtZjg3YS00NmFiLWIyZmQtZTJmNTM5MjVmYTQ4XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt1160419', 'title' => 'Dune', 'type' => 'movie', 'genres' => ['Sci-Fi', 'Aksi', 'Drama'], 'year' => 2021, 'date' => '2021-10-22',
        'synopsis' => 'Paul Atreides tiba di planet gurun Arrakis dan terseret perebutan rempah paling berharga di jagat raya.',
        'duration' => 155, 'age' => '13+', 'cast' => ['Timothée Chalamet', 'Rebecca Ferguson'], 'rating' => 8.0, 'featured' => true, 'views' => 8720,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNWIyNmU5MGYtZDZmNi00ZjAwLWJlYjgtZTc0ZGIxMDE4ZGYwXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0816692', 'title' => 'Interstellar', 'type' => 'movie', 'genres' => ['Sci-Fi', 'Drama'], 'year' => 2014, 'date' => '2014-11-07',
        'synopsis' => 'Sekelompok penjelajah menembus lubang cacing untuk mencari rumah baru bagi umat manusia.',
        'duration' => 169, 'age' => '13+', 'cast' => ['Matthew McConaughey', 'Anne Hathaway'], 'rating' => 8.7, 'featured' => true, 'views' => 9510,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BYzdjMDAxZGItMjI2My00ODA1LTlkNzItOWFjMDU5ZDJlYWY3XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt1375666', 'title' => 'Inception', 'type' => 'movie', 'genres' => ['Sci-Fi', 'Aksi', 'Thriller'], 'year' => 2010, 'date' => '2010-07-16',
        'synopsis' => 'Pencuri yang menyusup ke dalam mimpi mendapat tugas terakhir: menanamkan sebuah ide.',
        'duration' => 148, 'age' => '13+', 'cast' => ['Leonardo DiCaprio', 'Joseph Gordon-Levitt'], 'rating' => 8.8, 'featured' => false, 'views' => 8130,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0468569', 'title' => 'The Dark Knight', 'type' => 'movie', 'genres' => ['Aksi', 'Thriller', 'Drama'], 'year' => 2008, 'date' => '2008-07-18',
        'synopsis' => 'Batman menghadapi Joker, penjahat yang ingin menjerumuskan Gotham ke dalam kekacauan.',
        'duration' => 152, 'age' => '13+', 'cast' => ['Christian Bale', 'Heath Ledger'], 'rating' => 9.0, 'featured' => false, 'views' => 7980,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0111161', 'title' => 'The Shawshank Redemption', 'type' => 'movie', 'genres' => ['Drama'], 'year' => 1994, 'date' => '1994-10-14',
        'synopsis' => 'Bankir yang dihukum seumur hidup menjalin persahabatan di penjara sambil menyimpan harapan untuk bebas.',
        'duration' => 142, 'age' => '17+', 'cast' => ['Tim Robbins', 'Morgan Freeman'], 'rating' => 9.3, 'featured' => false, 'views' => 7420,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDAyY2FhYjctNDc5OS00MDNlLThiMGUtY2UxYWVkNGY2ZjljXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0068646', 'title' => 'The Godfather', 'type' => 'movie', 'genres' => ['Drama', 'Thriller'], 'year' => 1972, 'date' => '1972-03-24',
        'synopsis' => 'Patriark keluarga mafia Corleone menyerahkan kerajaannya kepada putra yang semula enggan terlibat.',
        'duration' => 175, 'age' => '17+', 'cast' => ['Marlon Brando', 'Al Pacino'], 'rating' => 9.2, 'featured' => false, 'views' => 6210,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNGEwYjgwOGQtYjg5ZS00Njc1LTk2ZGEtM2QwZWQ2NjdhZTE5XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt6751668', 'title' => 'Parasite', 'type' => 'movie', 'genres' => ['Drama', 'Thriller', 'Komedi'], 'year' => 2019, 'date' => '2019-05-30',
        'synopsis' => 'Keluarga miskin menyusup satu per satu ke rumah keluarga kaya, hingga sebuah rahasia terbongkar.',
        'duration' => 132, 'age' => '17+', 'cast' => ['Song Kang-ho', 'Lee Sun-kyun'], 'rating' => 8.5, 'featured' => false, 'views' => 6880,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BYjk1Y2U4MjQtY2ZiNS00OWQyLWI3MmYtZWUwNmRjYWRiNWNhXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt1899353', 'title' => 'The Raid', 'type' => 'movie', 'genres' => ['Aksi', 'Thriller'], 'year' => 2011, 'date' => '2012-03-23',
        'synopsis' => 'Tim polisi khusus terjebak di gedung apartemen milik gembong narkoba dan harus bertarung lantai demi lantai.',
        'duration' => 101, 'age' => '21+', 'cast' => ['Iko Uwais', 'Ananda George'], 'rating' => 7.6, 'featured' => false, 'views' => 5920,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BOGUxM2Q0MzItZDRmYy00ZWY1LWFiNzctNmY3Njg2ODJjZWNmXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0133093', 'title' => 'The Matrix', 'type' => 'movie', 'genres' => ['Sci-Fi', 'Aksi'], 'year' => 1999, 'date' => '1999-03-31',
        'synopsis' => 'Seorang peretas menemukan bahwa dunia yang ia kenal hanyalah simulasi yang dikendalikan mesin.',
        'duration' => 136, 'age' => '17+', 'cast' => ['Keanu Reeves', 'Laurence Fishburne'], 'rating' => 8.7, 'featured' => false, 'views' => 5470,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BN2NmN2VhMTQtMDNiOS00NDlhLTliMjgtODE2ZTY0ODQyNDRhXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt1392190', 'title' => 'Mad Max: Fury Road', 'type' => 'movie', 'genres' => ['Aksi', 'Sci-Fi'], 'year' => 2015, 'date' => '2015-05-15',
        'synopsis' => 'Di gurun pascakiamat, Max dan Furiosa melarikan diri dari tiran dengan truk perang.',
        'duration' => 120, 'age' => '17+', 'cast' => ['Tom Hardy', 'Charlize Theron'], 'rating' => 8.1, 'featured' => false, 'views' => 4560,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZDRkODJhOTgtOTc1OC00NTgzLTk4NjItNDgxZDY4YjlmNDY2XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt5052448', 'title' => 'Get Out', 'type' => 'movie', 'genres' => ['Horor', 'Thriller'], 'year' => 2017, 'date' => '2017-02-24',
        'synopsis' => 'Kunjungan ke rumah keluarga sang kekasih berubah menjadi mimpi buruk yang tak terduga.',
        'duration' => 104, 'age' => '17+', 'cast' => ['Daniel Kaluuya', 'Allison Williams'], 'rating' => 7.8, 'featured' => false, 'views' => 4310,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMjUxMDQwNjcyNl5BMl5BanBnXkFtZTgwNzcwMzc0MTI@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt3783958', 'title' => 'La La Land', 'type' => 'movie', 'genres' => ['Romansa', 'Drama', 'Komedi'], 'year' => 2016, 'date' => '2016-12-09',
        'synopsis' => 'Musisi jazz dan calon aktris jatuh cinta di Los Angeles sambil mengejar mimpi masing-masing.',
        'duration' => 128, 'age' => '13+', 'cast' => ['Ryan Gosling', 'Emma Stone'], 'rating' => 8.0, 'featured' => false, 'views' => 3890,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDllYjliOTUtMDJjZC00ODIzLWJmNGMtOWI2NzQxMjA2NzdlXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt2582802', 'title' => 'Whiplash', 'type' => 'movie', 'genres' => ['Drama'], 'year' => 2014, 'date' => '2014-10-10',
        'synopsis' => 'Drummer muda berambisi menjadi yang terbaik di bawah tekanan guru yang kejam.',
        'duration' => 106, 'age' => '17+', 'cast' => ['Miles Teller', 'J.K. Simmons'], 'rating' => 8.5, 'featured' => false, 'views' => 2970,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDFjOWFkYzktYzhhMC00NmYyLTkwY2EtYjViMDhmNzg0OGFkXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0245429', 'title' => 'Spirited Away', 'type' => 'movie', 'genres' => ['Animasi', 'Fantasi', 'Keluarga'], 'year' => 2001, 'date' => '2001-07-20',
        'synopsis' => 'Chihiro tersesat di dunia roh dan harus bekerja di pemandian ajaib untuk menyelamatkan orang tuanya.',
        'duration' => 125, 'age' => '7+', 'cast' => ['Miyu Irino', 'Rumi Hiiragi'], 'rating' => 8.6, 'featured' => false, 'views' => 6030,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNTEyNmEwOWUtYzkyOC00ZTQ4LTllZmUtMjk0Y2YwOGUzYjRiXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt2380307', 'title' => 'Coco', 'type' => 'movie', 'genres' => ['Animasi', 'Keluarga', 'Fantasi'], 'year' => 2017, 'date' => '2017-11-22',
        'synopsis' => 'Miguel yang bercita-cita jadi musisi tersesat di Negeri Orang Mati dan mencari leluhurnya.',
        'duration' => 105, 'age' => '7+', 'cast' => ['Anthony Gonzalez', 'Gael García Bernal'], 'rating' => 8.4, 'featured' => false, 'views' => 5210,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDIyM2E2NTAtMzlhNy00ZGUxLWI1NjgtZDY5MzhiMDc5NGU3XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt1049413', 'title' => 'Up', 'type' => 'movie', 'genres' => ['Animasi', 'Keluarga', 'Komedi'], 'year' => 2009, 'date' => '2009-05-29',
        'synopsis' => 'Kakek Carl menerbangkan rumahnya dengan ribuan balon, tanpa sadar membawa seorang pramuka cilik.',
        'duration' => 96, 'age' => '7+', 'cast' => ['Edward Asner', 'Jordan Nagai'], 'rating' => 8.3, 'featured' => false, 'views' => 4120,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNmI1ZTc5MWMtMDYyOS00ZDc2LTkzOTAtNjQ4NWIxNjYyNDgzXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0114709', 'title' => 'Toy Story', 'type' => 'movie', 'genres' => ['Animasi', 'Keluarga', 'Komedi'], 'year' => 1995, 'date' => '1995-11-22',
        'synopsis' => 'Woody si koboi merasa tersaingi saat mainan astronaut Buzz Lightyear datang ke kamar Andy.',
        'duration' => 81, 'age' => 'SU', 'cast' => ['Tom Hanks', 'Tim Allen'], 'rating' => 8.3, 'featured' => false, 'views' => 3560,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZTA3OWVjOWItNjE1NS00NzZiLWE1MjgtZDZhMWI1ZTlkNzYwXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0910970', 'title' => 'WALL·E', 'type' => 'movie', 'genres' => ['Animasi', 'Sci-Fi', 'Keluarga'], 'year' => 2008, 'date' => '2008-06-27',
        'synopsis' => 'Robot pembersih yang kesepian di Bumi jatuh hati pada robot penjelajah bernama EVE.',
        'duration' => 98, 'age' => 'SU', 'cast' => ['Ben Burtt', 'Elissa Knight'], 'rating' => 8.4, 'featured' => false, 'views' => 3310,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMjExMTg5OTU0NF5BMl5BanBnXkFtZTcwMjMxMzMzMw@@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0317705', 'title' => 'The Incredibles', 'type' => 'movie', 'genres' => ['Animasi', 'Aksi', 'Keluarga'], 'year' => 2004, 'date' => '2004-11-05',
        'synopsis' => 'Keluarga pahlawan super yang sedang menyamar sebagai warga biasa kembali beraksi menyelamatkan dunia.',
        'duration' => 115, 'age' => '7+', 'cast' => ['Craig T. Nelson', 'Samuel L. Jackson'], 'rating' => 8.0, 'featured' => false, 'views' => 2780,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMTY5OTU0OTc2NV5BMl5BanBnXkFtZTcwMzU4MDcyMQ@@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt11126994', 'title' => 'Arcane', 'type' => 'series', 'genres' => ['Animasi', 'Aksi', 'Fantasi'], 'year' => 2021, 'date' => '2021-11-06',
        'synopsis' => 'Dua saudari terpisah di tengah konflik kota Piltover yang megah dan Zaun yang kumuh.',
        'duration' => null, 'age' => '13+', 'cast' => ['Hailee Steinfeld', 'Kevin Alejandro'], 'rating' => 9.0, 'featured' => true, 'views' => 9120,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BYjA2NzhlMDItNWRmZC00MzRjLWE3ZjAtZjBlZDAwOWY2ODdjXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt10919420', 'title' => 'Squid Game', 'type' => 'series', 'genres' => ['Thriller', 'Drama', 'Aksi'], 'year' => 2021, 'date' => '2021-09-17',
        'synopsis' => 'Ratusan orang yang terlilit utang ikut permainan anak-anak berhadiah besar dengan taruhan nyawa.',
        'duration' => null, 'age' => '17+', 'cast' => ['Lee Jung-jae', 'Wi Ha-joon'], 'rating' => 8.0, 'featured' => true, 'views' => 8450,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDcxYjg1OTctYmUxZi00ZjhjLWIwNTMtNmI2M2Y4OTU1YWU2XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt4574334', 'title' => 'Stranger Things', 'type' => 'series', 'genres' => ['Sci-Fi', 'Horor', 'Drama'], 'year' => 2016, 'date' => '2016-07-15',
        'synopsis' => 'Hilangnya seorang anak di kota kecil Hawkins membuka rahasia eksperimen dan dunia lain.',
        'duration' => null, 'age' => '13+', 'cast' => ['Millie Bobby Brown', 'Finn Wolfhard'], 'rating' => 8.7, 'featured' => false, 'views' => 7350,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMjEzMDAxOTUyMV5BMl5BanBnXkFtZTgwNzAxMzYzOTE@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0903747', 'title' => 'Breaking Bad', 'type' => 'series', 'genres' => ['Drama', 'Thriller'], 'year' => 2008, 'date' => '2008-01-20',
        'synopsis' => 'Guru kimia yang divonis kanker beralih menjadi produsen narkoba demi masa depan keluarganya.',
        'duration' => null, 'age' => '17+', 'cast' => ['Bryan Cranston', 'Aaron Paul'], 'rating' => 9.5, 'featured' => false, 'views' => 6680,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMzU5ZGYzNmQtMTdhYy00OGRiLTg0NmQtYjVjNzliZTg1ZGE4XkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt7366338', 'title' => 'Chernobyl', 'type' => 'series', 'genres' => ['Drama', 'Thriller'], 'year' => 2019, 'date' => '2019-05-06',
        'synopsis' => 'Rekonstruksi bencana nuklir 1986 dan orang-orang yang berkorban untuk menahannya.',
        'duration' => null, 'age' => '17+', 'cast' => ['Jared Harris', 'Jessie Buckley'], 'rating' => 9.3, 'featured' => false, 'views' => 5040,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNzU0OTI4YTQtNGQ1ZS00ZjA4LTg3MTMtZjkyZWNjN2RiZDJmXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0386676', 'title' => 'The Office', 'type' => 'series', 'genres' => ['Komedi'], 'year' => 2005, 'date' => '2005-03-24',
        'synopsis' => 'Keseharian para karyawan kantor penjual kertas di Scranton bersama bos yang canggung.',
        'duration' => null, 'age' => '13+', 'cast' => ['Steve Carell', 'Jenna Fischer'], 'rating' => 9.0, 'featured' => false, 'views' => 4470,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZjQwYzBlYzUtZjhhOS00ZDQ0LWE0NzAtYTk4MjgzZTNkZWEzXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt0417299', 'title' => 'Avatar: The Last Airbender', 'type' => 'series', 'genres' => ['Animasi', 'Fantasi', 'Aksi'], 'year' => 2005, 'date' => '2005-02-21',
        'synopsis' => 'Aang, Avatar terakhir, harus menguasai empat elemen untuk menghentikan Negara Api.',
        'duration' => null, 'age' => '7+', 'cast' => ['Zach Tyler Eisen', 'Dee Bradley Baker'], 'rating' => 9.3, 'featured' => false, 'views' => 3980,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDMwMThjYWYtY2Q2OS00OGM2LTlkODQtNDJlZTZmMjAyYmFhXkEyXkFqcGc@._V1_.jpg',
    ],
    [
        'imdb_id' => 'tt7678620', 'title' => 'Bluey', 'type' => 'series', 'genres' => ['Animasi', 'Keluarga', 'Komedi'], 'year' => 2018, 'date' => '2018-10-01',
        'synopsis' => 'Bluey, anak anjing yang penuh imajinasi, mengubah hari-hari biasa menjadi permainan seru bersama keluarganya.',
        'duration' => null, 'age' => 'SU', 'cast' => ['David McCormack', 'Melanie Zanetti'], 'rating' => 9.3, 'featured' => false, 'views' => 3120,
        'poster' => 'https://m.media-amazon.com/images/M/MV5BYWU1YmQzMjEtMDNjOS00MGIyLWExY2ItZDAzNmU5NWViMGZmXkEyXkFqcGc@._V1_.jpg',
    ],
];

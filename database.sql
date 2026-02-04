
CREATE TABLE people(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100),
 division ENUM('Worship','Multimedia','Usher')
);

CREATE TABLE assign(
 id INT AUTO_INCREMENT PRIMARY KEY,
 week INT,
 role VARCHAR(100),
 person_id INT
);

CREATE TABLE songs(
 id INT AUTO_INCREMENT PRIMARY KEY,
 week INT,
 title VARCHAR(150),
 youtube TEXT,
 sequencer TEXT,
 lyrics TEXT,
 ordering INT
);

CREATE TABLE outfits(
 id INT AUTO_INCREMENT PRIMARY KEY,
 week INT,
 filename VARCHAR(200)
);

INSERT INTO people(name,division) VALUES
('Andre','Worship'),('Maria','Worship'),('Riko','Worship'),
('Sinta','Multimedia'),('Dewi','Multimedia'),
('Yohan','Usher'),('Anton','Usher');
CREATE TABLE Performers
(
    `performer_id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name`         VARCHAR(255) NOT NULL,
    `birth_date`   DATE
) ENGINE = InnoDB;


CREATE TABLE Movies
(
    `movie_id`     INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title`        VARCHAR(255) NOT NULL,
    `release_date` DATE
) ENGINE = InnoDB;


CREATE TABLE LookupCast
(
    `movie_id`     INT UNSIGNED NOT NULL,
    `performer_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `LookupCast-Movies` FOREIGN KEY (`movie_id`) REFERENCES Movies (`movie_id`),
    CONSTRAINT `LookupCast-Performers` FOREIGN KEY (`performer_id`) REFERENCES Performers (`performer_id`)
);



INSERT INTO Movies (title, release_date)
VALUES ('Demo The Movie', '2001-01-01'),
       ('The Demo Movie', '2002-02-02'),
       ('Movie Demo', '2005-03-03'),
       ('Redacted', '2006-06-06');


INSERT INTO Performers (name, birth_date)
VALUES ('JOHN DOE', '1950-01-01'),
       ('JANE DOE', '1985-02-02'),
       ('SARA JANE', '1990-03-03'),
       ('THE ROCK', '1975-04-04');




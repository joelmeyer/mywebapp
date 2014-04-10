CREATE TABLE students(
	last_name varchar(30) NOT NULL,
	first_name varchar(30) NOT NULL,
	student_id int(10) NOT NULL,
	username varchar(30) NOT NULL,
    class_id int(10) NOT NULL,
	PRIMARY KEY (student_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id),
	UNIQUE (username),
	INDEX(last_name, first_name)    );

CREATE TABLE locations(
	loc_name varchar(30) NOT NULL,
	loc_id int(10) NOT NULL,
	PRIMARY KEY (loc_id),
	UNIQUE (loc_name)       );

CREATE TABLE stu_loc_m2m( 
	id int(10) NOT NULL AUTO_INCREMENT,
	student_id int(10),
	loc_id int(10) ,
	PRIMARY KEY (id),
	FOREIGN KEY (student_id) REFERENCES students(student_id),
	FOREIGN KEY (loc_id) REFERENCES locations(loc_id)	);

CREATE TABLE classes(
    class_name varchar(30) NOT NULL,
    class_id int(10) NOT NULL,
    PRIMARY KEY (class_id)
    UNIQUE (class_name)     );

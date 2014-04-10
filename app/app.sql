CREATE TABLE users(
	last_name varchar(30) NOT NULL,
	first_name varchar(30) NOT NULL,
	user_id int(10) NOT NULL,
	username varchar(30) NOT NULL,
	PRIMARY KEY (user_id),
	UNIQUE (username),
	INDEX(last_name, first_name)    );

CREATE TABLE events(
	event_name varchar(30) NOT NULL,
	event_id int(10) NOT NULL,
	event_time DATETIME,
    event_descript varchar(250),
    event_location varchar(50),
    food boolean default 0,
    user_id int(10) NOT NULL,
    PRIMARY KEY (event_id), 
    FOREIGN KEY (user_id) REFERENCES users(user_id)  );

CREATE TABLE attendence( 
	id int(10) NOT NULL AUTO_INCREMENT,
	user_id int(10),
	event_id int(10) ,
	PRIMARY KEY (id),
	FOREIGN KEY (user_id) REFERENCES users(user_id),
	FOREIGN KEY (event_id) REFERENCES events(event_id)	);


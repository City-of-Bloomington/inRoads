-- @copyright 2014-2015 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	email varchar(255) not null,
	username varchar(40) unique,
	password varchar(40),
	authenticationMethod varchar(40),
	role varchar(30)
);

create table events (
    id int unsigned not null primary key auto_increment,
    eventType varchar(32) not null,
    created      datetime not null,
    updated      datetime not null,
    startDate    date     not null,
    endDate      date     not null,
    description text,
    geography             geometry,
    geography_description varchar(128)
);

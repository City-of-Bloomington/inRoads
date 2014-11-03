-- @copyright 2014 City of Bloomington, Indiana
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

create table jurisdictions (
    id int unsigned not null primary key auto_increment,
    domain varchar(128) not null,
    name   varchar(128) not null,
    email  varchar(128) not null,
    phone  varchar(32),
    description text
);

create table events (
    id int unsigned not null primary key auto_increment,
    jurisdiction_id int unsigned not null,
    eventType varchar(32) not null,
    severity  varchar(32) not null,
    status    varchar(32) not null,
    created      datetime not null,
    updated      datetime not null,
    headline varchar(255) not null,
    description text,
    detour      text,
    geography geometry,
    foreign key (jurisdiction_id) references jurisdictions(id)
);

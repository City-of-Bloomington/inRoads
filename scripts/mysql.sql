-- @copyright 2014-2015 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table departments (
    id int unsigned    not null primary key auto_increment,
    code  varchar(8)   not null unique,
    name  varchar(128) not null unique,
    phone varchar(16)
);

create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname  varchar(128) not null,
	email     varchar(255) not null,
	phone     varchar(16),
	username  varchar(40) unique,
	password  varchar(40),
	authenticationMethod varchar(40),
	role varchar(30),
	department_id int unsigned,
	foreign key (department_id) references departments(id)
);

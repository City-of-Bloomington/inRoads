-- @copyright 2014-2018 City of Bloomington, Indiana
-- @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
create table departments (
    id int unsigned    not null primary key auto_increment,
    code  varchar(8)   not null unique,
    name  varchar(128) not null unique,
    phone varchar(16)
);

create table people (
	id                   int unsigned not null primary key auto_increment,
	department_id        int unsigned,
	firstname            varchar(128) not null,
	lastname             varchar(128) not null,
	email                varchar(255) unique,
	phone                varchar(16),
	username             varchar(40) unique,
	password             varchar(40),
	authenticationMethod varchar(40),
	role                 varchar(30),
	notify_updates       boolean,
	notify_emergency     boolean,
	foreign key (department_id) references departments(id)
);

create table eventTypes (
    id int unsigned   not null primary key auto_increment,
    code varchar(128) not null unique,
    name varchar(128) not null,
    description varchar(128),
    color       varchar(6),
    isDefault   boolean,
    sortingNumber tinyint unsigned,
    cifsType varchar(128)
);

create table events (
    id            int unsigned not null primary key auto_increment,
    department_id int unsigned not null,
    eventType_id  int unsigned not null,
    google_event_id varchar(32) unique,
    title           varchar(128),
    primaryContact  varchar(128),
    description     text,
    constructionFlag boolean not null default 1,
    startDate date not null,
    endDate   date not null,
    startTime time,
    endTime   time,
    rrule varchar(128),
    geography geometry,
    geography_description varchar(128),
    created datetime not null,
    updated datetime not null,
    foreign key (department_id) references departments(id),
    foreign key ( eventType_id) references  eventTypes(id)
);

create table eventHistory (
    id          int unsigned not null primary key auto_increment,
    event_id    int unsigned not null,
    person_id   int unsigned not null,
    action_date timestamp    not null default CURRENT_TIMESTAMP,
    action      varchar(32)  not null,
    changes     text         not null,
    foreign key (event_id ) references events(id),
    foreign key (person_id) references people(id)
);

create table segments (
    id int unsigned not null primary key auto_increment,
    event_id int unsigned not null,
    street     varchar(128) not null,
    streetFrom varchar(128) not null,
    streetTo   varchar(128) not null,
    direction  varchar(8)   not null,
    startLatitude  float(11, 8),
    startLongitude float(11, 8),
    endLatitude    float(11, 8),
    endLongitude   float(11, 8),
    foreign key (event_id) references events(id)
);

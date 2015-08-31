alter table people add phone varchar(16) after email;

create table eventTypes (
    id int unsigned   not null primary key auto_increment,
    code varchar(128) not null unique,
    name varchar(128) not null,
    description varchar(128),
    color       varchar(6),
    isDefault   boolean
);

create table events (
    id            int unsigned not null primary key auto_increment,
    department_id int unsigned not null,
    eventType_id  int unsigned not null,
    google_event_id varchar(32) unique,
    description     varchar(255),
    startDate date not null,
    endDate   date not null,
    startTime time,
    endTime   time,
    rrule varchar(128),
    geography geometry,
    geography_description varchar(128),
    created datetime not null,
    updated datetime not null,
    foreign key (department_id) references departments(id)
);

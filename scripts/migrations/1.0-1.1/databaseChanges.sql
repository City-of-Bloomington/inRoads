alter table people add phone varchar(16) after email;

create table events (
    id            int unsigned not null primary key auto_increment,
    department_id int unsigned not null,
    google_event_id varchar(32) unique,
    eventType       varchar(32) not null,
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

delete from people where firstname='';

alter table events     add foreign key (eventType_id) references eventTypes(id);
alter table events     add primaryContact   varchar(128) after title;
alter table eventTypes add cifsType         varchar(128);
alter table people  modify email            varchar(255) unique;

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

create table notificationEmails (
    id    int unsigned not null primary key auto_increment,
    type  varchar(16)  not null,
    email varchar(128) not null,
    unique(type, email)
);

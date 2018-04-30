delete from people where firstname='';

alter table events     add foreign key (eventType_id) references eventTypes(id);
alter table eventTypes add cifsType      varchar(128);
alter table people     add notifications boolean;
alter table people  modify email         varchar(255) unique;

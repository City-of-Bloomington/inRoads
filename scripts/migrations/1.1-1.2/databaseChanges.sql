alter table eventTypes add cifsType varchar(128);
alter table events add foreign key (eventType_id) references eventTypes(id);

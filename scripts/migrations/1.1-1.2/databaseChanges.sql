delete from people where firstname='';

alter table events     add foreign key (eventType_id) references eventTypes(id);
alter table events     add primaryContact   varchar(128) after title;
alter table eventTypes add cifsType         varchar(128);
alter table people     add notify_updates   boolean;
alter table people     add notify_emergency boolean;
alter table people  modify email            varchar(255) unique;

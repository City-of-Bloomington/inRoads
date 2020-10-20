alter table events add temp geometry SRID 4326 after geography;
update events set temp=ST_GeomFromText(ST_AsText(geography), 4326);
alter table events drop geography;
alter table events rename column temp to geography;

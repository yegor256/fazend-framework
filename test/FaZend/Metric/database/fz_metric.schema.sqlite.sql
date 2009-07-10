create table fz_metric (
    id integer not null primary key autoincrement, 
    method varchar(255) not null, 
    params text, 
    value text, 
    msec integer not null, 
    updated timestamp default current_timestamp 
);